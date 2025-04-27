<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Models\Advertisement;
use App\Models\Page;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCategories = Category::where('is_featured', true)->take(6)->get();
        $latestProducts = Product::with(['category', 'subcategory', 'images'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();
        $randomProducts = Product::with(['category', 'subcategory', 'images'])
            ->inRandomOrder()
            ->take(4)
            ->get();
        
        // Get subcategories for filters
        $subcategories = SubCategory::all();
        
        // Get brands for filters
        $brands = Product::select('brand')->distinct()->whereNotNull('brand')->orderBy('brand')->get();
        
        // Get homepage content
        $homePage = Page::where('slug', 'home')->first();
        
        // Get featured features
        $features = $this->getFeaturedFeatures();
        
        return view('home', compact(
            'featuredCategories', 
            'latestProducts', 
            'randomProducts',
            'subcategories',
            'brands',
            'homePage',
            'features'
        ));
    }
    
    /**
     * Get featured features for homepage.
     */
    private function getFeaturedFeatures()
    {
        $features = [
            [
                'icon' => 'fas fa-laptop-code',
                'title' => 'Advanced Comparison',
                'description' => 'Compare multiple products side by side with detailed specifications.',
            ],
            [
                'icon' => 'fas fa-chart-bar',
                'title' => 'Visual Insights',
                'description' => 'View performance metrics with interactive charts and graphs.',
            ],
            [
                'icon' => 'fas fa-sync-alt',
                'title' => 'Real-time Updates',
                'description' => 'Get the latest product information and price updates in real-time.',
            ],
            [
                'icon' => 'fas fa-user-check',
                'title' => 'User Reviews',
                'description' => 'Read authentic user reviews and ratings for each product.',
            ],
            [
                'icon' => 'fas fa-tags',
                'title' => 'Price Alerts',
                'description' => 'Get notified when prices drop for your favorite products.',
            ]
        ];
        
        return array_slice($features, 0, 3);
    }
    
    /**
     * Get the latest products for the slider.
     *
     * @param int $limit
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewestProducts($limit = 10)
    {
        try {
            $products = Product::with(['images', 'category'])
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'price' => $product->price,
                        'discount_price' => $product->discount_price,
                        'category' => $product->category ? $product->category->name : null,
                        'brand' => $product->brand,
                        'model' => $product->model,
                        'description' => $product->description,
                        'image' => $product->primary_image 
                            ? Storage::url($product->primary_image->path)
                            : ($product->images->isNotEmpty() 
                                ? Storage::url($product->images->first()->path)
                                : asset('img/no-image.png')),
                        'url' => route('product', $product->slug)
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching latest products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Load more products via AJAX
     */
    public function loadMoreProducts(Request $request)
    {
        try {
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
                $query->orderBy('created_at', 'desc'); // Default to newest
            }

            // Get the products with pagination
            $products = $query->with(['category', 'subcategory', 'images'])->paginate(6);
            $lastPage = $products->currentPage() >= $products->lastPage();

            // Generate HTML for products
            $html = '';
            foreach ($products as $product) {
                $html .= view('partials.product-item', compact('product'))->render();
            }

            return response()->json([
                'html' => $html,
                'lastPage' => $lastPage,
                'success' => true
            ]);
        } catch (\Exception $e) {
            \Log::error('Load more products error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading more products'
            ], 500);
        }
    }
    
    private function getPopularProductsData()
    {
        // In a real app, you'd probably have a view_count or popularity metric
        // Here we'll just take the 3 most recent products as a sample
        $products = Product::latest()->take(3)->get();
        
        // Generate sample comparison data
        return $products->map(function($product) {
            // Generate random scores between 75-95 for each metric
            $randomScores = collect(['Performance', 'Display', 'Camera', 'Battery', 'Value'])
                ->map(function($metric) {
                    return rand(75, 95);
                })->toArray();
            
            return [
                'name' => $product->name,
                'data' => $randomScores
            ];
        })->toArray();
    }

    /**
     * Handle AI-powered search suggestions
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function aiSearch(Request $request)
    {
        try {
            $query = $request->input('query');
            
            if (strlen($query) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter at least 2 characters'
                ]);
            }

            // Get products matching the search query
            $products = Product::where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->with(['category', 'brand'])
                ->take(5)
                ->get();

            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No products found matching your search'
                ]);
            }

            // Generate AI suggestions based on product specifications
            $suggestions = $this->generateSearchSuggestions($products, $query);

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);

        } catch (\Exception $e) {
            \Log::error('AI Search Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your search'
            ], 500);
        }
    }

    /**
     * Generate AI-powered search suggestions based on product specifications
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $products
     * @param  string  $query
     * @return array
     */
    private function generateSearchSuggestions($products, $query)
    {
        $suggestions = [];

        foreach ($products as $product) {
            // Extract relevant specifications
            $specs = $product->specifications->pluck('value', 'specificationType.name')->toArray();
            
            // Generate a smart suggestion based on product features
            $suggestion = [
                'id' => $product->id,
                'name' => $product->name,
                'brand' => $product->brand->name,
                'category' => $product->category->name,
                'highlight' => $this->generateHighlight($product, $query, $specs),
                'features' => $this->extractKeyFeatures($specs),
                'url' => route('products.show', $product->slug)
            ];

            $suggestions[] = $suggestion;
        }

        return $suggestions;
    }

    /**
     * Generate a highlighted suggestion based on product features
     *
     * @param  \App\Models\Product  $product
     * @param  string  $query
     * @param  array  $specs
     * @return string
     */
    private function generateHighlight($product, $query, $specs)
    {
        $highlights = [];

        // Check if query matches any specification
        foreach ($specs as $name => $value) {
            if (stripos($value, $query) !== false) {
                $highlights[] = "{$name}: {$value}";
            }
        }

        // If no specification matches, generate a general highlight
        if (empty($highlights)) {
            if (isset($specs['Processor'])) {
                $highlights[] = "Powered by {$specs['Processor']}";
            } elseif (isset($specs['Screen Size'])) {
                $highlights[] = "{$specs['Screen Size']} display";
            } elseif (isset($specs['RAM'])) {
                $highlights[] = "{$specs['RAM']} RAM";
            }
        }

        return !empty($highlights) ? implode(' | ', $highlights) : $product->description;
    }

    /**
     * Extract key features from product specifications
     *
     * @param  array  $specs
     * @return array
     */
    private function extractKeyFeatures($specs)
    {
        $features = [];
        $prioritySpecs = ['Processor', 'RAM', 'Storage', 'Screen Size', 'Battery', 'Camera'];

        foreach ($prioritySpecs as $spec) {
            if (isset($specs[$spec])) {
                $features[] = $specs[$spec];
            }
        }

        return array_slice($features, 0, 3);
    }
} 