<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Log;

class DebugMenuController extends Controller
{
    /**
     * Update a menu item with direct debug logging
     */
    public function update(Request $request, $id)
    {
        Log::info('Debug Menu Update Called', [
            'id' => $id,
            'all_request_data' => $request->all(),
            'url' => $request->input('url'),
            'route_name' => $request->input('route_name'),
            'is_dynamic_page' => $request->boolean('is_dynamic_page')
        ]);
        
        try {
            // Get the menu item by ID
            $menu = MenuItem::findOrFail($id);
            
            // Make a copy of the original values for logging
            $original = [
                'url' => $menu->url,
                'route_name' => $menu->route_name,
                'name' => $menu->name
            ];
            
            // Simple update with minimal processing
            $menu->name = $request->input('name');
            $menu->url = $request->input('url');
            $menu->route_name = $request->input('route_name');
            $menu->location = $request->input('location');
            $menu->order = $request->input('order');
            $menu->icon = $request->input('icon');
            $menu->parent_id = $request->input('parent_id') ?: null;
            $menu->category_id = $request->input('category_id') ?: null;
            $menu->active = $request->boolean('active');
            $menu->is_mega_menu = $request->boolean('is_mega_menu');
            $menu->is_dynamic_page = $request->boolean('is_dynamic_page');
            
            if ($menu->is_dynamic_page) {
                $menu->slug = $request->input('slug') ?: \Illuminate\Support\Str::slug($menu->name);
                $menu->content = $request->input('content');
            }
            
            $menu->save();
            
            // Log the changes
            Log::info('Menu updated successfully', [
                'id' => $menu->id,
                'original' => $original,
                'new' => [
                    'url' => $menu->url,
                    'route_name' => $menu->route_name,
                    'name' => $menu->name
                ]
            ]);
            
            // Clear menu cache
            $this->clearMenuCache($menu->location);
            
            return response()->json([
                'success' => true,
                'message' => "Menu item '{$menu->name}' updated successfully!",
                'menu' => $menu
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating menu item', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating menu: ' . $e->getMessage()
            ], 500);
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
                \Illuminate\Support\Facades\Cache::forget('footer_admin_menu');
            }
            
            // Also clear all menus to be sure
            \Illuminate\Support\Facades\Cache::forget('main_menu');
            \Illuminate\Support\Facades\Cache::forget('footer_menu');
            
            // Set a flag that we've updated the menu
            \Illuminate\Support\Facades\Cache::put('menu_updated', true, 600); // 10 minutes
        } catch (\Exception $e) {
            \Log::error('Error clearing menu cache: ' . $e->getMessage());
        }
    }

    /**
     * Create test menu items for showing hierarchical menu structure
     */
    public function createTestMenuItems()
    {
        try {
            // First check if we already have a 'Laptops' main menu
            $laptopsMenu = \App\Models\MenuItem::where('name', 'Laptops')
                ->where('location', 'main')
                ->first();
                
            if (!$laptopsMenu) {
                // Create the parent category menu
                $laptopsMenu = \App\Models\MenuItem::create([
                    'name' => 'Laptops',
                    'url' => '/category/laptop',
                    'route_name' => 'categories.show',
                    'icon' => 'fas fa-laptop',
                    'location' => 'main',
                    'active' => true,
                    'order' => 10,
                    'parent_id' => null
                ]);
                
                Log::info('Created Laptops parent menu', ['id' => $laptopsMenu->id]);
            }
            
            // Check if we already have a Dell submenu
            $dellMenu = \App\Models\MenuItem::where('name', 'Dell')
                ->where('parent_id', $laptopsMenu->id)
                ->first();
                
            if (!$dellMenu) {
                // Create the brand submenu
                $dellMenu = \App\Models\MenuItem::create([
                    'name' => 'Dell',
                    'url' => '/laptop-by-brand/dell',
                    'route_name' => 'products.by.brand',
                    'icon' => 'fas fa-building',
                    'location' => 'main',
                    'active' => true,
                    'order' => 1,
                    'parent_id' => $laptopsMenu->id
                ]);
                
                Log::info('Created Dell submenu', ['id' => $dellMenu->id]);
            }
            
            // Check if we already have an XPS submenu
            $xpsMenu = \App\Models\MenuItem::where('name', 'XPS Series')
                ->where('parent_id', $dellMenu->id)
                ->first();
                
            if (!$xpsMenu) {
                // Create the model submenu
                $xpsMenu = \App\Models\MenuItem::create([
                    'name' => 'XPS Series',
                    'url' => '/laptop-by-brand/dell/xps',
                    'route_name' => 'products.by.brand.model',
                    'icon' => 'fas fa-laptop-code',
                    'location' => 'main',
                    'active' => true,
                    'order' => 1,
                    'parent_id' => $dellMenu->id
                ]);
                
                Log::info('Created XPS Series submenu', ['id' => $xpsMenu->id]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Test menu items created successfully',
                'structure' => [
                    'parent' => $laptopsMenu->toArray(),
                    'child' => $dellMenu->toArray(),
                    'grandchild' => $xpsMenu->toArray()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error creating test menu items', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating test menu items: ' . $e->getMessage()
            ], 500);
        }
    }
}
