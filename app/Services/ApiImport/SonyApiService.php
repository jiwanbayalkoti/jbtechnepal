<?php

namespace App\Services\ApiImport;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Category;

class SonyApiService extends ApiImportService
{
    protected $endpoint;
    protected $apiKey;
    protected $useMockData;
    protected $categoryMapping;
    protected $specificationMapping;

    public function __construct()
    {
        // Set API endpoint and credentials from config
        $this->endpoint = config('api_imports.sony.endpoint', 'https://api.sony.com/products');
        $this->apiKey = config('api_imports.sony.api_key');
        
        // Use mock data if enabled or if no API key is provided
        $this->useMockData = config('api_imports.sony.use_mock_data', true) || empty($this->apiKey);
        
        // Define category mapping (Sony API category name => Our system category ID)
        $this->categoryMapping = [
            'TV' => 'televisions',
            'CAMERA' => 'cameras',
            'AUDIO' => 'audio',
            'GAMING' => 'gaming',
            'MOBILE' => 'smartphones',
        ];
        
        // Define specification mapping (Sony API spec name => Our system spec name)
        $this->specificationMapping = [
            'screen_size' => 'screen_size',
            'resolution' => 'resolution',
            'processor' => 'processor',
            'memory' => 'ram',
            'storage' => 'storage',
            'camera' => 'camera',
            'battery' => 'battery',
            'dimensions' => 'dimensions',
            'weight' => 'weight',
        ];
    }

    /**
     * Fetch products from the Sony API
     *
     * @return array
     */
    public function fetchProducts(): array
    {
        $options = [];
        if ($this->useMockData) {
            Log::info('Using mock data for Sony products');
            return $this->getMockProducts();
        }
        
        try {
            // Prepare request parameters
            $params = [
                'apiKey' => $this->apiKey,
                'limit' => $options['limit'] ?? 100,
            ];
            
            if (!empty($options['categories'])) {
                $params['categories'] = implode(',', $options['categories']);
            }
            
            // Make the API request
            $response = Http::get($this->endpoint, $params);
            
            if ($response->successful()) {
                return $response->json()['products'] ?? [];
            } else {
                Log::error('Sony API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Error fetching Sony products: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get mock Sony products for testing
     */
    protected function getMockProducts()
    {
        return [
            [
                'id' => 'XBR55A8H',
                'name' => 'Sony A8H 55-inch OLED 4K Ultra HD Smart TV',
                'category' => 'TV',
                'description' => 'Experience the ultimate in picture quality with this Sony OLED TV featuring X-Motion Clarity technology and Acoustic Surface Audio.',
                'price' => 1499.99,
                'stock' => 15,
                'specifications' => [
                    'screen_size' => '55 inches',
                    'resolution' => '4K Ultra HD (3840 x 2160)',
                    'processor' => 'X1 Ultimate',
                    'dimensions' => '48.3 x 28.1 x 2.3 inches',
                    'weight' => '41.9 lbs',
                ],
                'images' => [
                    'https://example.com/images/sony/XBR55A8H_1.jpg',
                    'https://example.com/images/sony/XBR55A8H_2.jpg',
                ],
            ],
            [
                'id' => 'WH1000XM4',
                'name' => 'Sony WH-1000XM4 Wireless Noise-Cancelling Headphones',
                'category' => 'AUDIO',
                'description' => 'Industry-leading noise cancellation with premium sound quality and smart features like Speak-to-Chat.',
                'price' => 349.99,
                'stock' => 50,
                'specifications' => [
                    'battery' => 'Up to 30 hours',
                    'weight' => '8.96 oz',
                    'frequency_response' => '4Hz-40,000Hz',
                ],
                'images' => [
                    'https://example.com/images/sony/WH1000XM4_1.jpg',
                    'https://example.com/images/sony/WH1000XM4_2.jpg',
                ],
            ],
            [
                'id' => 'ALPHA7III',
                'name' => 'Sony Alpha a7 III Mirrorless Digital Camera',
                'category' => 'CAMERA',
                'description' => 'Full-frame mirrorless camera with outstanding imaging capability and high-speed performance.',
                'price' => 1999.99,
                'stock' => 8,
                'specifications' => [
                    'sensor' => '24.2MP Full-Frame Exmor R BSI CMOS Sensor',
                    'processor' => 'BIONZ X Image Processor',
                    'iso' => '100-51200 (Expandable to 50-204800)',
                    'weight' => '1.43 lbs',
                ],
                'images' => [
                    'https://example.com/images/sony/ALPHA7III_1.jpg',
                    'https://example.com/images/sony/ALPHA7III_2.jpg',
                ],
            ],
            [
                'id' => 'PS5',
                'name' => 'PlayStation 5 Console',
                'category' => 'GAMING',
                'description' => 'Experience lightning-fast loading with an ultra-high speed SSD, deeper immersion with haptic feedback, and adaptive triggers.',
                'price' => 499.99,
                'stock' => 5,
                'specifications' => [
                    'processor' => 'AMD Zen 2-based CPU with 8 cores at 3.5GHz',
                    'gpu' => 'Custom RDNA 2 AMD GPU',
                    'memory' => '16GB GDDR6',
                    'storage' => '825GB SSD',
                ],
                'images' => [
                    'https://example.com/images/sony/PS5_1.jpg',
                    'https://example.com/images/sony/PS5_2.jpg',
                ],
            ],
            [
                'id' => 'XPERIA1III',
                'name' => 'Sony Xperia 1 III Smartphone',
                'category' => 'MOBILE',
                'description' => 'World\'s first smartphone with a variable telephoto lens paired with a 4K HDR OLED 120Hz display.',
                'price' => 1299.99,
                'stock' => 12,
                'specifications' => [
                    'screen_size' => '6.5 inches',
                    'resolution' => '4K HDR OLED (3840 x 1644)',
                    'processor' => 'Qualcomm Snapdragon 888',
                    'memory' => '12GB',
                    'storage' => '256GB',
                    'camera' => '12MP triple lens with variable telephoto',
                    'battery' => '4500 mAh',
                ],
                'images' => [
                    'https://example.com/images/sony/XPERIA1III_1.jpg',
                    'https://example.com/images/sony/XPERIA1III_2.jpg',
                ],
            ],
        ];
    }

    /**
     * Transform product data from the Sony API format to our format
     *
     * @param array $apiProduct
     * @return array
     */
    protected function transformProduct(array $apiProduct): array
    {
        // Map category from Sony's format to our system's category
        $category = $this->mapCategory($apiProduct['category'] ?? '');
        
        // Create specifications array by mapping API specs to our system's specs
        $specifications = [];
        if (!empty($apiProduct['specifications'])) {
            foreach ($apiProduct['specifications'] as $key => $value) {
                $mappedKey = $this->specificationMapping[$key] ?? $key;
                $specifications[$mappedKey] = $value;
            }
        }
        
        // Construct images array
        $images = !empty($apiProduct['images']) ? $apiProduct['images'] : [];
        
        // Return the transformed product data
        return [
            'sku' => $apiProduct['id'],
            'name' => $apiProduct['name'],
            'description' => $apiProduct['description'] ?? '',
            'price' => $apiProduct['price'] ?? 0,
            'stock' => $apiProduct['stock'] ?? 0,
            'category' => $category,
            'brand' => 'Sony',
            'specifications' => $specifications,
            'images' => $images,
        ];
    }

    /**
     * Map API categories to local system categories
     *
     * @param string $apiCategory
     * @return int|null
     */
    protected function mapCategory(string $apiCategory): ?int
    {
        $categoryMapping = [
            'TV' => 2,         // Televisions
            'CAMERA' => 8,     // Cameras
            'AUDIO' => 7,      // Audio
            'GAMING' => 9,     // Gaming
            'MOBILE' => 1,     // Smartphones
        ];
        
        return $categoryMapping[$apiCategory] ?? null;
    }
} 