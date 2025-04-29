<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Social Media Settings
        $socialSettings = [
            [
                'key' => 'facebook_url',
                'label' => 'Facebook URL',
                'group' => 'social',
                'type' => 'url',
                'value' => 'https://facebook.com/',
                'description' => 'Your Facebook page URL'
            ],
            [
                'key' => 'instagram_url',
                'label' => 'Instagram URL',
                'group' => 'social',
                'type' => 'url',
                'value' => 'https://instagram.com/',
                'description' => 'Your Instagram profile URL'
            ],
            [
                'key' => 'twitter_url',
                'label' => 'Twitter URL',
                'group' => 'social',
                'type' => 'url',
                'value' => 'https://twitter.com/',
                'description' => 'Your Twitter profile URL'
            ],
            [
                'key' => 'linkedin_url',
                'label' => 'LinkedIn URL',
                'group' => 'social',
                'type' => 'url',
                'value' => 'https://linkedin.com/',
                'description' => 'Your LinkedIn page URL'
            ],
            [
                'key' => 'youtube_url',
                'label' => 'YouTube URL',
                'group' => 'social',
                'type' => 'url',
                'value' => 'https://youtube.com/',
                'description' => 'Your YouTube channel URL'
            ],
            [
                'key' => 'pinterest_url',
                'label' => 'Pinterest URL',
                'group' => 'social',
                'type' => 'url',
                'value' => '',
                'description' => 'Your Pinterest profile URL'
            ],
            [
                'key' => 'tiktok_url',
                'label' => 'TikTok URL',
                'group' => 'social',
                'type' => 'url',
                'value' => '',
                'description' => 'Your TikTok profile URL'
            ],
            [
                'key' => 'show_social_share',
                'label' => 'Show Social Share Buttons',
                'group' => 'social',
                'type' => 'checkbox',
                'value' => '1',
                'description' => 'Enable social sharing buttons on product pages'
            ],
            [
                'key' => 'show_social_follow',
                'label' => 'Show Social Follow Buttons',
                'group' => 'social',
                'type' => 'checkbox',
                'value' => '1',
                'description' => 'Display social media follow buttons in the footer'
            ]
        ];

        // Insert or update Social Media settings
        foreach ($socialSettings as $setting) {
            $exists = DB::table('settings')->where('key', $setting['key'])->exists();
            
            if (!$exists) {
                DB::table('settings')->insert([
                    'key' => $setting['key'],
                    'label' => $setting['label'],
                    'group' => $setting['group'],
                    'type' => $setting['type'],
                    'value' => $setting['value'],
                    'description' => $setting['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the added Social Media settings
        DB::table('settings')->whereIn('key', [
            'facebook_url', 'instagram_url', 'twitter_url', 'linkedin_url',
            'youtube_url', 'pinterest_url', 'tiktok_url',
            'show_social_share', 'show_social_follow'
        ])->delete();
    }
}; 