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
        
        // Query only parent menus (null parent_id) and load their children
        $mainQuery = MenuItem::where('location', 'main')
                      ->whereNull('parent_id')
                      ->with(['children' => function($query) {
                          $query->orderBy('order');
                      }]);
                      
        $footerQuery = MenuItem::where('location', 'footer')
                        ->whereNull('parent_id')
                        ->with(['children' => function($query) {
                            $query->orderBy('order');
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
        
        // Get list of menu items for parent dropdown
        $menuItems = MenuItem::whereNull('parent_id')
                           ->orderBy('name')
                           ->get();

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
                    if (preg_match('/\/([^\/]+)-by-brand\/([^\/]+)/', $parentMenu->url, $matches)) {
                        $categorySlug = $matches[1];
                    } 
                    // If parent has a category associated
                    elseif ($parentMenu->category_id) {
                        $category = \App\Models\Category::find($parentMenu->category_id);
                        $categorySlug = $category ? $category->slug : \Illuminate\Support\Str::slug($parentMenu->name);
                    } 
                    // Otherwise use parent's name as category
                    else {
                        $categorySlug = \Illuminate\Support\Str::slug($parentMenu->name);
                    }
                    
                    // Format: /category-by-brand/brand
                    $validated['url'] = '/' . $categorySlug . '-by-brand/' . $brandValue;
                    $validated['route_name'] = 'products.by.brand';
                }
            }
            // Special handling for direct category selection (no parent)
            else if (!empty($validated['category_id']) && $request->has('brand_for_url')) {
                // Get the category slug
                $category = \App\Models\Category::findOrFail($validated['category_id']);
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
        
        try {
            // Create the menu item
            $menuItem = MenuItem::create($validated);
            
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
            
            // Get list of menu items for parent dropdown (don't query if $menu is null)
            $menuItems = MenuItem::whereNull('parent_id')
                        ->when($menu && $menu->id, function ($query) use ($menu) {
                            return $query->where('id', '!=', $menu->id);
                        })
                        ->orderBy('name')
            ->get();
        
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
                    if (preg_match('/\/([^\/]+)-by-brand\/([^\/]+)/', $parentMenu->url, $matches)) {
                        $categorySlug = $matches[1];
                    } 
                    // If parent has a category associated
                    elseif ($parentMenu->category_id) {
                        $category = \App\Models\Category::find($parentMenu->category_id);
                        $categorySlug = $category ? $category->slug : \Illuminate\Support\Str::slug($parentMenu->name);
                    } 
                    // Otherwise use parent's name as category
                    else {
                        $categorySlug = \Illuminate\Support\Str::slug($parentMenu->name);
                    }
                    
                    // Format: /category-by-brand/brand
                    $validated['url'] = '/' . $categorySlug . '-by-brand/' . $brandValue;
                    $validated['route_name'] = 'products.by.brand';
                }
            }
            // Special handling for direct category selection (no parent)
            else if (!empty($validated['category_id']) && $request->has('brand_for_url')) {
                // Get the category slug
                $category = \App\Models\Category::findOrFail($validated['category_id']);
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
        
        try {
            // Update the menu item
            $menu->update($validated);
            
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
}
