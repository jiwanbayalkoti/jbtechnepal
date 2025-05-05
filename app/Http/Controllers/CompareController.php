<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\SpecificationType;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    /**
     * Display the homepage with categories
     */
    public function index()
    {
        $categories = Category::all();
        return view('home', compact('categories'));
    }
    
    /**
     * Display products by category
     */
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $products = Product::where('category_id', $category->id)->get();
        
        return view('category', compact('category', 'products'));
    }
    
    /**
     * Display product details
     */
    public function product($slug)
    {
        $product = Product::where('slug', $slug)
            ->with(['category', 'subcategory', 'images', 'specifications.specificationType'])
            ->firstOrFail();
            
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['images'])
            ->take(4)
            ->get();
            
        // Get products from the same brand
        $brandProducts = Product::where('brand', $product->brand)
            ->where('id', '!=', $product->id)
            ->with(['images'])
            ->take(4)
            ->get();
            
        return view('products.show', compact('product', 'relatedProducts', 'brandProducts'));
    }
    
    /**
     * Show the comparison page.
     */
    public function compare()
    {
        $compareList = session('compare_list', []);
        $products = [];
        $specTypes = collect(); // Initialize as empty collection instead of null
        
        if (!empty($compareList)) {
            $products = Product::whereIn('id', $compareList)
                ->with(['category', 'subcategory', 'images', 'specifications.specificationType'])
                ->get();
                
            // Get all specification types used by these products
            $specTypeIds = [];
            foreach ($products as $product) {
                // Ensure specifications is not null
                if ($product->specifications) {
                    foreach ($product->specifications as $spec) {
                        if ($spec->specification_type_id) {
                            $specTypeIds[] = $spec->specification_type_id;
                        }
                    }
                }
            }
            
            // Get unique specification types
            if (!empty($specTypeIds)) {
                $specTypes = SpecificationType::whereIn('id', array_unique($specTypeIds))
                    ->orderBy('name')
                    ->get();
            }
        }
        
        return view('compare.index', compact('products', 'specTypes'));
    }
    
    /**
     * Add product to compare list.
     */
    public function addToCompare(Request $request)
    {
        $productId = $request->product_id;
        $compareList = session()->get('compare_list', []);
        
        // Check if product already exists in the compare list
        if (!in_array($productId, $compareList)) {
            // Limit to 4 products maximum
            if (count($compareList) >= 4) {
                return redirect()->back()->with('error', 'You can compare a maximum of 4 products at a time.');
            }
            
            // Add product to compare list
            $compareList[] = $productId;
            session()->put('compare_list', $compareList);
            
            return redirect()->back()->with('success', 'Product added to comparison list.');
        }
        
        return redirect()->back()->with('info', 'Product is already in your comparison list.');
    }
    
    /**
     * Remove product from compare list.
     */
    public function removeFromCompare(Request $request)
    {
        $productId = $request->product_id;
        $compareList = session()->get('compare_list', []);
        
        // Remove product from compare list
        $compareList = array_diff($compareList, [$productId]);
        session()->put('compare_list', $compareList);
        
        return redirect()->back()->with('success', 'Product removed from comparison list.');
    }
    
    /**
     * Clear all items from compare list.
     */
    public function clearCompare()
    {
        session()->forget('compare_list');
        
        return redirect()->back()->with('success', 'Comparison list cleared.');
    }
    
    /**
     * Add product to compare list via AJAX.
     */
    public function addToCompareAjax($id)
    {
        $compareList = session()->get('compare_list', []);
        
        // Check if product already exists in the compare list
        if (!in_array($id, $compareList)) {
            // Limit to 4 products maximum
            if (count($compareList) >= 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can compare a maximum of 4 products at a time.'
                ], 400);
            }
            
            // Add product to compare list
            $compareList[] = $id;
            session()->put('compare_list', $compareList);
            
            return response()->json([
                'success' => true,
                'message' => 'Product added to comparison list.',
                'count' => count($compareList)
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Product is already in your comparison list.',
            'count' => count($compareList)
        ]);
    }
    
    /**
     * Remove product from compare list via AJAX.
     */
    public function removeFromCompareAjax($id)
    {
        $compareList = session()->get('compare_list', []);
        
        // Remove product from compare list
        $compareList = array_diff($compareList, [$id]);
        session()->put('compare_list', $compareList);
        
        return response()->json([
            'success' => true,
            'message' => 'Product removed from comparison list.',
            'count' => count($compareList)
        ]);
    }
    
    /**
     * Clear all items from compare list via AJAX.
     */
    public function clearCompareAjax()
    {
        session()->forget('compare_list');
        
        return response()->json([
            'success' => true,
            'message' => 'Comparison list cleared.',
            'count' => 0
        ]);
    }

    /**
     * Get AI-powered product recommendations based on product specifications.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAiRecommendations(Request $request)
    {
        try {
            \Log::info('AI Recommendations requested', [
                'product_ids' => $request->product_ids,
                'request_data' => $request->all()
            ]);

            // Validate request
            $request->validate([
                'product_ids' => 'required|array|min:1',
                'product_ids.*' => 'required|integer|exists:products,id'
            ]);

            // Get the products being compared
            $products = Product::whereIn('id', $request->product_ids)
                ->with(['specifications.specificationType', 'category', 'subcategory'])
                ->get();

            \Log::info('Products retrieved', [
                'count' => $products->count(),
                'product_ids' => $products->pluck('id')->toArray()
            ]);

            if ($products->isEmpty()) {
                \Log::warning('No products found for comparison');
                return response()->json([
                    'success' => false,
                    'message' => 'No products found for comparison.'
                ]);
            }

            // Check if products have specifications
            foreach ($products as $product) {
                \Log::info('Product specifications', [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'specifications_count' => $product->specifications->count(),
                    'specifications' => $product->specifications->map(function($spec) {
                        return [
                            'id' => $spec->id,
                            'type_id' => $spec->specification_type_id,
                            'type_name' => $spec->specificationType ? $spec->specificationType->name : 'N/A',
                            'value' => $spec->value
                        ];
                    })->toArray()
                ]);
            }

            // Generate AI recommendations based on product specifications
            $recommendations = $this->generateAiRecommendations($products);

            \Log::info('Recommendations generated', [
                'summary' => $recommendations['summary'],
                'recommendations_count' => count($recommendations['recommendations'])
            ]);

            return response()->json([
                'success' => true,
                'summary' => $recommendations['summary'],
                'recommendations' => $recommendations['recommendations'],
                'overall_recommendation' => $recommendations['overall_recommendation']
            ]);
        } catch (\Exception $e) {
            \Log::error('AI Recommendation Error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while generating recommendations. Please try again.',
                'debug_message' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Generate AI recommendations based on products.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $products
     * @return array
     */
    private function generateAiRecommendations($products)
    {
        try {
            // Define criteria weights for different use cases
            $useCases = [
                'gaming' => [
                    'processor_speed' => 0.25,
                    'ram' => 0.20,
                    'graphics' => 0.25,
                    'storage' => 0.15,
                    'display' => 0.15
                ],
                'professional' => [
                    'processor_speed' => 0.30,
                    'ram' => 0.25,
                    'storage' => 0.20,
                    'display' => 0.15,
                    'battery' => 0.10
                ],
                'entertainment' => [
                    'display' => 0.30,
                    'audio' => 0.25,
                    'storage' => 0.20,
                    'battery' => 0.15,
                    'processor_speed' => 0.10
                ],
                'budget' => [
                    'price' => 0.40,
                    'processor_speed' => 0.20,
                    'ram' => 0.20,
                    'storage' => 0.20
                ]
            ];

            $recommendations = [];
            $bestForUseCase = [];

            // Extract specifications for each product
            $productSpecs = [];
            foreach ($products as $product) {
                $specs = [];
                foreach ($product->specifications as $spec) {
                    if ($spec->specificationType) {
                        $specs[$spec->specificationType->name] = $spec->value;
                    }
                }
                $productSpecs[$product->id] = [
                    'name' => $product->name,
                    'brand' => $product->brand,
                    'model' => $product->model,
                    'price' => $product->price,
                    'specs' => $specs
                ];
            }

            \Log::info('Product specifications extracted', [
                'product_specs' => $productSpecs
            ]);

            foreach ($useCases as $useCase => $weights) {
                $scores = [];
                foreach ($productSpecs as $productId => $productData) {
                    $score = 0;
                    foreach ($weights as $criterion => $weight) {
                        $score += $this->evaluateCriterion($productData, $criterion) * $weight;
                    }
                    $scores[$productId] = $score;
                }

                \Log::info('Scores calculated for use case', [
                    'use_case' => $useCase,
                    'scores' => $scores
                ]);

                // Find best product for this use case
                arsort($scores);
                $bestProductId = array_key_first($scores);
                $bestProduct = $products->firstWhere('id', $bestProductId);
                
                if ($bestProduct) {
                    $bestForUseCase[$useCase] = [
                        'product' => $bestProduct,
                        'score' => $scores[$bestProductId]
                    ];
                }
            }

            // Generate recommendations for each product
            foreach ($products as $product) {
                $productData = $productSpecs[$product->id];
                $pros = $this->generatePros($product);
                $cons = $this->generateCons($product);
                
                // Find best use case for this product
                $bestUseCase = '';
                $highestScore = 0;
                foreach ($useCases as $useCase => $weights) {
                    $score = 0;
                    foreach ($weights as $criterion => $weight) {
                        $score += $this->evaluateCriterion($productData, $criterion) * $weight;
                    }
                    if ($score > $highestScore) {
                        $highestScore = $score;
                        $bestUseCase = $useCase;
                    }
                }

                $explanation = "This product is best suited for " . ucfirst($bestUseCase) . " use. ";
                $explanation .= $this->getUseCaseExplanation($productData, $bestUseCase);

                $recommendations[] = [
                    'product_name' => $product->name,
                    'explanation' => $explanation,
                    'pros' => $pros,
                    'cons' => $cons,
                    'best_for' => $bestUseCase
                ];
            }

            // Generate summary
            $summary = "Based on our analysis:\n";
            foreach ($bestForUseCase as $useCase => $data) {
                if (isset($data['product'])) {
                    $summary .= "• For " . ucfirst($useCase) . ": " . $data['product']->name . "\n";
                }
            }

            // Generate overall recommendation
            $overallRecommendation = "Each product excels in different areas:\n";
            foreach ($recommendations as $rec) {
                $overallRecommendation .= "• " . $rec['product_name'] . " is ideal for " . ucfirst($rec['best_for']) . " use\n";
            }

            return [
                'summary' => $summary,
                'recommendations' => $recommendations,
                'overall_recommendation' => $overallRecommendation
            ];
        } catch (\Exception $e) {
            \Log::error('Error in generateAiRecommendations: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function evaluateCriterion($productData, $criterion)
    {
        try {
            $specs = $productData['specs'];
            $price = $productData['price'];
            
            switch ($criterion) {
                case 'processor_speed':
                    if (isset($specs['Processor'])) {
                        if (preg_match('/(\d+\.?\d*)\s*GHz/i', $specs['Processor'], $matches)) {
                            $speed = floatval($matches[1]);
                            return min(1, $speed / 4.0);
                        }
                    }
                    return 0.5;
                    
                case 'ram':
                    if (isset($specs['RAM'])) {
                        if (preg_match('/(\d+)\s*GB/i', $specs['RAM'], $matches)) {
                            $ram = intval($matches[1]);
                            return min(1, $ram / 32.0);
                        }
                    }
                    return 0.5;
                    
                case 'storage':
                    if (isset($specs['Storage'])) {
                        if (preg_match('/(\d+)\s*(GB|TB)/i', $specs['Storage'], $matches)) {
                            $storage = intval($matches[1]);
                            if (strtoupper($matches[2]) === 'TB') {
                                $storage *= 1024;
                            }
                            return min(1, $storage / 2048.0);
                        }
                    }
                    return 0.5;
                    
                case 'battery':
                    if (isset($specs['Battery'])) {
                        if (preg_match('/(\d+)\s*mAh/i', $specs['Battery'], $matches)) {
                            $battery = intval($matches[1]);
                            return min(1, $battery / 6000.0);
                        }
                    }
                    return 0.5;
                    
                case 'price':
                    if ($price > 0) {
                        return max(0, 1 - ($price / 5000.0));
                    }
                    return 0.5;
                    
                case 'graphics':
                    if (isset($specs['Graphics'])) {
                        if (stripos($specs['Graphics'], 'dedicated') !== false || 
                            stripos($specs['Graphics'], 'discrete') !== false) {
                            return 1.0;
                        }
                        return 0.7;
                    }
                    return 0.5;
                    
                case 'display':
                    if (isset($specs['Display'])) {
                        if (preg_match('/(\d+)x(\d+)/', $specs['Display'], $matches)) {
                            $width = intval($matches[1]);
                            $height = intval($matches[2]);
                            $pixels = $width * $height;
                            return min(1, $pixels / (3840 * 2160));
                        }
                    }
                    return 0.5;
                    
                case 'audio':
                    if (isset($specs['Audio'])) {
                        if (stripos($specs['Audio'], 'Dolby') !== false || 
                            stripos($specs['Audio'], 'Harman') !== false ||
                            stripos($specs['Audio'], 'Bang & Olufsen') !== false) {
                            return 1.0;
                        }
                        return 0.7;
                    }
                    return 0.5;
                    
                default:
                    return 0.5;
            }
        } catch (\Exception $e) {
            \Log::error('Error in evaluateCriterion: ' . $e->getMessage(), [
                'criterion' => $criterion,
                'productData' => $productData
            ]);
            return 0.5;
        }
    }

    private function getUseCaseExplanation($productData, $useCase)
    {
        try {
            $specs = $productData['specs'];
            
            switch ($useCase) {
                case 'gaming':
                    return "It features " . 
                           (isset($specs['Processor']) ? "a powerful " . $specs['Processor'] . " processor, " : "") .
                           (isset($specs['Graphics']) ? "dedicated " . $specs['Graphics'] . " graphics, " : "") .
                           (isset($specs['RAM']) ? "and " . $specs['RAM'] . " of RAM " : "") .
                           "making it perfect for gaming.";
                    
                case 'professional':
                    return "It offers " . 
                           (isset($specs['Processor']) ? "a high-performance " . $specs['Processor'] . " processor, " : "") .
                           (isset($specs['RAM']) ? "generous " . $specs['RAM'] . " of RAM, " : "") .
                           (isset($specs['Storage']) ? "and " . $specs['Storage'] . " of storage " : "") .
                           "ideal for professional work.";
                    
                case 'entertainment':
                    return "It provides " . 
                           (isset($specs['Display']) ? "a stunning " . $specs['Display'] . " display, " : "") .
                           (isset($specs['Audio']) ? "excellent " . $specs['Audio'] . " audio, " : "") .
                           (isset($specs['Battery']) ? "and " . $specs['Battery'] . " battery life " : "") .
                           "perfect for entertainment.";
                    
                case 'budget':
                    return "It delivers " . 
                           (isset($specs['Processor']) ? "a capable " . $specs['Processor'] . " processor " : "") .
                           "at an affordable price point, offering great value for money.";
                    
                default:
                    return "It provides a balanced set of features suitable for various uses.";
            }
        } catch (\Exception $e) {
            \Log::error('Error in getUseCaseExplanation: ' . $e->getMessage(), [
                'useCase' => $useCase,
                'productData' => $productData
            ]);
            return "This product offers a good balance of features.";
        }
    }

    /**
     * Generate pros for a product.
     *
     * @param  \App\Models\Product  $product
     * @return array
     */
    private function generatePros($product)
    {
        $pros = [];
        $specs = $product->specifications->pluck('value', 'specificationType.name')->toArray();
        
        // Common pros
        if ($product->price < 1000) {
            $pros[] = "Affordable price point";
        }
        
        if (isset($specs['Processor']) && stripos($specs['Processor'], 'Intel i7') !== false || 
            stripos($specs['Processor'], 'AMD Ryzen 7') !== false) {
            $pros[] = "Powerful processor";
        }
        
        if (isset($specs['RAM']) && preg_match('/(\d+)\s*GB/i', $specs['RAM'], $matches) && intval($matches[1]) >= 16) {
            $pros[] = "Ample RAM for multitasking";
        }
        
        if (isset($specs['Storage']) && (stripos($specs['Storage'], 'SSD') !== false || 
            stripos($specs['Storage'], 'NVMe') !== false)) {
            $pros[] = "Fast SSD storage";
        }
        
        if (isset($specs['Display']) && (stripos($specs['Display'], '4K') !== false || 
            stripos($specs['Display'], 'Retina') !== false)) {
            $pros[] = "High-resolution display";
        }
        
        if (isset($specs['Battery']) && preg_match('/(\d+)\s*mAh/i', $specs['Battery'], $matches) && 
            intval($matches[1]) > 4000) {
            $pros[] = "Long battery life";
        }
        
        // Ensure we have at least 3 pros
        while (count($pros) < 3) {
            $pros[] = "Good overall performance";
        }
        
        return array_slice($pros, 0, 5); // Return top 5 pros
    }

    /**
     * Generate cons for a product.
     *
     * @param  \App\Models\Product  $product
     * @return array
     */
    private function generateCons($product)
    {
        $cons = [];
        $specs = $product->specifications->pluck('value', 'specificationType.name')->toArray();
        
        // Common cons
        if ($product->price > 2000) {
            $cons[] = "Premium price tag";
        }
        
        if (isset($specs['RAM']) && preg_match('/(\d+)\s*GB/i', $specs['RAM'], $matches) && intval($matches[1]) < 8) {
            $cons[] = "Limited RAM may affect performance";
        }
        
        if (isset($specs['Storage']) && preg_match('/(\d+)\s*(GB|TB)/i', $specs['Storage'], $matches)) {
            $storage = intval($matches[1]);
            if (strtoupper($matches[2]) === 'TB') {
                $storage *= 1024;
            }
            if ($storage < 256) {
                $cons[] = "Limited storage capacity";
            }
        }
        
        if (isset($specs['Battery']) && preg_match('/(\d+)\s*mAh/i', $specs['Battery'], $matches) && 
            intval($matches[1]) < 3000) {
            $cons[] = "Limited battery life";
        }
        
        // Ensure we have at least 2 cons
        while (count($cons) < 2) {
            $cons[] = "May not excel in all aspects";
        }
        
        return array_slice($cons, 0, 3); // Return top 3 cons
    }
}
