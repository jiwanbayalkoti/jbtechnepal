<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subcategory;
use App\Models\Category;

class SubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $subcategories = [
            'Laptops' => [
                [
                    'name' => 'Gaming Laptops',
                    'slug' => 'gaming-laptops',
                    'description' => 'High-performance laptops designed for gaming',
                    'is_active' => true
                ],
                [
                    'name' => 'Business Laptops',
                    'slug' => 'business-laptops',
                    'description' => 'Laptops optimized for productivity and business use',
                    'is_active' => true
                ],
                [
                    'name' => 'Student Laptops',
                    'slug' => 'student-laptops',
                    'description' => 'Affordable laptops suitable for students',
                    'is_active' => true
                ]
            ],
            'Smartphones' => [
                [
                    'name' => 'Flagship Phones',
                    'slug' => 'flagship-phones',
                    'description' => 'Premium smartphones with cutting-edge features',
                    'is_active' => true
                ],
                [
                    'name' => 'Mid-range Phones',
                    'slug' => 'mid-range-phones',
                    'description' => 'Balanced smartphones with good value',
                    'is_active' => true
                ],
                [
                    'name' => 'Budget Phones',
                    'slug' => 'budget-phones',
                    'description' => 'Affordable smartphones for everyday use',
                    'is_active' => true
                ]
            ],
            'TVs' => [
                [
                    'name' => 'OLED TVs',
                    'slug' => 'oled-tvs',
                    'description' => 'Premium TVs with OLED display technology',
                    'is_active' => true
                ],
                [
                    'name' => 'QLED TVs',
                    'slug' => 'qled-tvs',
                    'description' => 'High-end TVs with QLED display technology',
                    'is_active' => true
                ],
                [
                    'name' => 'LED TVs',
                    'slug' => 'led-tvs',
                    'description' => 'Standard TVs with LED backlighting',
                    'is_active' => true
                ]
            ],
            'Refrigerators' => [
                [
                    'name' => 'French Door',
                    'slug' => 'french-door-refrigerators',
                    'description' => 'Refrigerators with French door design',
                    'is_active' => true
                ],
                [
                    'name' => 'Side by Side',
                    'slug' => 'side-by-side-refrigerators',
                    'description' => 'Refrigerators with side-by-side compartments',
                    'is_active' => true
                ],
                [
                    'name' => 'Top Freezer',
                    'slug' => 'top-freezer-refrigerators',
                    'description' => 'Traditional refrigerators with top freezer',
                    'is_active' => true
                ]
            ],
            'Gaming Consoles' => [
                [
                    'name' => 'PlayStation',
                    'slug' => 'playstation',
                    'description' => 'Sony gaming consoles',
                    'is_active' => true
                ],
                [
                    'name' => 'Xbox',
                    'slug' => 'xbox',
                    'description' => 'Microsoft gaming consoles',
                    'is_active' => true
                ],
                [
                    'name' => 'Nintendo',
                    'slug' => 'nintendo',
                    'description' => 'Nintendo gaming systems',
                    'is_active' => true
                ]
            ],
            'Tablets' => [
                [
                    'name' => 'iPad',
                    'slug' => 'ipad',
                    'description' => 'Apple tablets',
                    'is_active' => true
                ],
                [
                    'name' => 'Android Tablets',
                    'slug' => 'android-tablets',
                    'description' => 'Tablets running Android OS',
                    'is_active' => true
                ],
                [
                    'name' => 'Windows Tablets',
                    'slug' => 'windows-tablets',
                    'description' => 'Tablets running Windows OS',
                    'is_active' => true
                ]
            ],
            'Washing Machines' => [
                [
                    'name' => 'Front Load',
                    'slug' => 'front-load-washing-machines',
                    'description' => 'Washing machines with front-loading design',
                    'is_active' => true
                ],
                [
                    'name' => 'Top Load',
                    'slug' => 'top-load-washing-machines',
                    'description' => 'Washing machines with top-loading design',
                    'is_active' => true
                ],
                [
                    'name' => 'Semi-Automatic',
                    'slug' => 'semi-automatic-washing-machines',
                    'description' => 'Partially automated washing machines',
                    'is_active' => true
                ]
            ],
            'Air Conditioners' => [
                [
                    'name' => 'Split AC',
                    'slug' => 'split-ac',
                    'description' => 'Air conditioners with separate indoor and outdoor units',
                    'is_active' => true
                ],
                [
                    'name' => 'Window AC',
                    'slug' => 'window-ac',
                    'description' => 'Single-unit air conditioners for windows',
                    'is_active' => true
                ],
                [
                    'name' => 'Portable AC',
                    'slug' => 'portable-ac',
                    'description' => 'Movable air conditioning units',
                    'is_active' => true
                ]
            ]
        ];

        foreach ($subcategories as $categoryName => $categorySubcategories) {
            $category = Category::where('name', $categoryName)->first();
            
            if ($category) {
                foreach ($categorySubcategories as $subcategory) {
                    Subcategory::create([
                        'category_id' => $category->id,
                        'name' => $subcategory['name'],
                        'slug' => $subcategory['slug'],
                        'description' => $subcategory['description'],
                        'is_active' => $subcategory['is_active']
                    ]);
                }
            }
        }
    }
} 