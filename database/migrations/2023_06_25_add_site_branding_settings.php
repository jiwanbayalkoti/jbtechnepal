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
        // Add site title setting
        if (!Setting::where('key', 'site_title')->exists()) {
            Setting::create([
                'key' => 'site_title',
                'value' => config('app.name', 'Product Compare'),
                'label' => 'Site Title',
                'description' => 'The title of your website (displayed in browser tabs and search results)',
                'type' => 'text',
                'group' => 'general',
                'order' => 1
            ]);
        }
        
        // Add site logo setting
        if (!Setting::where('key', 'site_logo')->exists()) {
            Setting::create([
                'key' => 'site_logo',
                'value' => null,
                'label' => 'Site Logo',
                'description' => 'Your website logo (recommended size: 200x50px)',
                'type' => 'image',
                'group' => 'general',
                'order' => 2
            ]);
        }
        
        // Add favicon setting
        if (!Setting::where('key', 'favicon')->exists()) {
            Setting::create([
                'key' => 'favicon',
                'value' => null,
                'label' => 'Favicon',
                'description' => 'Small icon displayed in browser tabs (recommended size: 32x32px, .ico or .png format)',
                'type' => 'image',
                'group' => 'general',
                'order' => 3
            ]);
        }
        
        // Add primary color setting
        if (!Setting::where('key', 'primary_color')->exists()) {
            Setting::create([
                'key' => 'primary_color',
                'value' => '#0d6efd',
                'label' => 'Primary Color',
                'description' => 'Main color used throughout the site',
                'type' => 'color',
                'group' => 'general',
                'order' => 4
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::whereIn('key', ['site_title', 'site_logo', 'favicon', 'primary_color'])->delete();
    }
}; 