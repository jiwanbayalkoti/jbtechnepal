<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApiMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Get the menu item by ID
            $menu = MenuItem::findOrFail($id);
            
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

            // Handle booleans
            $validated['active'] = $request->has('active') ? (bool)$request->input('active') : false;
            $validated['is_mega_menu'] = $request->has('is_mega_menu') ? (bool)$request->input('is_mega_menu') : false;
            $validated['is_dynamic_page'] = $request->has('is_dynamic_page') ? (bool)$request->input('is_dynamic_page') : false;
            
            $shouldGenerateModels = $request->boolean('auto_generate_models');
            $brandSlug = $request->brand_for_url ?? 'all';
            $categoryId = $validated['category_id'] ?? null;
            $categorySlug = null;
            
            // Remove fields that aren't stored in the database
            if (isset($validated['brand_for_url'])) {
                unset($validated['brand_for_url']);
            }
            
            if (isset($validated['auto_generate_models'])) {
                unset($validated['auto_generate_models']);
            }
            
            // Update the menu item
            $menu->update($validated);
            
            // Clear menu cache
            $this->clearMenuCache($menu->location);
            
            return response()->json([
                'success' => true,
                'message' => "Menu item '{$menu->name}' updated successfully!",
                'menu' => $menu
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error updating menu item: ' . $e->getMessage(), [
                'menu_id' => $id,
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating menu item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
}
