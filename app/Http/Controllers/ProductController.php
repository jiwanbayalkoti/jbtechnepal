<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'subcategory', 'images']);

        // Apply category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
            
            // Load subcategories for the selected category
            $subcategories = \App\Models\SubCategory::where('category_id', $request->category)
                ->orderBy('name')
                ->get();
        } else {
            $subcategories = collect();
        }

        // Apply subcategory filter
        if ($request->filled('subcategory')) {
            $query->where('subcategory_id', $request->subcategory);
        }

        // Apply brand filter - brand is a field, not a relationship
        if ($request->filled('brand')) {
            $query->where('brand', 'like', "%{$request->brand}%");
        }

        // Apply price range filter
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Get all products with pagination
        $products = $query->latest()->paginate(15);
        
        // Get all categories for the filter dropdown
        $categories = Category::orderBy('name')->get();
        
        // Get unique brands from the products table for the filter dropdown
        $brands = Product::select('brand')
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand');

        return view('admin.products.index', compact('products', 'categories', 'brands', 'subcategories'));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Create the product
        $product = new Product([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'subcategory_id' => $validated['subcategory_id'],
                'brand' => $validated['brand'],
                'model' => $validated['model'],
                'price' => $validated['price'],
                'description' => $validated['description'],
                'slug' => Str::slug($validated['name'])
            ]);

            // Log before saving for debugging
            \Log::info('Attempting to save product with data:', [
                'request_data' => $request->all(),
                'validated_data' => $validated,
                'product_data' => $product->toArray()
            ]);

        $product->save();

            // Log after saving for debugging
            \Log::info('Product saved with ID: ' . $product->id);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');
                    $product->images()->create([
                        'path' => $path,
                        'is_primary' => $index === 0 // Make first image primary
                    ]);
                    \Log::info('Image saved for product', ['product_id' => $product->id, 'path' => $path]);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product created successfully',
                    'product_id' => $product->id
                ]);
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully with ID: ' . $product->id);
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error creating product: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating product: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error creating product: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        // Load related data
        $product->load(['category', 'subcategory', 'images', 'specifications.specificationType']);
        
        return view('admin.products.show', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:product_images,id'
        ]);

        try {
            DB::beginTransaction();

            // Handle image deletions
            if ($request->has('delete_images')) {
                foreach ($request->delete_images as $imageId) {
                    $image = $product->images()->find($imageId);
                    if ($image) {
                        Storage::disk('public')->delete($image->path);
                        $image->delete();
                    }
                }
            }

            // Handle new image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $product->images()->create([
                        'path' => $path,
                        'is_primary' => $product->images()->count() === 0 // Make first image primary if no images exist
                    ]);
                }
            }

            $product->update([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'subcategory_id' => $validated['subcategory_id'] ?? null,
                'brand' => $validated['brand'],
                'model' => $validated['model'],
                'price' => $validated['price'],
                'description' => $validated['description'],
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product updated successfully'
                ]);
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error updating product: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
                'product_id' => $product->id
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating product: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error updating product: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();
            
            // Delete product images
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
            
            // Delete the product
        $product->delete();
            
            DB::commit();

        return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error deleting product: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'exception' => $e
            ]);
            
            return back()->with('error', 'Error deleting product: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        // Load relationships and data needed for form
        $product->load(['category', 'subcategory', 'images', 'specifications.specificationType']);
        
        // Get categories for dropdown
        $categories = Category::orderBy('name')->get();
        
        // Get subcategories for the product's category
        $subcategories = \App\Models\SubCategory::where('category_id', $product->category_id)
            ->orderBy('name')
            ->get();
        
        // Get specification types for the product's category
        $specificationTypes = \App\Models\SpecificationType::where('category_id', $product->category_id)
            ->orderBy('display_order')
            ->get();
        
        // Create a map of existing specifications for easier access in the view
        $specValues = [];
        foreach ($product->specifications as $spec) {
            $specValues[$spec->specification_type_id] = $spec->value;
        }
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.products.edit-form', compact(
                    'product', 
                    'categories', 
                    'subcategories', 
                    'specificationTypes',
                    'specValues'
                ))->render(),
                'product' => $product
            ]);
        }
        
        return view('admin.products.edit', compact(
            'product', 
            'categories', 
            'subcategories', 
            'specificationTypes',
            'specValues'
        ));
    }

    /**
     * Display all products from a specific category with filtering options.
     * Used by the "View All" link in the mega menu.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function categoryProducts(string $slug, Request $request)
    {
        // Find the category by slug
        $category = Category::where('slug', $slug)->firstOrFail();
        
        // Start query with the category
        $query = Product::where('category_id', $category->id)
                        ->where('is_active', true)
                        ->with(['category', 'subcategory', 'images']);
        
        // Apply subcategory filter
        if ($request->filled('subcategory')) {
            $query->where('subcategory_id', $request->subcategory);
        }
        
        // Apply brand filter
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }
        
        // Apply price range filter
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }
        
        // Apply sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        
        if (in_array($sortBy, ['name', 'price', 'created_at'])) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }
        
        // Get the products with pagination
        $products = $query->paginate(12)->withQueryString();
        
        // Get subcategories for the category
        $subcategories = \App\Models\SubCategory::where('category_id', $category->id)
                                                ->orderBy('name')
                                                ->get();
        
        // Get unique brands for this category
        $brands = Product::where('category_id', $category->id)
                         ->where('is_active', true)
                         ->distinct()
                         ->pluck('brand')
                         ->filter()
                         ->sort();
        
        // Get price range for the category
        $priceRange = [
            'min' => Product::where('category_id', $category->id)
                           ->where('is_active', true)
                           ->min('price') ?? 0,
            'max' => Product::where('category_id', $category->id)
                           ->where('is_active', true)
                           ->max('price') ?? 1000
        ];
        
        return view('products.category', compact(
            'category', 
            'products', 
            'subcategories', 
            'brands', 
            'priceRange',
            'sortBy',
            'sortDir'
        ));
    }

    /**
     * Display products by brand for a specific category.
     * Used for mega menu links like "laptop-by-brand/dell".
     *
     * @param  string  $category
     * @param  string  $brand
     * @return \Illuminate\Http\Response
     */
    public function productsByBrand(string $category, string $brand, Request $request)
    {
        // Find the base category (e.g., 'laptop' from 'laptop-by-brand')
        $baseCategory = explode('-by-brand', $category)[0];
        $categoryObj = Category::where('slug', $baseCategory)->first();
        
        if (!$categoryObj) {
            abort(404, 'Category not found');
        }
        
        // Start building the query
        $query = Product::where('category_id', $categoryObj->id)
                        ->where('is_active', true)
                        ->with(['category', 'subcategory', 'images']);
        
        // If not "all", filter by the specific brand
        if ($brand !== 'all') {
            // Find the brand using its slug
            $brandObj = \App\Models\Brand::where('slug', $brand)->first();
            
            if ($brandObj) {
                $query->where('brand', $brandObj->name);
            } else {
                // Fallback to using the slug as the brand name if no match is found
                $query->where('brand', $brand);
                \Log::warning("Brand with slug '{$brand}' not found in brands table. Using slug as name.");
            }
        }
        
        // Apply sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        
        if (in_array($sortBy, ['name', 'price', 'created_at'])) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }
        
        // Get the products with pagination
        $products = $query->paginate(12)->withQueryString();
        
        // Get subcategories for the category
        $subcategories = \App\Models\SubCategory::where('category_id', $categoryObj->id)
                                               ->orderBy('name')
                                               ->get();
        
        // Get unique brands for this category
        $brands = Product::where('category_id', $categoryObj->id)
                        ->where('is_active', true)
                        ->distinct()
                        ->pluck('brand')
                        ->filter()
                        ->sort();
        
        // Get price range for the category
        $priceRange = [
            'min' => Product::where('category_id', $categoryObj->id)
                           ->where('is_active', true)
                           ->min('price') ?? 0,
            'max' => Product::where('category_id', $categoryObj->id)
                           ->where('is_active', true)
                           ->max('price') ?? 1000
        ];
        
        return view('products.brand-filter', compact(
            'category',
            'brand',
            'categoryObj',
            'products', 
            'subcategories', 
            'brands', 
            'priceRange',
            'sortBy',
            'sortDir'
        ));
    }
} 