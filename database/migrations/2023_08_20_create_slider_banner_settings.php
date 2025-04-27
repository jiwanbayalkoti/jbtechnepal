<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add banner slider enabled setting
        if (!Setting::where('key', 'product_slider_enabled')->exists()) {
            Setting::create([
                'key' => 'product_slider_enabled',
                'value' => '1',
                'label' => 'Enable Product Slider Banner',
                'description' => 'Show a product slider banner below the menu',
                'type' => 'checkbox',
                'group' => 'general',
                'order' => 20
            ]);
        }
        
        // Add slider title setting
        if (!Setting::where('key', 'product_slider_title')->exists()) {
            Setting::create([
                'key' => 'product_slider_title',
                'value' => 'New Arrivals',
                'label' => 'Slider Title',
                'description' => 'Title to display above the product slider',
                'type' => 'text',
                'group' => 'general',
                'order' => 21
            ]);
        }
        
        // Add subtitle setting
        if (!Setting::where('key', 'product_slider_subtitle')->exists()) {
            Setting::create([
                'key' => 'product_slider_subtitle',
                'value' => 'Check out our latest products',
                'label' => 'Slider Subtitle',
                'description' => 'Subtitle to display below the slider title',
                'type' => 'text',
                'group' => 'general',
                'order' => 22
            ]);
        }
        
        // Add products count setting
        if (!Setting::where('key', 'product_slider_count')->exists()) {
            Setting::create([
                'key' => 'product_slider_count',
                'value' => '10',
                'label' => 'Number of Products',
                'description' => 'Number of newest products to display in the slider',
                'type' => 'number',
                'group' => 'general',
                'order' => 23
            ]);
        }
        
        // Add background color setting
        if (!Setting::where('key', 'product_slider_bg_color')->exists()) {
            Setting::create([
                'key' => 'product_slider_bg_color',
                'value' => '#f8f9fa',
                'label' => 'Slider Background Color',
                'description' => 'Background color for the product slider section',
                'type' => 'color',
                'group' => 'general',
                'order' => 24
            ]);
        }
        
        // Add auto-play setting
        if (!Setting::where('key', 'product_slider_autoplay')->exists()) {
            Setting::create([
                'key' => 'product_slider_autoplay',
                'value' => '1',
                'label' => 'Enable Auto-play',
                'description' => 'Automatically slide through products',
                'type' => 'checkbox',
                'group' => 'general',
                'order' => 25
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::whereIn('key', [
            'product_slider_enabled',
            'product_slider_title',
            'product_slider_subtitle',
            'product_slider_count',
            'product_slider_bg_color',
            'product_slider_autoplay'
        ])->delete();
    }
}; 