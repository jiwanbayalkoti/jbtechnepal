<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    /**
     * Display a listing of all brands.
     */
    public function index()
    {
        $brands = Brand::where('is_active', true)
            ->withCount(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('name')
            ->paginate(24);
            
        return view('brands.index', compact('brands'));
    }

    /**
     * Display the specified brand.
     */
    public function show(Request $request, $slug)
    {
        $brand = Brand::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
        
        $query = Product::where('brand', $brand->name)
            ->where('is_active', true)
            ->with('images');
        
        // Apply sorting
        switch ($request->get('sort')) {
            case 'price_low':
                $query->orderBy('discount_price', 'asc')
                      ->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('discount_price', 'desc')
                      ->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        $products = $query->paginate(12)->withQueryString();
            
        return view('brands.show', compact('brand', 'products'));
    }
} 