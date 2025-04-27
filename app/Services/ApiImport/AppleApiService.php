<?php

namespace App\Services\ApiImport;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Category;

class AppleApiService extends ApiImportService
{
    protected $endpoint;
    protected $apiKey;
    protected $useMockData;
    protected $categoryMapping;
    protected $specificationMapping;

    public function __construct()
    {
        // Set API endpoint and credentials from config
        $this->endpoint = config('api_imports.apple.endpoint', 'https://api.apple.com/products');
        $this->apiKey = config('api_imports.apple.api_key');
        
        // Use mock data if enabled or if no API key is provided
        $this->useMockData = config('api_imports.apple.use_mock_data', true) || empty($this->apiKey);
        
        // Define category mapping (Apple API category name => Our system category name)
        $this->categoryMapping = [
            'iPhone' => 'smartphones',
            'iPad' => 'tablets',
            'Mac' => 'computers',
            'Apple Watch' => 'wearables',
            'AirPods' => 'audio',
            'Apple TV' => 'media-players',
        ];
        
        // Define specification mapping (Apple API spec name => Our system spec name)
        $this->specificationMapping = [
            'display_size' => 'screen_size',
            'display_resolution' => 'resolution',
            'chip' => 'processor',
            'ram' => 'ram',
            'storage' => 'storage',
            'camera' => 'camera',
            'battery' => 'battery',
            'dimensions' => 'dimensions',
            'weight' => 'weight',
            'connectivity' => 'connectivity',
            'colors' => 'color_options',
        ];
    }

    /**
     * Fetch products from the Apple API
     *
     * @return array
     */
    public function fetchProducts(): array
    {
        $options = [];
        if ($this->useMockData) {
            Log::info('Using mock data for Apple products');
            return $this->getMockProducts();
        }
        
        try {
            // Prepare request parameters
            $params = [
                'apiKey' => $this->apiKey,
                'limit' => $options['limit'] ?? 50,
            ];
            
            if (!empty($options['categories'])) {
                $params['categories'] = implode(',', $options['categories']);
            }
            
            // Make the API request
            $response = Http::get($this->endpoint, $params);
            
            if ($response->successful()) {
                return $response->json()['products'] ?? [];
            } else {
                Log::error('Apple API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Error fetching Apple products: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get mock Apple products for testing
     */
    protected function getMockProducts()
    {
        return [
            [
                'id' => 'IPHONE13PRO',
                'name' => 'iPhone 13 Pro',
                'category' => 'iPhone',
                'description' => 'A dramatically more powerful camera system. A display so responsive, every interaction feels new again. The world\'s fastest smartphone chip. Exceptional durability. And a huge leap in battery life.',
                'price' => 999.00,
                'stock' => 25,
                'specifications' => [
                    'display_size' => '6.1 inches',
                    'display_resolution' => 'Super Retina XDR (2532 x 1170)',
                    'chip' => 'A15 Bionic',
                    'ram' => '6GB',
                    'storage' => '128GB, 256GB, 512GB, 1TB',
                    'camera' => 'Pro 12MP camera system: Telephoto, Wide, and Ultra Wide',
                    'battery' => 'Up to 22 hours video playback',
                    'dimensions' => '146.7 x 71.5 x 7.65 mm',
                    'weight' => '204 grams',
                    'connectivity' => '5G capable',
                    'colors' => 'Graphite, Gold, Silver, Sierra Blue',
                ],
                'images' => [
                    'https://example.com/images/apple/iphone13pro_1.jpg',
                    'https://example.com/images/apple/iphone13pro_2.jpg',
                ],
            ],
            [
                'id' => 'IPADPRO12',
                'name' => 'iPad Pro 12.9-inch (5th generation)',
                'category' => 'iPad',
                'description' => 'The ultimate iPad experience with the most advanced technology. Featuring a breathtaking Liquid Retina XDR display and the powerful Apple M1 chip.',
                'price' => 1099.00,
                'stock' => 15,
                'specifications' => [
                    'display_size' => '12.9 inches',
                    'display_resolution' => 'Liquid Retina XDR (2732 x 2048)',
                    'chip' => 'Apple M1',
                    'ram' => '8GB or 16GB',
                    'storage' => '128GB, 256GB, 512GB, 1TB, 2TB',
                    'camera' => '12MP Wide camera, 10MP Ultra Wide camera, LiDAR Scanner',
                    'battery' => 'Up to 10 hours',
                    'dimensions' => '280.6 x 214.9 x 6.4 mm',
                    'weight' => '682 grams (Wi-Fi), 685 grams (Wi-Fi + Cellular)',
                ],
                'images' => [
                    'https://example.com/images/apple/ipadpro12_1.jpg',
                    'https://example.com/images/apple/ipadpro12_2.jpg',
                ],
            ],
            [
                'id' => 'MBPM1PRO',
                'name' => 'MacBook Pro 14-inch',
                'category' => 'Mac',
                'description' => 'The most powerful MacBook Pro ever is here. With the blazing-fast M1 Pro or M1 Max chip — the first Apple silicon designed for pros — and amazing battery life.',
                'price' => 1999.00,
                'stock' => 10,
                'specifications' => [
                    'display_size' => '14.2 inches',
                    'display_resolution' => 'Liquid Retina XDR (3024 x 1964)',
                    'chip' => 'Apple M1 Pro or M1 Max',
                    'ram' => '16GB, 32GB, or 64GB',
                    'storage' => '512GB, 1TB, 2TB, 4TB, or 8TB',
                    'camera' => '1080p FaceTime HD camera',
                    'battery' => 'Up to 17 hours',
                    'dimensions' => '312.6 x 221.2 x 15.5 mm',
                    'weight' => '1.6 kg',
                ],
                'images' => [
                    'https://example.com/images/apple/macbookpro14_1.jpg',
                    'https://example.com/images/apple/macbookpro14_2.jpg',
                ],
            ],
            [
                'id' => 'APPLEWATCHS7',
                'name' => 'Apple Watch Series 7',
                'category' => 'Apple Watch',
                'description' => 'The largest, most advanced display yet. The most durable Apple Watch ever. Breakthrough health innovations. Up to 33% faster charging.',
                'price' => 399.00,
                'stock' => 30,
                'specifications' => [
                    'display_size' => '41mm or 45mm',
                    'display_resolution' => 'Always-On Retina LTPO OLED display',
                    'chip' => 'S7 SiP with 64-bit dual-core processor',
                    'storage' => '32GB',
                    'battery' => 'Up to 18 hours',
                    'dimensions' => 'Varies by size',
                    'weight' => 'Varies by material and size',
                    'connectivity' => 'GPS, GPS + Cellular',
                    'colors' => 'Midnight, Starlight, Green, Blue, Red',
                ],
                'images' => [
                    'https://example.com/images/apple/watchseries7_1.jpg',
                    'https://example.com/images/apple/watchseries7_2.jpg',
                ],
            ],
            [
                'id' => 'AIRPODSPRO',
                'name' => 'AirPods Pro',
                'category' => 'AirPods',
                'description' => 'Active Noise Cancellation for immersive sound. Transparency mode for hearing what\'s happening around you. A customizable fit for all-day comfort.',
                'price' => 249.00,
                'stock' => 40,
                'specifications' => [
                    'chip' => 'H1 headphone chip',
                    'battery' => 'Up to 4.5 hours listening time with ANC on',
                    'weight' => '5.4 grams (each)',
                    'connectivity' => 'Bluetooth 5.0',
                ],
                'images' => [
                    'https://example.com/images/apple/airpodspro_1.jpg',
                    'https://example.com/images/apple/airpodspro_2.jpg',
                ],
            ],
        ];
    }

    /**
     * Transform product data from the Apple API format to our format
     *
     * @param array $apiProduct
     * @return array
     */
    protected function transformProduct(array $apiProduct): array
    {
        // Map category from Apple's format to our system's category
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
            'brand' => 'Apple',
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
            'iPhone' => 1,       // Smartphones
            'iPad' => 4,         // Tablets
            'Mac' => 5,          // Computers
            'Apple Watch' => 6,  // Wearables
            'AirPods' => 7,      // Audio
            'Apple TV' => 10,    // Media-players
        ];
        
        return $categoryMapping[$apiCategory] ?? null;
    }
} 