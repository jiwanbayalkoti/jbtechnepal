<?php

namespace App\Http\Controllers\Admin;

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
     * Show the form for creating a new product.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get all categories for the dropdown
        $categories = Category::orderBy('name')->get();
        
        return view('admin.products.create', compact('categories'));
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
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'specifications' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Create the product
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $count = 1;

            // Check if slug exists and make it unique by appending a number
            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            $product = new Product([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'subcategory_id' => $validated['subcategory_id'],
                'brand' => $validated['brand'],
                'model' => $validated['model'],
                'price' => $validated['price'],
                'description' => $validated['description'],
                'slug' => $slug
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
                \Log::info('Images found in request', [
                    'count' => count($request->file('images')),
                    'file_names' => collect($request->file('images'))->map->getClientOriginalName()->toArray()
                ]);
                
                foreach ($request->file('images') as $index => $image) {
                    try {
                        $path = $image->store('products', 'public');
                        \Log::info('Image stored at path', ['path' => $path]);
                        
                        $productImage = $product->images()->create([
                            'path' => $path,
                            'is_primary' => $index === 0 // Make first image primary
                        ]);
                        
                        \Log::info('Image saved for product', [
                            'product_id' => $product->id, 
                            'path' => $path,
                            'image_id' => $productImage->id
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Failed to save image', [
                            'error' => $e->getMessage(),
                            'file' => $image->getClientOriginalName()
                        ]);
                    }
                }
            } else {
                \Log::warning('No images found in request', [
                    'has_file' => $request->hasFile('images'),
                    'request_keys' => array_keys($request->all()),
                    'files' => $request->files->all()
                ]);
            }
            
            // Handle specifications
            if ($request->has('specifications')) {
                // Get all specification type IDs for this category to ensure we only process valid ones
                $validSpecTypeIds = \App\Models\SpecificationType::where('category_id', $validated['category_id'])
                    ->pluck('id')
                    ->toArray();
                
                foreach ($request->specifications as $specTypeId => $value) {
                    // Only process specifications that are valid for this category and have a value
                    if (in_array($specTypeId, $validSpecTypeIds) && !empty($value)) {
                        $product->specifications()->create([
                            'specification_type_id' => $specTypeId,
                            'value' => $value
                        ]);
                    }
                }
                
                \Log::info('Product specifications saved', [
                    'product_id' => $product->id,
                    'spec_count' => count($request->specifications)
                ]);
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
            'delete_images.*' => 'exists:product_images,id',
            'specifications' => 'nullable|array',
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
            
            // Create update data array with default subcategory_id to null if not provided
            $updateData = [
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'subcategory_id' => $validated['subcategory_id'] ?? null,
                'brand' => $validated['brand'],
                'model' => $validated['model'],
                'price' => $validated['price'],
                'description' => $validated['description'],
            ];

            // Only update slug if name has changed
            if ($product->name !== $validated['name']) {
                $slug = Str::slug($validated['name']);
                $originalSlug = $slug;
                $count = 1;
                
                // Check if slug exists and make it unique by appending a number
                // Exclude current product from the check
                while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }
                
                $updateData['slug'] = $slug;
            }

            $product->update($updateData);
            
            // Handle specifications
            if ($request->has('specifications')) {
                // Get all specification type IDs for this category to ensure we only process valid ones
                $validSpecTypeIds = \App\Models\SpecificationType::where('category_id', $validated['category_id'])
                    ->pluck('id')
                    ->toArray();
                
                foreach ($request->specifications as $specTypeId => $value) {
                    // Only process specifications that are valid for this category
                    if (in_array($specTypeId, $validSpecTypeIds) && !empty($value)) {
                        // Find existing specification or create new one
                        $product->specifications()->updateOrCreate(
                            ['specification_type_id' => $specTypeId],
                            ['value' => $value]
                        );
                    }
                }
                
                // Log successful specifications update
                \Log::info('Product specifications updated', [
                    'product_id' => $product->id,
                    'spec_count' => count($request->specifications)
                ]);
            }

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
     * Get specification types for a specific category.
     * Used for AJAX requests when changing category in product forms.
     *
     * @param  int  $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSpecificationTypes($categoryId)
    {
        if (!$categoryId) {
            return response()->json([
                'success' => false,
                'message' => 'Category ID is required'
            ], 400);
        }
        
        $specificationTypes = \App\Models\SpecificationType::where('category_id', $categoryId)
            ->orderBy('display_order')
            ->get();
        
        return response()->json([
            'success' => true,
            'specificationTypes' => $specificationTypes
        ]);
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
     * Get subcategories for a specific category.
     * Used for AJAX requests when changing category in product forms.
     *
     * @param  int  $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubcategories($categoryId)
    {
        if (!$categoryId) {
            return response()->json([
                'success' => false,
                'message' => 'Category ID is required'
            ], 400);
        }
        
        $subcategories = \App\Models\SubCategory::where('category_id', $categoryId)
            ->orderBy('name')
            ->get();
        
        return response()->json([
            'success' => true,
            'subcategories' => $subcategories
        ]);
    }
} 