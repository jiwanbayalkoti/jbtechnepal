<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of menu items.
     */
    public function index(Request $request)
    {
        $location = $request->input('location');
        
        $mainQuery = MenuItem::where('location', 'main');
        $footerQuery = MenuItem::where('location', 'footer');
        
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
        $parentMenuItems = MenuItem::whereNull('parent_id')->get();
        
        return view('admin.menus.create', compact('parentMenuItems'));
    }
    
    /**
     * Store a newly created menu item in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
            'name' => 'required|string|max:255',
                'url' => 'nullable|string|max:255',
                'route_name' => 'nullable|string|max:255',
                'icon' => 'nullable|string|max:50',
                'location' => 'required|string|max:255',
                'order' => 'required|integer',
                'active' => 'required|boolean',
                'parent_id' => 'nullable|integer|exists:menu_items,id',
            ]);
            
            // Ensure either url or route_name is provided
            if (empty($validated['url']) && empty($validated['route_name'])) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Either URL or Route Name must be provided'
                    ], 422);
                }
                
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'Either URL or Route Name must be provided']);
            }
            
            // Create the menu item
            $menuItem = MenuItem::create($validated);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Menu item '{$menuItem->name}' created successfully.",
                    'menuItem' => $menuItem
                ]);
            }
            
            return redirect()->route('admin.menus.index')
                ->with('success', "Menu item '{$menuItem->name}' created successfully.");
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error creating menu item: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating menu item: ' . $e->getMessage()
                ], 500);
            }
        
        return redirect()->route('admin.menus.index')
                ->with('error', 'Error creating menu item: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for editing the specified menu item.
     */
    public function edit(string $id)
    {
        try {
        $menuItem = MenuItem::findOrFail($id);
            
            // Get potential parent items of the same location, excluding itself and its children
        $parentMenuItems = MenuItem::whereNull('parent_id')
            ->where('id', '!=', $id)
                ->where('location', $menuItem->location)
            ->get();
        
            if (request()->ajax() || request()->wantsJson()) {
                // For AJAX requests, return JSON with the rendered form HTML
                return response()->json([
                    'success' => true,
                    'html' => view('admin.menus.edit-form', compact('menuItem', 'parentMenuItems'))->render(),
                    'menuItem' => $menuItem
                ]);
            }
            
            // For regular requests, return the full edit page view
        return view('admin.menus.edit', compact('menuItem', 'parentMenuItems'));
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error in menu edit: ' . $e->getMessage());
            
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
    public function update(Request $request, MenuItem $menu)
    {
        try {
            // Validate the request
            $validated = $request->validate([
            'name' => 'required|string|max:255',
                'url' => 'nullable|string|max:255',
                'route_name' => 'nullable|string|max:255',
                'icon' => 'nullable|string|max:50',
                'location' => 'required|string|max:255',
                'order' => 'required|integer',
                'active' => 'required|boolean',
                'parent_id' => 'nullable|integer|exists:menu_items,id',
            ]);
            
            // Ensure either url or route_name is provided
            if (empty($validated['url']) && empty($validated['route_name'])) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Either URL or Route Name must be provided'
                    ], 422);
                }
                
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'Either URL or Route Name must be provided']);
            }
            
            // Ensure a menu item cannot be its own parent
            if (!empty($validated['parent_id']) && $validated['parent_id'] == $menu->id) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A menu item cannot be its own parent'
                    ], 422);
                }
                
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['parent_id' => 'A menu item cannot be its own parent']);
            }
            
            // Update the menu item
            $menu->update($validated);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Menu item '{$menu->name}' updated successfully."
                ]);
            }
            
            return redirect()->route('admin.menus.index')
                ->with('success', "Menu item '{$menu->name}' updated successfully.");
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error updating menu item: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating menu item: ' . $e->getMessage()
                ], 500);
            }
        
        return redirect()->route('admin.menus.index')
                ->with('error', 'Error updating menu item: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified menu item from storage.
     */
    public function destroy(MenuItem $menu)
    {
        try {
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
            $menu->delete();
            
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
}
