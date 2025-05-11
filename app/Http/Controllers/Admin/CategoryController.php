<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::with('brand');
        
        // Apply search filters
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('brand', function($brandQuery) use ($searchTerm) {
                      $brandQuery->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }
        
        // Apply sorting
        $sortField = $request->sort ?? 'name';
        $sortDirection = $request->direction ?? 'asc';
        
        if ($sortField === 'brand_id') {
            $query->leftJoin('brands', 'categories.brand_id', '=', 'brands.id')
                  ->select('categories.*')
                  ->orderBy('brands.name', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }
        
        $categories = $query->paginate(15)->withQueryString();
        
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brand::orderBy('name')->get();
        return view('admin.categories.create', compact('brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'brand_id' => 'nullable|exists:brands,id',
            'brand_featured' => 'boolean',
        ]);
        
        // If slug is not provided, generate it from name
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Handle boolean inputs that might not be present
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');
        $validated['brand_featured'] = $request->has('brand_featured');
        
        $category = Category::create($validated);
        
        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$category->name}' created successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::with('brand')->findOrFail($id);
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        $brands = Brand::orderBy('name')->get();
        return view('admin.categories.edit', compact('category', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'brand_id' => 'nullable|exists:brands,id',
            'brand_featured' => 'boolean',
        ]);
        
        // If slug is not provided, generate it from name
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Handle boolean inputs that might not be present
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');
        $validated['brand_featured'] = $request->has('brand_featured');
        
        $category->update($validated);
        
        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$category->name}' updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        
        // Check if category has products
        if ($category->products()->exists()) {
            return back()->with('error', "Cannot delete category '{$category->name}' because it has associated products.");
        }
        
        $categoryName = $category->name;
        $category->delete();
        
        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$categoryName}' deleted successfully!");
    }
}
