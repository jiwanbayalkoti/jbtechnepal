<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    /**
     * Display a listing of menu items.
     */
    public function index(Request $request)
    {
        $location = $request->input('location');
        
        // Query only parent menus (null parent_id) and load their children with nested children
        $mainQuery = MenuItem::where('location', 'main')
                      ->whereNull('parent_id')
                      ->with(['children' => function($query) {
                          $query->orderBy('order');
                          // Load the grandchildren
                          $query->with(['children' => function($q) {
                              $q->orderBy('order');
                          }]);
                      }]);
                      
        $footerQuery = MenuItem::where('location', 'footer')
                        ->whereNull('parent_id')
                        ->with(['children' => function($query) {
                            $query->orderBy('order');
                            // Load the grandchildren
                            $query->with(['children' => function($q) {
                                $q->orderBy('order');
                            }]);
                        }]);
        
        if ($location) {
            $mainQuery->when($location !== 'main', function($q) { return $q->whereRaw('1=0'); });
            $footerQuery->when($location !== 'footer', function($q) { return $q->whereRaw('1=0'); });
        }
        
        $mainMenu = $mainQuery->orderBy('order')->get();
        $footerMenu = $footerQuery->orderBy('order')->get();
        $parentMenuItems = MenuItem::whereNull('parent_id')->get();
        $locations = MenuItem::select('location')->distinct()->pluck('location');
        
        return view('admin.menus.index', compact('mainMenu', 'footerMenu', 'parentMenuItems', 'locations', 'location'));
    }
    
    /**
     * Show the form for creating a new menu item.
     */
    public function create()
    {
        // Get categories for dropdown
        $categories = \App\Models\Category::orderBy('name')->get();
        
        // Get all menu items (both parents and children) for dropdown
        // First get parent menu items
        $parentMenuItems = MenuItem::whereNull('parent_id')
                           ->orderBy('name')
                           ->get();
        
        // Get all menu items for a flattened dropdown with hierarchical display
        $menuItems = $this->getAllMenuItemsForDropdown();

        // Get list of available locations
        $locations = ['main', 'footer', 'admin'];
        
        // Get brands for dropdown
        $brands = Brand::orderBy('name')->get();
        
        return view('admin.menus.create', compact('menuItems', 'locations', 'categories', 'brands'));
    }
    
    /**
     * Store a newly created menu item in storage.
     */
    public function store(Request $request)
    {
        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
            'parent_id' => 'nullable|exists:menu_items,id',
            'icon' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'is_dynamic_page' => 'boolean',
            'slug' => 'nullable|string|max:255|unique:menu_items,slug',
            'content' => 'nullable|string',
            'brand_for_url' => 'nullable|string|max:255',
            'auto_generate_models' => 'nullable|boolean',
        ];

        // Add URL or route_name validation based on is_dynamic_page
        if (!$request->has('is_dynamic_page') || !$request->boolean('is_dynamic_page')) {
            $rules['url'] = 'nullable|string|max:255';
            $rules['route_name'] = 'nullable|string|max:255';
        }

        // Validate the request
        $validated = $request->validate($rules);

        // Handle booleans that might not be present in the request
        if (!$request->has('active')) {
            $validated['active'] = false;
        }
        
        if (!$request->has('is_dynamic_page')) {
            $validated['is_dynamic_page'] = false;
        }
        
        if (!$request->has('auto_generate_models')) {
            $validated['auto_generate_models'] = false;
        }
        
        $shouldGenerateModels = $request->boolean('auto_generate_models');
        $brandSlug = $request->brand_for_url ?? 'all';
        $categoryId = $validated['category_id'] ?? null;
        $categorySlug = null;
        $brandName = null;

        // If this is a dynamic page, make sure we have a slug
        if ($request->boolean('is_dynamic_page')) {
            if (empty($validated['slug'])) {
                // Generate slug from name
                $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
            }
        } else {
            // Check for parent menu first - this takes priority
            if (!empty($validated['parent_id'])) {
                $parentMenu = MenuItem::find($validated['parent_id']);
                
                if ($parentMenu) {
                    $brandValue = $request->brand_for_url ?? 'all';
                    
                    // Check if parent menu already has a category-by-brand format
                    if (preg_match('/\/([^\/]+)-by-brand\/([^\/]+)$/', $parentMenu->url, $matches)) {
                        // This is a direct child of a category-by-brand menu (child level 1)
                        $categorySlug = $matches[1];
                        $brandSlug = $matches[2];
                        
                        // For child menu items under a brand, use the model/series format
                        // Format: /category-by-brand/brand/model
                        $modelSlug = \Illuminate\Support\Str::slug($validated['name']);
                        $validated['url'] = '/' . $categorySlug . '-by-brand/' . $brandSlug . '/' . $modelSlug;
                        $validated['route_name'] = 'products.by.brand.model';
                    }
                    // Check if parent menu already has a model format (third level)
                    elseif (preg_match('/\/([^\/]+)-by-brand\/([^\/]+)\/([^\/]+)$/', $parentMenu->url, $matches)) {
                        // This is a grandchild (child level 2) - we'll keep the same URL pattern but append the slug
                        $categorySlug = $matches[1];
                        $brandSlug = $matches[2]; 
                        $modelSlug = $matches[3];
                        $subModelSlug = \Illuminate\Support\Str::slug($validated['name']);
                        
                        // We'll use the same URL but add a query parameter for filtering
                        $validated['url'] = '/' . $categorySlug . '-by-brand/' . $brandSlug . '/' . $modelSlug . '?submodel=' . $subModelSlug;
                        $validated['route_name'] = 'products.by.brand.model';
                    }
                    // If parent has a category associated
                    elseif ($parentMenu->category_id) {
                        $category = \App\Models\Category::find($parentMenu->category_id);
                        $categorySlug = $category ? $category->slug : \Illuminate\Support\Str::slug($parentMenu->name);
                        $categoryId = $parentMenu->category_id;
                        
                        // Format: /category-by-brand/brand
                        $validated['url'] = '/' . $categorySlug . '-by-brand/' . $brandValue;
                        $validated['route_name'] = 'products.by.brand';
                    } 
                    // Otherwise use parent's name as category
                    else {
                        $categorySlug = \Illuminate\Support\Str::slug($parentMenu->name);
                        
                        // Format: /category-by-brand/brand
                        $validated['url'] = '/' . $categorySlug . '-by-brand/' . $brandValue;
                        $validated['route_name'] = 'products.by.brand';
                    }
                }
            }
            // Special handling for direct category selection (no parent)
            else if (!empty($validated['category_id']) && $request->has('brand_for_url')) {
                // Get the category slug
                $category = \App\Models\Category::findOrFail($validated['category_id']);
                $categorySlug = $category->slug;
                $brandValue = $request->brand_for_url;
                
                // Format: /category-by-brand/brand
                $validated['url'] = '/' . $category->slug . '-by-brand/' . $brandValue;
                $validated['route_name'] = 'products.by.brand';
            }
            // For non-dynamic pages with no parent or category, automatically generate URL and route_name
            else if (empty($validated['url']) && empty($validated['route_name'])) {
                // Generate URL from name
                $validated['url'] = '/' . \Illuminate\Support\Str::slug($validated['name']);
                
                // Generate route_name from name
                $validated['route_name'] = \Illuminate\Support\Str::slug($validated['name']);
            }
        }
        
        // Remove the fields that aren't stored in the database
        if (isset($validated['brand_for_url'])) {
            unset($validated['brand_for_url']);
        }
        
        if (isset($validated['auto_generate_models'])) {
            unset($validated['auto_generate_models']);
        }
        
        try {
            // Create the menu item
            $menuItem = MenuItem::create($validated);
            
            // If auto-generate option is checked and we have a brand-by-category URL
            if ($shouldGenerateModels && $categorySlug && $brandSlug !== 'all') {
                $this->generateModelSubmenus($menuItem, $categorySlug, $brandSlug, $categoryId);
            }
            
            // Clear menu cache for the affected location
            $this->clearMenuCache($menuItem->location);
            
            return redirect()->route('admin.menus.index')
                ->with('success', "Menu item '{$menuItem->name}' created successfully!");
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error creating menu item: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Error creating menu item: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for editing the specified menu item.
     */
    public function edit($menu)
    {
        try {
            // Get the menu item by ID
            $menu = MenuItem::findOrFail($menu);
            
            // Get categories for dropdown
            $categories = \App\Models\Category::orderBy('name')->get();
            
            // Get all menu items for dropdown except the current menu and its children
            $menuItems = $this->getAllMenuItemsForDropdown($menu->id);
        
            // Get list of available locations
            $locations = ['main', 'footer', 'admin'];
            
            // Get brands for dropdown
            $brands = Brand::orderBy('name')->get();
            
            // Handle AJAX requests specifically
            if (request()->ajax() || request()->wantsJson()) {
                $view = view('admin.menus.edit-form', compact('menu', 'menuItems', 'locations', 'categories', 'brands'))->render();
                
                return response()->json([
                    'success' => true,
                    'html' => '<div id="formContent">' . $view . '</div>'
                ]);
            }
            
            // Regular view for non-AJAX requests
            return view('admin.menus.edit', compact('menu', 'menuItems', 'locations', 'categories', 'brands'));
        } catch (\Exception $e) {
            \Log::error('Error in menu edit: ' . $e->getMessage(), [
                'menu_id' => $menu,
                'exception' => $e
            ]);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading menu item: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.menus.index')
                ->with('error', 'Error loading menu item: ' . $e->getMessage());
        }
    }
    
    /**
     * Update the specified menu item in storage.
     */
    public function update(Request $request, $menu)
    {
        \Log::info('Menu update called with ID: ' . $menu);
        \Log::info('Full request data: ', $request->all());
        
        // Get the menu item by ID
        $menu = MenuItem::findOrFail($menu);
        
        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
            'parent_id' => 'nullable|exists:menu_items,id',
            'icon' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'is_dynamic_page' => 'boolean',
            'slug' => 'nullable|string|max:255|unique:menu_items,slug,' . $menu->id,
            'content' => 'nullable|string',
            'brand_for_url' => 'nullable|string|max:255',
            'auto_generate_models' => 'nullable|boolean',
        ];

        // Add URL or route_name validation based on is_dynamic_page
        if (!$request->has('is_dynamic_page') || !$request->boolean('is_dynamic_page')) {
            $rules['url'] = 'nullable|string|max:255';
            $rules['route_name'] = 'nullable|string|max:255';
        }

        // Validate the request
        $validated = $request->validate($rules);

        // Handle booleans that might not be present in the request
        if (!$request->has('active')) {
            $validated['active'] = false;
        }
        
        if (!$request->has('is_dynamic_page')) {
            $validated['is_dynamic_page'] = false;
        }
        
        if (!$request->has('auto_generate_models')) {
            $validated['auto_generate_models'] = false;
        }
        
        $shouldGenerateModels = $request->boolean('auto_generate_models');
        $brandSlug = $request->brand_for_url ?? 'all';
        $categoryId = $validated['category_id'] ?? null;
        $categorySlug = null;

        // If this is a dynamic page, make sure we have a slug
        if ($request->boolean('is_dynamic_page')) {
            if (empty($validated['slug'])) {
                // Generate slug from name
                $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
            }
        } else {
            // Check for parent menu first - this takes priority
            if (!empty($validated['parent_id'])) {
                $parentMenu = MenuItem::find($validated['parent_id']);
                
                if ($parentMenu) {
                    $brandValue = $request->brand_for_url ?? 'all';
                    
                    // Check if parent menu already has a category-by-brand format
                    if (preg_match('/\/([^\/]+)-by-brand\/([^\/]+)$/', $parentMenu->url, $matches)) {
                        // This is a direct child of a category-by-brand menu (child level 1)
                        $categorySlug = $matches[1];
                        $brandSlug = $matches[2];
                        
                        // For child menu items under a brand, use the model/series format
                        // Format: /category-by-brand/brand/model
                        $modelSlug = \Illuminate\Support\Str::slug($validated['name']);
                        $validated['url'] = '/' . $categorySlug . '-by-brand/' . $brandSlug . '/' . $modelSlug;
                        $validated['route_name'] = 'products.by.brand.model';
                    }
                    // Check if parent menu already has a model format (third level)
                    elseif (preg_match('/\/([^\/]+)-by-brand\/([^\/]+)\/([^\/]+)$/', $parentMenu->url, $matches)) {
                        // This is a grandchild (child level 2) - we'll keep the same URL pattern but append the slug
                        $categorySlug = $matches[1];
                        $brandSlug = $matches[2]; 
                        $modelSlug = $matches[3];
                        $subModelSlug = \Illuminate\Support\Str::slug($validated['name']);
                        
                        // We'll use the same URL but add a query parameter for filtering
                        $validated['url'] = '/' . $categorySlug . '-by-brand/' . $brandSlug . '/' . $modelSlug . '?submodel=' . $subModelSlug;
                        $validated['route_name'] = 'products.by.brand.model';
                    }
                    // If parent has a category associated
                    elseif ($parentMenu->category_id) {
                        $category = \App\Models\Category::find($parentMenu->category_id);
                        $categorySlug = $category ? $category->slug : \Illuminate\Support\Str::slug($parentMenu->name);
                        $categoryId = $parentMenu->category_id;
                        
                        // Format: /category-by-brand/brand
                        $validated['url'] = '/' . $categorySlug . '-by-brand/' . $brandValue;
                        $validated['route_name'] = 'products.by.brand';
                    } 
                    // Otherwise use parent's name as category
                    else {
                        $categorySlug = \Illuminate\Support\Str::slug($parentMenu->name);
                        
                        // Format: /category-by-brand/brand
                        $validated['url'] = '/' . $categorySlug . '-by-brand/' . $brandValue;
                        $validated['route_name'] = 'products.by.brand';
                    }
                }
            }
            // Special handling for direct category selection (no parent)
            else if (!empty($validated['category_id']) && $request->has('brand_for_url')) {
                // Get the category slug
                $category = \App\Models\Category::findOrFail($validated['category_id']);
                $categorySlug = $category->slug;
                $brandValue = $request->brand_for_url;
                
                // Format: /category-by-brand/brand
                $validated['url'] = '/' . $category->slug . '-by-brand/' . $brandValue;
                $validated['route_name'] = 'products.by.brand';
            }
            // For non-dynamic pages with no parent or category, automatically generate URL and route_name
            else if (empty($validated['url']) && empty($validated['route_name'])) {
                // Generate URL from name
                $validated['url'] = '/' . \Illuminate\Support\Str::slug($validated['name']);
                
                // Generate route_name from name
                $validated['route_name'] = \Illuminate\Support\Str::slug($validated['name']);
            }
        }
        
        // Remove the brand_for_url field as it's not stored in the database
        if (isset($validated['brand_for_url'])) {
            unset($validated['brand_for_url']);
        }
        
        if (isset($validated['auto_generate_models'])) {
            unset($validated['auto_generate_models']);
        }
        
        try {
            // Update the menu item
            $menu->update($validated);
            
            // If auto-generate option is checked and we have a brand-by-category URL
            if ($shouldGenerateModels && $categorySlug && $brandSlug !== 'all') {
                $this->generateModelSubmenus($menu, $categorySlug, $brandSlug, $categoryId);
            }
            
            // Clear menu cache for the affected location
            $this->clearMenuCache($menu->location);
            
            return redirect()->route('admin.menus.index')
                ->with('success', "Menu item '{$menu->name}' updated successfully!");
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error updating menu item: ' . $e->getMessage(), [
                'menu_id' => $menu->id,
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Error updating menu item: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified menu item from storage.
     */
    public function destroy($menu)
    {
        try {
            // Get the menu item by ID
            $menu = MenuItem::findOrFail($menu);
            
            // Check if the menu item has children
            if ($menu->children()->count() > 0) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot delete menu item '{$menu->name}' because it has child items. Please remove all child items first."
                    ], 422);
                }
                
                return redirect()->route('admin.menus.index')
                    ->with('error', "Cannot delete menu item '{$menu->name}' because it has child items. Please remove all child items first.");
            }
            
            $menuName = $menu->name;
            $location = $menu->location;
            
            $menu->delete();
            
            // Clear menu cache
            $this->clearMenuCache($location);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Menu item '{$menuName}' deleted successfully."
                ]);
            }
            
            return redirect()->route('admin.menus.index')
                ->with('success', "Menu item '{$menuName}' deleted successfully.");
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error deleting menu item: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting menu item: ' . $e->getMessage()
                ], 500);
            }
        
        return redirect()->route('admin.menus.index')
                ->with('error', 'Error deleting menu item: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear the menu cache for the specified location
     */
    private function clearMenuCache($location)
    {
        try {
            if ($location === 'main') {
                \Illuminate\Support\Facades\Cache::forget('main_menu');
            } elseif ($location === 'footer') {
                \Illuminate\Support\Facades\Cache::forget('footer_menu');
            } elseif ($location === 'footer_admin') {
                // If you have a specific cache key for the footer admin menu
                \Illuminate\Support\Facades\Cache::forget('footer_admin_menu');
            }
            
            // You might want to clear all menu caches to be sure
            \Illuminate\Support\Facades\Cache::forget('main_menu');
            \Illuminate\Support\Facades\Cache::forget('footer_menu');
            
            // Set a flag that we've updated the menu
            // This will be used in the SettingsServiceProvider to reload the menu
            \Illuminate\Support\Facades\Cache::put('menu_updated', true, 600); // 10 minutes
            
            \Log::info('Menu cache cleared for location: ' . $location);
        } catch (\Exception $e) {
            \Log::error('Error clearing menu cache: ' . $e->getMessage());
        }
    }
    
    /**
     * Helper function to get all menu items for dropdown with hierarchical display
     * 
     * @param int|null $excludeMenuId Menu ID to exclude (used when editing)
     * @return Collection
     */
    private function getAllMenuItemsForDropdown($excludeMenuId = null)
    {
        // Get all parent menu items
        $parents = MenuItem::whereNull('parent_id')
                 ->orderBy('location')
                 ->orderBy('order')
                 ->get();
                 
        $allMenuItems = collect();
        
        // For each parent, get its children
        foreach ($parents as $parent) {
            // Skip if this is the menu being edited
            if ($excludeMenuId && $parent->id == $excludeMenuId) {
                continue;
            }
            
            // Add parent to the collection
            $allMenuItems->push([
                'id' => $parent->id,
                'name' => $parent->name,
                'location' => $parent->location,
                'level' => 0,
                'url' => $parent->url,
                'display_name' => $parent->name . ' (' . $parent->location . ')'
            ]);
            
            // Add children to the collection
            $this->addChildMenuItems($allMenuItems, $parent, 1, $excludeMenuId);
        }
        
        return $allMenuItems;
    }
    
    /**
     * Helper function to add child menu items recursively
     * 
     * @param Collection $collection Collection to add items to
     * @param MenuItem $parent Parent menu item
     * @param int $level Nesting level
     * @param int|null $excludeMenuId Menu ID to exclude
     */
    private function addChildMenuItems(&$collection, $parent, $level, $excludeMenuId = null)
    {
        // Get children of this parent
        $children = MenuItem::where('parent_id', $parent->id)
                  ->orderBy('order')
                  ->get();
                  
        foreach ($children as $child) {
            // Skip if this is the menu being edited
            if ($excludeMenuId && $child->id == $excludeMenuId) {
                continue;
            }
            
            // Add this child to the collection with indentation
            $prefix = str_repeat('— ', $level);
            $collection->push([
                'id' => $child->id,
                'name' => $child->name,
                'location' => $child->location,
                'level' => $level,
                'url' => $child->url,
                'display_name' => $prefix . $child->name . ' (' . $child->location . ')'
            ]);
            
            // Recursively add this child's children
            $this->addChildMenuItems($collection, $child, $level + 1, $excludeMenuId);
        }
    }
    
    /**
     * Generate model submenu items automatically for a brand menu
     * 
     * @param MenuItem $parentMenu The parent brand menu
     * @param string $categorySlug The category slug
     * @param string $brandSlug The brand slug
     * @param int $categoryId The category ID
     * @return void
     */
    private function generateModelSubmenus($parentMenu, $categorySlug, $brandSlug, $categoryId)
    {
        // Find the brand record
        $brandObj = \App\Models\Brand::where('slug', $brandSlug)->first();
        
        if (!$brandObj && $brandSlug !== 'all') {
            // Try to find the brand by name if slug doesn't match
            $brandObj = \App\Models\Brand::where('name', 'LIKE', $brandSlug)
                                        ->orWhere('name', 'LIKE', str_replace('-', ' ', $brandSlug))
                                        ->first();
        }
        
        if (!$brandObj && $brandSlug !== 'all') {
            \Log::warning("Could not find brand with slug '{$brandSlug}' for automatic model generation");
            return;
        }
        
        $brandName = $brandObj ? $brandObj->name : ucfirst($brandSlug);
        
        // Start query to find unique models for this brand and category
        $query = \App\Models\Product::query();
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        if ($brandObj) {
            $query->where('brand', $brandObj->name);
        } else {
            $query->where('brand', 'LIKE', '%' . str_replace('-', ' ', $brandSlug) . '%');
        }
        
        // Get all unique models
        $models = $query->distinct()
                      ->pluck('model')
                      ->filter()
                      ->unique()
                      ->map(function($model) {
                          return trim($model);
                      })
                      ->filter(function($model) {
                          return !empty($model);
                      });
        
        // Create a menu item for each model
        foreach ($models as $model) {
            // Skip very short model names as they might not be real models
            if (strlen($model) < 2) {
                continue;
            }
            
            $modelSlug = \Illuminate\Support\Str::slug($model);
            
            // Check if this menu item already exists
            $existingMenu = MenuItem::where('parent_id', $parentMenu->id)
                                  ->where('name', $model)
                                  ->first();
            
            if ($existingMenu) {
                continue; // Skip if already exists
            }
            
            // Create the model menu item
            try {
                MenuItem::create([
                    'name' => $model,
                    'location' => $parentMenu->location,
                    'order' => MenuItem::where('parent_id', $parentMenu->id)->max('order') + 10, // Put at the end
                    'parent_id' => $parentMenu->id,
                    'url' => '/' . $categorySlug . '-by-brand/' . $brandSlug . '/' . $modelSlug,
                    'route_name' => 'products.by.brand.model',
                    'active' => true
                ]);
                
                \Log::info("Auto-generated model menu item '{$model}' under '{$brandName}'");
            } catch (\Exception $e) {
                \Log::error("Failed to auto-generate model menu item '{$model}': " . $e->getMessage());
            }
        }
    }
}
