<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SubCategory::with('category');
        
        // Apply category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        // Apply sorting
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortField, ['name', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection === 'desc' ? 'desc' : 'asc');
        }
        
        // Get paginated results
        $subcategories = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();
        
        return view('admin.subcategories.index', compact('subcategories', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.subcategories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $subcategory = new SubCategory([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'category_id' => $request->category_id,
            'icon' => $request->icon,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        $subcategory->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Subcategory '{$subcategory->name}' created successfully."
            ]);
        }

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategory created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subcategory = SubCategory::with('category')->findOrFail($id);
        // return view('admin.subcategories.show', compact('subcategory'));
         return response()->json([
            'success' => true,
            'subcategory' => $subcategory
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $subcategory = SubCategory::findOrFail($id);
            $categories = Category::all();
            
            if (request()->ajax() || request()->wantsJson()) {
                // For AJAX requests, return JSON with the rendered form HTML
                return response()->json([
                    'success' => true,
                    'html' => view('admin.subcategories.edit-form', compact('subcategory', 'categories'))->render(),
                    'subcategory' => $subcategory
                ]);
            }
            
            // For regular requests, return the full edit page view
            return view('admin.subcategories.edit', compact('subcategory', 'categories'));
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error in subcategory edit: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading subcategory: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.subcategories.index')
                ->with('error', 'Error loading subcategory: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'icon' => 'nullable|string|max:255',
                'description' => 'nullable|string',
            ]);

            $subcategory = SubCategory::findOrFail($id);
            
            $subcategory->name = $request->name;
            $subcategory->slug = Str::slug($request->name);
            $subcategory->category_id = $request->category_id;
            $subcategory->icon = $request->icon;
            $subcategory->description = $request->description;
            $subcategory->is_active = $request->has('is_active');
            
            $subcategory->save();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Subcategory '{$subcategory->name}' updated successfully."
                ]);
            }
            
            return redirect()->route('admin.subcategories.index')
                ->with('success', 'Subcategory updated successfully.');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error updating subcategory: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating subcategory: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.subcategories.index')
                ->with('error', 'Error updating subcategory: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $subcategory = SubCategory::findOrFail($id);
            $subcategoryName = $subcategory->name;
            $subcategory->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Subcategory '{$subcategoryName}' deleted successfully."
                ]);
            }
            
            return redirect()->route('admin.subcategories.index')
                ->with('success', 'Subcategory deleted successfully.');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error deleting subcategory: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting subcategory: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.subcategories.index')
                ->with('error', 'Error deleting subcategory: ' . $e->getMessage());
        }
    }
}
