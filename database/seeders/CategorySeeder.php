<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Laptops',
                'slug' => 'laptops',
                'description' => 'Portable computers for work, gaming, and everyday use',
                'image' => 'categories/laptops.jpg',
                'is_active' => true
            ],
            [
                'name' => 'Smartphones',
                'slug' => 'smartphones',
                'description' => 'Mobile phones with advanced computing capabilities',
                'image' => 'categories/smartphones.jpg',
                'is_active' => true
            ],
            [
                'name' => 'TVs',
                'slug' => 'tvs',
                'description' => 'Television sets with smart features and high-resolution displays',
                'image' => 'categories/tvs.jpg',
                'is_active' => true
            ],
            [
                'name' => 'Refrigerators',
                'slug' => 'refrigerators',
                'description' => 'Home appliances for food storage and preservation',
                'image' => 'categories/refrigerators.jpg',
                'is_active' => true
            ],
            [
                'name' => 'Gaming Consoles',
                'slug' => 'gaming-consoles',
                'description' => 'Video game systems for home entertainment',
                'image' => 'categories/gaming-consoles.jpg',
                'is_active' => true
            ],
            [
                'name' => 'Tablets',
                'slug' => 'tablets',
                'description' => 'Portable computing devices with touchscreen displays',
                'image' => 'categories/tablets.jpg',
                'is_active' => true
            ],
            [
                'name' => 'Washing Machines',
                'slug' => 'washing-machines',
                'description' => 'Home appliances for cleaning clothes',
                'image' => 'categories/washing-machines.jpg',
                'is_active' => true
            ],
            [
                'name' => 'Air Conditioners',
                'slug' => 'air-conditioners',
                'description' => 'Climate control systems for indoor comfort',
                'image' => 'categories/air-conditioners.jpg',
                'is_active' => true
            ]
        ];

        foreach ($categories as $category) {
            // Create the category
            $createdCategory = Category::create([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'description' => $category['description'],
                'image' => $category['image'],
                'is_active' => $category['is_active']
            ]);

            // Create category images directory if it doesn't exist
            if (!Storage::exists('public/categories')) {
                Storage::makeDirectory('public/categories');
            }

            // Copy image file from resources to public storage
            $sourcePath = resource_path('images/categories/' . basename($category['image']));
            $destinationPath = 'public/' . $category['image'];
            
            if (file_exists($sourcePath)) {
                Storage::put($destinationPath, file_get_contents($sourcePath));
            }
        }
    }
} 