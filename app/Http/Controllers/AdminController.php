<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\SpecificationType;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function dashboard()
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $recentProducts = Product::with('category')->latest()->take(5)->get();
        $categories = Category::all();
        $parentMenuItems = MenuItem::whereNull('parent_id')->get();
        
        // Get SEO and Contact settings
        $seoSettings = \App\Models\Setting::where('group', 'seo')->first();
        $contactSettings = \App\Models\Setting::where('group', 'contact')->first();
        
        return view('admin.dashboard', compact(
            'totalProducts', 
            'totalCategories', 
            'recentProducts', 
            'categories', 
            'parentMenuItems',
            'seoSettings',
            'contactSettings'
        ));
    }
    
    /**
     * Display specification types for a category.
     */
    public function specificationTypes($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $specificationTypes = SpecificationType::where('category_id', $categoryId)
            ->orderBy('display_order')
            ->get();
            
        return view('admin.specifications.index', compact('category', 'specificationTypes'));
    }
    
    /**
     * Show the form for creating a new specification type.
     */
    public function createSpecificationType($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        return view('admin.specifications.create', compact('category'));
    }
    
    /**
     * Store a newly created specification type.
     */
    public function storeSpecificationType(Request $request, $categoryId)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'unit' => 'nullable|string|max:50',
                'is_comparable' => 'boolean',
                'display_order' => 'nullable|integer'
            ]);
            
            $category = Category::findOrFail($categoryId);
            
            $specificationType = new SpecificationType([
                'name' => $request->name,
                'unit' => $request->unit,
                'is_comparable' => $request->has('is_comparable'),
                'display_order' => $request->display_order ?? 0,
                'category_id' => $category->id
            ]);
            
            $specificationType->save();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Specification type '{$specificationType->name}' created successfully."
                ]);
            }
            
            return redirect()->route('admin.specifications', $category->id)
                ->with('success', 'Specification type created successfully');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error creating specification type: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating specification type: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.specifications', $categoryId)
                ->with('error', 'Error creating specification type: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for editing a specification type.
     */
    public function editSpecificationType($categoryId, $id)
    {
        try {
            $category = Category::findOrFail($categoryId);
            $specificationType = SpecificationType::findOrFail($id);
            
            if (request()->ajax() || request()->wantsJson()) {
                // For AJAX requests, return JSON with the rendered form HTML
                return response()->json([
                    'success' => true,
                    'html' => view('admin.specifications.edit-form', compact('category', 'specificationType'))->render(),
                    'specificationType' => $specificationType
                ]);
            }
            
            // For regular requests, return the full edit page view
            return view('admin.specifications.edit', compact('category', 'specificationType'));
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error in specification type edit: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading specification type: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.specifications', $categoryId)
                ->with('error', 'Error loading specification type: ' . $e->getMessage());
        }
    }
    
    /**
     * Update the specified specification type.
     */
    public function updateSpecificationType(Request $request, $categoryId, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'unit' => 'nullable|string|max:50',
                'is_comparable' => 'boolean',
                'display_order' => 'nullable|integer'
            ]);
            
            $category = Category::findOrFail($categoryId);
            $specificationType = SpecificationType::findOrFail($id);
            
            $specificationType->name = $request->name;
            $specificationType->unit = $request->unit;
            $specificationType->is_comparable = $request->has('is_comparable');
            $specificationType->display_order = $request->display_order ?? 0;
            
            $specificationType->save();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Specification type '{$specificationType->name}' updated successfully."
                ]);
            }
            
            return redirect()->route('admin.specifications', $category->id)
                ->with('success', 'Specification type updated successfully');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error updating specification type: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating specification type: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.specifications', $categoryId)
                ->with('error', 'Error updating specification type: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified specification type.
     */
    public function destroySpecificationType($categoryId, $id)
    {
        try {
            $specificationType = SpecificationType::findOrFail($id);
            $specTypeName = $specificationType->name;
            $specificationType->delete();
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Specification type '{$specTypeName}' deleted successfully."
                ]);
            }
            
            return redirect()->route('admin.specifications', $categoryId)
                ->with('success', 'Specification type deleted successfully');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error deleting specification type: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting specification type: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.specifications', $categoryId)
                ->with('error', 'Error deleting specification type: ' . $e->getMessage());
        }
    }
}
