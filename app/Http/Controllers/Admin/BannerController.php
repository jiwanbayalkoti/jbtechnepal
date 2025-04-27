<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    /**
     * Display a listing of the banners.
     */
    public function index(Request $request)
    {
        $query = Banner::query();
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%");
            });
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        // Apply sorting
        $sortField = $request->get('sort', 'order');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortField, ['title', 'order', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection === 'desc' ? 'desc' : 'asc');
        }
        
        // Get paginated results
        $banners = $query->paginate(10)->withQueryString();
        
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new banner.
     */
    public function create()
    {
        $pages = Page::all();
        return view('admin.banners.create', compact('pages'));
    }

    /**
     * Store a newly created banner in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
            'order' => 'nullable|integer|min:0',
            // 'is_active' => 'boolean',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Set default order if not provided
        $validated['order'] = $validated['order'] ?? 0;
        
        // Set is_active based on checkbox presence
        $validated['is_active'] = $request->has('is_active');
        
        // Create banner
        $banner = Banner::create($validated);
        
        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $imagePath = $image->store('banners', 'public');
                
                // First image is primary by default
                $isPrimary = ($index === 0);
                
                $banner->images()->create([
                    'image_path' => $imagePath,
                    'display_order' => $index,
                    'is_primary' => $isPrimary
                ]);
            }
        }
        
        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner created successfully.');
    }

    /**
     * Display the specified banner.
     */
    public function show(Banner $banner)
    {
        return view('admin.banners.show', compact('banner'));
    }

    /**
     * Show the form for editing the specified banner.
     */
    public function edit(Banner $banner)
    {
        $pages = Page::all();
        return view('admin.banners.edit', compact('banner', 'pages'));
    }

    /**
     * Update the specified banner in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
            'order' => 'nullable|integer|min:0',
            // 'is_active' => 'boolean',
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'primary_image_id' => 'nullable|exists:banner_images,id',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:banner_images,id',
        ]);
        
        // Set default order if not provided
        $validated['order'] = $validated['order'] ?? 0;
        
        // Set is_active based on checkbox presence
        $validated['is_active'] = $request->has('is_active');
        
        // Update banner
        $banner->update($validated);
        
        // Set primary image
        if ($request->has('primary_image_id')) {
            // Reset all images to non-primary
            $banner->images()->update(['is_primary' => false]);
            
            // Set selected image as primary
            $banner->images()->where('id', $request->primary_image_id)->update(['is_primary' => true]);
        }
        
        // Delete images if requested
        if ($request->has('delete_images')) {
            $imagesToDelete = $banner->images()->whereIn('id', $request->delete_images)->get();
            
            foreach ($imagesToDelete as $image) {
                // Delete file from storage
                if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
                
                // Delete record from database
                $image->delete();
            }
        }
        
        // Handle new image uploads
        if ($request->hasFile('new_images')) {
            // Get the highest current display order
            $maxOrder = $banner->images()->max('display_order') ?? -1;
            
            foreach ($request->file('new_images') as $index => $image) {
                $imagePath = $image->store('banners', 'public');
                
                // Set as primary if there are no other images
                $isPrimary = false;
                if ($banner->images()->count() === 0) {
                    $isPrimary = true;
                }
                
                $banner->images()->create([
                    'image_path' => $imagePath,
                    'display_order' => $maxOrder + $index + 1,
                    'is_primary' => $isPrimary
                ]);
            }
        }
        
        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner updated successfully.');
    }

    /**
     * Remove the specified banner from storage.
     */
    public function destroy(Banner $banner)
    {
        try {
            // Delete all associated images
            foreach ($banner->images as $image) {
                if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
            }
            
            // Delete old direct image if exists
            if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
                Storage::disk('public')->delete($banner->image_path);
            }
            
            $banner->delete();
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Banner deleted successfully.']);
            }
            
            return redirect()->route('admin.banners.index')
                ->with('success', 'Banner deleted successfully.');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error deleting banner.'], 500);
            }
            
            return redirect()->route('admin.banners.index')
                ->with('error', 'Error deleting banner.');
        }
    }
    
    /**
     * Update the order of banners.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'banners' => 'required|array',
            'banners.*.id' => 'required|exists:banners,id',
            'banners.*.order' => 'required|integer|min:0',
        ]);
        
        foreach ($request->banners as $bannerData) {
            Banner::where('id', $bannerData['id'])->update([
                'order' => $bannerData['order']
            ]);
        }
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Toggle the active status of a banner.
     */
    public function toggleStatus(Banner $banner)
    {
        $banner->is_active = !$banner->is_active;
        $banner->save();
        
        return redirect()->back()->with('success', 'Banner status updated successfully.');
    }

    /**
     * Display a demonstration page with banner usage examples.
     *
     * @return \Illuminate\View\View
     */
    public function demo()
    {
        return view('admin.banners.demo');
    }
}
