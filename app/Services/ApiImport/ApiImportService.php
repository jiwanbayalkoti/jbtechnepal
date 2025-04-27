<?php

namespace App\Services\ApiImport;

use App\Models\Product;
use App\Models\Category;
use App\Models\SpecificationType;
use App\Models\ProductSpecification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

abstract class ApiImportService
{
    /**
     * The API endpoint for this brand
     *
     * @var string
     */
    protected $apiEndpoint;
    
    /**
     * API credentials
     *
     * @var array
     */
    protected $credentials = [];
    
    /**
     * Category mapping from API to local system
     *
     * @var array
     */
    protected $categoryMapping = [];
    
    /**
     * Specification mapping from API to local system
     *
     * @var array
     */
    protected $specificationMapping = [];

    /**
     * Abstract method to fetch products from the API
     *
     * @return array
     */
    abstract public function fetchProducts(): array;
    
    /**
     * Abstract method to transform product data from API format to system format
     *
     * @param array $apiProduct
     * @return array
     */
    abstract protected function transformProduct(array $apiProduct): array;
    
    /**
     * Import products from API to the system
     *
     * @param array $options
     * @return array
     */
    public function importProducts(array $options = []): array
    {
        $stats = [
            'total' => 0,
            'created' => 0,
            'updated' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        try {
            $products = $this->fetchProducts();
            $stats['total'] = count($products);
            
            foreach ($products as $apiProduct) {
                try {
                    $productData = $this->transformProduct($apiProduct);
                    
                    // Check if product already exists (by brand and model or other unique identifier)
                    $existingProduct = null;
                    if (!empty($productData['brand']) && !empty($productData['model'])) {
                        $existingProduct = Product::where('brand', $productData['brand'])
                            ->where('model', $productData['model'])
                            ->first();
                    }
                    
                    if ($existingProduct) {
                        // Update existing product
                        $existingProduct->update($productData);
                        
                        // Update specifications
                        if (!empty($productData['specifications'])) {
                            // Delete existing specifications
                            ProductSpecification::where('product_id', $existingProduct->id)->delete();
                            
                            // Create new specifications
                            foreach ($productData['specifications'] as $specTypeId => $value) {
                                if (!empty($value)) {
                                    ProductSpecification::create([
                                        'product_id' => $existingProduct->id,
                                        'specification_type_id' => $specTypeId,
                                        'value' => $value
                                    ]);
                                }
                            }
                        }
                        
                        $stats['updated']++;
                    } else {
                        // Create new product
                        $product = new Product($productData);
                        $product->save();
                        
                        // Save specifications
                        if (!empty($productData['specifications'])) {
                            foreach ($productData['specifications'] as $specTypeId => $value) {
                                if (!empty($value)) {
                                    ProductSpecification::create([
                                        'product_id' => $product->id,
                                        'specification_type_id' => $specTypeId,
                                        'value' => $value
                                    ]);
                                }
                            }
                        }
                        
                        $stats['created']++;
                    }
                } catch (\Exception $e) {
                    $stats['failed']++;
                    $stats['errors'][] = 'Error processing product: ' . $e->getMessage();
                    Log::error('API Import Error: ' . $e->getMessage(), [
                        'product' => $apiProduct ?? null,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        } catch (\Exception $e) {
            $stats['errors'][] = 'API fetch error: ' . $e->getMessage();
            Log::error('API Import Fetch Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        return $stats;
    }
    
    /**
     * Map API categories to local system categories
     *
     * @param string $apiCategory
     * @return int|null
     */
    protected function mapCategory(string $apiCategory): ?int
    {
        return $this->categoryMapping[$apiCategory] ?? null;
    }
    
    /**
     * Map API specifications to local system specification types
     *
     * @param string $apiSpecification
     * @param int $categoryId
     * @return int|null
     */
    protected function mapSpecification(string $apiSpecification, int $categoryId): ?int
    {
        $key = "{$categoryId}:{$apiSpecification}";
        return $this->specificationMapping[$key] ?? null;
    }
    
    /**
     * Download and store an image from a URL
     *
     * @param string $imageUrl
     * @return string|null Path to stored image
     */
    protected function downloadImage(string $imageUrl): ?string
    {
        try {
            $response = Http::get($imageUrl);
            
            if ($response->successful()) {
                $filename = 'products/' . Str::random(40) . '.jpg';
                $path = storage_path('app/public/' . $filename);
                
                file_put_contents($path, $response->body());
                
                return $filename;
            }
        } catch (\Exception $e) {
            Log::error('Image download failed: ' . $e->getMessage(), [
                'url' => $imageUrl
            ]);
        }
        
        return null;
    }
} 