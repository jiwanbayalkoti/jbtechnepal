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
        // Add promotional banner enabled setting
        if (!Setting::where('key', 'promotional_banner_enabled')->exists()) {
            Setting::create([
                'key' => 'promotional_banner_enabled',
                'value' => '0',
                'label' => 'Enable Promotional Banner',
                'description' => 'Show a promotional banner at the top of the website',
                'type' => 'checkbox',
                'group' => 'general',
                'order' => 10
            ]);
        }
        
        // Add promotional banner text setting
        if (!Setting::where('key', 'promotional_banner_text')->exists()) {
            Setting::create([
                'key' => 'promotional_banner_text',
                'value' => 'Special Offer! Get 10% off on all products with code PROMO10',
                'label' => 'Banner Text',
                'description' => 'The text to display in the promotional banner',
                'type' => 'text',
                'group' => 'general',
                'order' => 11
            ]);
        }
        
        // Add promotional banner background color
        if (!Setting::where('key', 'promotional_banner_bg_color')->exists()) {
            Setting::create([
                'key' => 'promotional_banner_bg_color',
                'value' => '#ffc107',
                'label' => 'Banner Background Color',
                'description' => 'Background color for the promotional banner',
                'type' => 'color',
                'group' => 'general',
                'order' => 12
            ]);
        }
        
        // Add promotional banner text color
        if (!Setting::where('key', 'promotional_banner_text_color')->exists()) {
            Setting::create([
                'key' => 'promotional_banner_text_color',
                'value' => '#000000',
                'label' => 'Banner Text Color',
                'description' => 'Text color for the promotional banner',
                'type' => 'color',
                'group' => 'general',
                'order' => 13
            ]);
        }

        // Add promotional banner link
        if (!Setting::where('key', 'promotional_banner_link')->exists()) {
            Setting::create([
                'key' => 'promotional_banner_link',
                'value' => null,
                'label' => 'Banner Link',
                'description' => 'URL to link the banner to (optional)',
                'type' => 'url',
                'group' => 'general',
                'order' => 14
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::whereIn('key', [
            'promotional_banner_enabled',
            'promotional_banner_text',
            'promotional_banner_bg_color',
            'promotional_banner_text_color',
            'promotional_banner_link'
        ])->delete();
    }
}; 