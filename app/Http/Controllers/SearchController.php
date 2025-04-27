<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Display search results.
     */
    public function index(Request $request)
    {
        // Get all categories and brands for the filter options
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $brands = Product::select('brand')->distinct()->whereNotNull('brand')->orderBy('brand')->get();
        
        // Start with a base query
        $query = Product::query();

        // Apply search term filter
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('brand', 'like', "%{$searchTerm}%")
                  ->orWhere('model', 'like', "%{$searchTerm}%");
            });
        }

        // Apply category filter (multiple categories)
        if ($request->has('category') && !empty($request->category)) {
            if (is_array($request->category)) {
                $query->whereIn('category_id', $request->category);
            } else {
                $query->where('category_id', $request->category);
            }
        }

        // Apply subcategory filter (multiple subcategories)
        if ($request->has('subcategory') && !empty($request->subcategory)) {
            if (is_array($request->subcategory)) {
                $query->whereIn('subcategory_id', $request->subcategory);
            } else {
                $query->where('subcategory_id', $request->subcategory);
            }
        }

        // Apply brand filter (multiple brands)
        if ($request->has('brand') && !empty($request->brand)) {
            if (is_array($request->brand)) {
                $query->whereIn('brand', $request->brand);
            } else {
                $query->where('brand', $request->brand);
            }
        }

        // Apply price range filter
        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort results
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('name', 'asc');
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        // Get the products with pagination
        $products = $query->with(['category', 'subcategory'])->paginate(12);

        // Keep the filters in the pagination links
        $products->appends($request->all());

        return view('search', compact('products', 'categories', 'subcategories', 'brands'));
    }
} 