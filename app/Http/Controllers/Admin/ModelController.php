<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Model;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class ModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Model::with(['brand', 'category']);
        
        // Apply filters if provided
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == 'true' ? 1 : 0);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }
        
        $models = $query->latest()->paginate(15);
        
        // Get filter options for the form
        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        
        return view('admin.models.index', compact('models', 'brands', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brand::orderBy('name')->get();
        $categories = collect(); // Empty collection initially
        
        return view('admin.models.create', compact('brands', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'specifications' => 'nullable|array',
            'specifications.*' => 'string|max:255',
        ]);
        
        // Format features and specifications
        $features = !empty($request->features) ? $request->features : [];
        $specifications = !empty($request->specifications) ? $request->specifications : [];
        
        // Ensure is_active is set
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }
        
        try {
            // Create model
            $model = Model::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'brand_id' => $validated['brand_id'],
                'category_id' => $validated['category_id'],
                'subcategory_id' => null, // Set subcategory_id to null
                'is_active' => $validated['is_active'],
                'features' => $features,
                'specifications' => $specifications,
            ]);
            
            return redirect()->route('admin.models.index')
                ->with('success', 'Model created successfully.');
        } catch (\Exception $e) {
            \Log::error('Error creating model: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error creating model: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Model $model)
    {
        return view('admin.models.show', compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Model $model)
    {
        $brands = Brand::orderBy('name')->get();
        $categories = Category::where('brand_id', $model->brand_id)->orderBy('name')->get();
        
        // Check if this is an AJAX request (for modal form)
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'model' => $model,
                'brands' => $brands,
                'categories' => $categories
            ]);
        }
        
        return view('admin.models.edit', compact('model', 'brands', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Model $model)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'specifications' => 'nullable|array',
            'specifications.*' => 'string|max:255',
        ]);
        
        // Format features and specifications
        $features = !empty($request->features) ? $request->features : [];
        $specifications = !empty($request->specifications) ? $request->specifications : [];
        
        // Ensure is_active is set
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }
        
        try {
            // Update model
            $model->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'brand_id' => $validated['brand_id'],
                'category_id' => $validated['category_id'],
                'subcategory_id' => null, // Set subcategory_id to null
                'is_active' => $validated['is_active'],
                'features' => $features,
                'specifications' => $specifications,
            ]);
            
            return redirect()->route('admin.models.index')
                ->with('success', 'Model updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating model: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error updating model: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Model $model)
    {
        try {
            $model->delete();
            return redirect()->route('admin.models.index')
                ->with('success', 'Model deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting model: ' . $e->getMessage());
            return back()->with('error', 'Error deleting model: ' . $e->getMessage());
        }
    }
    
    /**
     * Get categories based on brand
     */
    public function getCategoriesByBrand(Request $request)
    {
        try {
            $brand_id = null;
            
            // Check if we have brand ID directly
            if ($request->has('brand_id')) {
                $brand_id = $request->input('brand_id');
            } 
            // Otherwise, check if we have a brand name
            elseif ($request->has('brand')) {
                $brandName = $request->input('brand');
                $brand = Brand::where('name', $brandName)->first();
                if ($brand) {
                    $brand_id = $brand->id;
                }
            }
            
            if (!$brand_id) {
                // If no brand was found, return all categories
                $categories = Category::orderBy('name')->get();
            } else {
                // Filter categories by brand_id if we found a matching brand
                $categories = Category::where('brand_id', $brand_id)->orderBy('name')->get();
                
                // If no categories assigned to this brand, return all categories as fallback
                if ($categories->isEmpty()) {
                    $categories = Category::orderBy('name')->get();
                }
            }
            
            return response()->json([
                'success' => true,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching categories: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching categories: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get models based on category
     */
    public function getModelsByCategory($category_id)
    {
        try {
            $models = Model::where('category_id', $category_id)
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->get();
                        
            return response()->json([
                'success' => true,
                'models' => $models
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching models by category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching models: ' . $e->getMessage()
            ], 500);
        }
    }
} 