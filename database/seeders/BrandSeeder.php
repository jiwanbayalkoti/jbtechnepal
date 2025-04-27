<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Facades\Storage;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Samsung',
                'description' => 'A global leader in electronics and technology, known for innovative smartphones, TVs, and home appliances.',
                'logo' => 'brands/samsung.png',
                'website' => 'https://www.samsung.com',
                'is_active' => true
            ],
            [
                'name' => 'Sony',
                'description' => 'A Japanese multinational conglomerate known for electronics, gaming, and entertainment products.',
                'logo' => 'brands/sony.png',
                'website' => 'https://www.sony.com',
                'is_active' => true
            ],
            [
                'name' => 'LG',
                'description' => 'A South Korean multinational electronics company known for TVs, appliances, and mobile devices.',
                'logo' => 'brands/lg.png',
                'website' => 'https://www.lg.com',
                'is_active' => true
            ],
            [
                'name' => 'Apple',
                'description' => 'An American multinational technology company known for iPhones, MacBooks, and other premium devices.',
                'logo' => 'brands/apple.png',
                'website' => 'https://www.apple.com',
                'is_active' => true
            ],
            [
                'name' => 'Dell',
                'description' => 'An American technology company known for personal computers, laptops, and enterprise solutions.',
                'logo' => 'brands/dell.png',
                'website' => 'https://www.dell.com',
                'is_active' => true
            ],
            [
                'name' => 'HP',
                'description' => 'An American multinational information technology company known for personal computers and printers.',
                'logo' => 'brands/hp.png',
                'website' => 'https://www.hp.com',
                'is_active' => true
            ],
            [
                'name' => 'Lenovo',
                'description' => 'A Chinese multinational technology company known for laptops, tablets, and smartphones.',
                'logo' => 'brands/lenovo.png',
                'website' => 'https://www.lenovo.com',
                'is_active' => true
            ],
            [
                'name' => 'Asus',
                'description' => 'A Taiwanese multinational computer and phone hardware and electronics company.',
                'logo' => 'brands/asus.png',
                'website' => 'https://www.asus.com',
                'is_active' => true
            ],
            [
                'name' => 'Acer',
                'description' => 'A Taiwanese multinational hardware and electronics corporation specializing in advanced electronics technology.',
                'logo' => 'brands/acer.png',
                'website' => 'https://www.acer.com',
                'is_active' => true
            ],
            [
                'name' => 'MSI',
                'description' => 'A Taiwanese multinational information technology corporation known for gaming laptops and components.',
                'logo' => 'brands/msi.png',
                'website' => 'https://www.msi.com',
                'is_active' => true
            ]
        ];

        foreach ($brands as $brand) {
            // Create the brand
            $createdBrand = Brand::create([
                'name' => $brand['name'],
                'description' => $brand['description'],
                'logo' => $brand['logo'],
                'website' => $brand['website'],
                'is_active' => $brand['is_active']
            ]);

            // Create logo directory if it doesn't exist
            if (!Storage::exists('public/brands')) {
                Storage::makeDirectory('public/brands');
            }

            // Copy logo file from resources to public storage
            $sourcePath = resource_path('images/brands/' . basename($brand['logo']));
            $destinationPath = 'public/' . $brand['logo'];
            
            if (file_exists($sourcePath)) {
                Storage::put($destinationPath, file_get_contents($sourcePath));
            }
        }
    }
} 