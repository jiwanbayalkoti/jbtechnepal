<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdvertisementController extends Controller
{
    /**
     * Display a listing of advertisements.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Advertisement::query();
        
        // Apply filters
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('position')) {
            $query->where('position', $request->position);
        }
        
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'scheduled') {
                $query->where('start_date', '>', now());
            } elseif ($request->status === 'expired') {
                $query->where('end_date', '<', now())
                      ->where('end_date', '!=', null);
            }
        }
        
        $advertisements = $query->orderBy('display_order', 'asc')
                              ->paginate(10)
                              ->withQueryString();
        
        // Get counts for dashboard
        $counts = [
            'total' => Advertisement::count(),
            'active' => Advertisement::where('is_active', true)->count(),
            'inactive' => Advertisement::where('is_active', false)->count(),
            'scheduled' => Advertisement::where('start_date', '>', now())->count(),
            'expired' => Advertisement::where('end_date', '<', now())->where('end_date', '!=', null)->count(),
        ];
        
        // Get available positions
        $positions = Advertisement::distinct()->pluck('position')->toArray();
        
        return view('admin.advertisements.index', compact('advertisements', 'counts', 'positions'));
    }

    /**
     * Show the form for creating a new advertisement.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.advertisements.create');
    }

    /**
     * Store a newly created advertisement in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'required|string|max:50',
            'url' => 'nullable|url|max:255',
            'content' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048', // 2MB max
        ]);
        
        // Process image if uploaded
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('advertisements', 'public');
            $validated['image'] = $imagePath;
        }
        
        // Set default values if not provided
        $validated['display_order'] = $validated['display_order'] ?? 0;
        $validated['is_active'] = $request->has('is_active');
        $validated['views'] = 0;
        $validated['clicks'] = 0;
        
        Advertisement::create($validated);
        
        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement created successfully.');
    }

    /**
     * Display the specified advertisement.
     *
     * @param  \App\Models\Advertisement  $advertisement
     * @return \Illuminate\Http\Response
     */
    public function show(Advertisement $advertisement)
    {
        return view('admin.advertisements.show', compact('advertisement'));
    }

    /**
     * Show the form for editing the specified advertisement.
     *
     * @param  \App\Models\Advertisement  $advertisement
     * @return \Illuminate\Http\Response
     */
    public function edit(Advertisement $advertisement)
    {
        return view('admin.advertisements.edit', compact('advertisement'));
    }

    /**
     * Update the specified advertisement in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Advertisement  $advertisement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Advertisement $advertisement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'required|string|max:50',
            'url' => 'nullable|url|max:255',
            'content' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048', // 2MB max
        ]);
        
        // Process image if uploaded
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($advertisement->image) {
                Storage::disk('public')->delete($advertisement->image);
            }
            
            $imagePath = $request->file('image')->store('advertisements', 'public');
            $validated['image'] = $imagePath;
        }
        
        // Set is_active based on checkbox
        $validated['is_active'] = $request->has('is_active');
        
        $advertisement->update($validated);
        
        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement updated successfully.');
    }

    /**
     * Remove the specified advertisement from storage.
     *
     * @param  \App\Models\Advertisement  $advertisement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Advertisement $advertisement)
    {
        // Delete image if exists
        if ($advertisement->image) {
            Storage::disk('public')->delete($advertisement->image);
        }
        
        $advertisement->delete();
        
        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement deleted successfully.');
    }
    
    /**
     * Update the display order of advertisements.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'advertisements' => 'required|array',
            'advertisements.*.id' => 'required|exists:advertisements,id',
            'advertisements.*.order' => 'required|integer|min:0',
        ]);
        
        foreach ($request->advertisements as $adData) {
            Advertisement::where('id', $adData['id'])->update([
                'display_order' => $adData['order']
            ]);
        }
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Toggle the active status of an advertisement.
     */
    public function toggleStatus(Advertisement $advertisement)
    {
        $advertisement->is_active = !$advertisement->is_active;
        $advertisement->save();
        
        return redirect()->back()->with('success', 'Advertisement status updated successfully.');
    }

    /**
     * Track a click on an advertisement.
     *
     * @param  \App\Models\Advertisement  $advertisement
     * @return \Illuminate\Http\Response
     */
    public function trackClick(Advertisement $advertisement)
    {
        $advertisement->increment('clicks');
        
        return redirect($advertisement->url);
    }

    /**
     * Show statistics for all advertisements.
     */
    public function statistics()
    {
        $advertisements = Advertisement::orderByDesc('views')->get();
        
        // Calculate total statistics
        $totalViews = $advertisements->sum('views');
        $totalClicks = $advertisements->sum('clicks');
        $avgCtr = $totalViews > 0 ? number_format(($totalClicks / $totalViews) * 100, 2) : '0.00';
        $activeAdsCount = Advertisement::where('is_active', true)->count();
        
        // Calculate CTR for each advertisement
        foreach ($advertisements as $ad) {
            $ad->ctr = $ad->views > 0 ? ($ad->clicks / $ad->views) * 100 : 0;
        }
        
        // Get top performing ads by CTR (with minimum views)
        $topAds = $advertisements->filter(function($ad) {
            return $ad->views >= 10; // Only ads with at least 10 views
        })->sortByDesc('ctr')->take(10);
        
        // Calculate stats by position
        $positionStats = $advertisements->groupBy('position')
            ->map(function($ads, $position) {
                $totalViews = $ads->sum('views');
                $totalClicks = $ads->sum('clicks');
                $ctr = $totalViews > 0 ? ($totalClicks / $totalViews) * 100 : 0;
                
                return [
                    'position' => $position,
                    'position_name' => (new Advertisement(['position' => $position]))->getPositionNameAttribute(),
                    'views' => $totalViews,
                    'clicks' => $totalClicks,
                    'ctr' => $ctr
                ];
            })->values();
        
        return view('admin.advertisements.statistics', compact(
            'advertisements', 
            'totalViews', 
            'totalClicks', 
            'avgCtr', 
            'activeAdsCount',
            'topAds',
            'positionStats'
        ));
    }

    public function recordClick($id)
    {
        $ad = Advertisement::findOrFail($id);
        $ad->clicks += 1;
        $ad->save();
        
        return redirect()->away($ad->url);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);
        
        $ad = Advertisement::findOrFail($id);
        $ad->is_active = $request->is_active;
        $ad->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Advertisement status updated successfully'
        ]);
    }

    /**
     * Export advertisements to CSV.
     */
    public function export()
    {
        $advertisements = Advertisement::orderBy('display_order', 'asc')->get();
        
        $filename = 'advertisements_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($advertisements) {
            $file = fopen('php://output', 'w');
            
            // Add CSV header
            fputcsv($file, [
                'ID',
                'Title',
                'URL',
                'Position',
                'Display Order',
                'Start Date',
                'End Date',
                'Status',
                'Views',
                'Clicks',
                'CTR (%)',
                'Created',
                'Updated'
            ]);
            
            // Add data rows
            foreach ($advertisements as $ad) {
                $ctr = $ad->views > 0 ? ($ad->clicks / $ad->views) * 100 : 0;
                
                fputcsv($file, [
                    $ad->id,
                    $ad->title,
                    $ad->url,
                    $ad->position,
                    $ad->display_order,
                    $ad->start_date ? $ad->start_date->format('Y-m-d') : 'N/A',
                    $ad->end_date ? $ad->end_date->format('Y-m-d') : 'Indefinite',
                    $ad->is_active ? 'Active' : 'Inactive',
                    $ad->views,
                    $ad->clicks,
                    number_format($ctr, 2),
                    $ad->created_at->format('Y-m-d H:i:s'),
                    $ad->updated_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Display the test page for advertisements.
     *
     * @return \Illuminate\Http\Response
     */
    public function test()
    {
        return view('admin.advertisements.test');
    }
}
