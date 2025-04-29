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
        // SEO Settings
        $seoSettings = [
            [
                'key' => 'meta_title',
                'label' => 'Meta Title',
                'group' => 'seo',
                'type' => 'text',
                'value' => config('app.name') . ' - Product Comparison Platform',
                'description' => 'Default meta title for the website (max 60 characters)'
            ],
            [
                'key' => 'meta_description',
                'label' => 'Meta Description',
                'group' => 'seo',
                'type' => 'textarea',
                'value' => 'Compare products, find the best deals, and make informed purchasing decisions with our advanced comparison tools.',
                'description' => 'Default meta description for the website (max 160 characters)'
            ],
            [
                'key' => 'meta_keywords',
                'label' => 'Meta Keywords',
                'group' => 'seo',
                'type' => 'textarea',
                'value' => 'product comparison, price comparison, product reviews, best deals',
                'description' => 'Comma-separated list of keywords (less important these days)'
            ],
            [
                'key' => 'google_analytics',
                'label' => 'Google Analytics ID',
                'group' => 'seo',
                'type' => 'text',
                'value' => '',
                'description' => 'Your Google Analytics tracking code (example: UA-XXXXXXXX-X or G-XXXXXXXXXX)'
            ],
            [
                'key' => 'robots_index',
                'label' => 'Allow Search Engines to Index',
                'group' => 'seo',
                'type' => 'checkbox',
                'value' => '1',
                'description' => 'If disabled, adds noindex meta tag to prevent search engines from indexing your site'
            ],
            [
                'key' => 'canonical_url',
                'label' => 'Canonical URL',
                'group' => 'seo',
                'type' => 'url',
                'value' => config('app.url'),
                'description' => 'The preferred version of your site URL (with or without www)'
            ],
            [
                'key' => 'og_image',
                'label' => 'Default Social Share Image',
                'group' => 'seo',
                'type' => 'image',
                'value' => '',
                'description' => 'Image displayed when your site is shared on social media (min 1200x630 pixels)'
            ],
            [
                'key' => 'twitter_handle',
                'label' => 'Twitter Handle',
                'group' => 'seo',
                'type' => 'text',
                'value' => '',
                'description' => 'Your Twitter username (without @)'
            ]
        ];

        // Contact Settings
        $contactSettings = [
            [
                'key' => 'contact_email',
                'label' => 'Contact Email',
                'group' => 'contact',
                'type' => 'email',
                'value' => 'contact@example.com',
                'description' => 'Primary email for receiving contact form submissions'
            ],
            [
                'key' => 'support_email',
                'label' => 'Support Email',
                'group' => 'contact',
                'type' => 'email',
                'value' => 'support@example.com',
                'description' => 'Email for customer support inquiries'
            ],
            [
                'key' => 'sales_email',
                'label' => 'Sales Email',
                'group' => 'contact',
                'type' => 'email',
                'value' => 'sales@example.com',
                'description' => 'Email for sales inquiries'
            ],
            [
                'key' => 'phone_number',
                'label' => 'Phone Number',
                'group' => 'contact',
                'type' => 'text',
                'value' => '+1 (555) 123-4567',
                'description' => 'Primary contact phone number'
            ],
            [
                'key' => 'business_hours',
                'label' => 'Business Hours',
                'group' => 'contact',
                'type' => 'text',
                'value' => 'Monday-Friday, 9am-5pm EST',
                'description' => 'Your business operating hours'
            ],
            [
                'key' => 'address',
                'label' => 'Business Address',
                'group' => 'contact',
                'type' => 'textarea',
                'value' => '123 Business St., Suite 100, City, State 12345',
                'description' => 'Your business physical address'
            ],
            [
                'key' => 'google_maps_embed',
                'label' => 'Google Maps Embed Code',
                'group' => 'contact',
                'type' => 'textarea',
                'value' => '',
                'description' => 'The iframe embed code from Google Maps for your location'
            ],
            [
                'key' => 'contact_form_recipients',
                'label' => 'Contact Form Recipients',
                'group' => 'contact',
                'type' => 'text',
                'value' => 'contact@example.com',
                'description' => 'Comma-separated list of emails to receive form submissions'
            ],
            [
                'key' => 'auto_reply_message',
                'label' => 'Auto-Reply Message',
                'group' => 'contact',
                'type' => 'textarea',
                'value' => 'Thank you for contacting us. We have received your message and will respond as soon as possible.',
                'description' => 'Message automatically sent to users after form submission'
            ]
        ];

        // Insert or update SEO settings
        foreach ($seoSettings as $setting) {
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

        // Insert or update Contact settings
        foreach ($contactSettings as $setting) {
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
        // Remove the added SEO settings
        DB::table('settings')->whereIn('key', [
            'meta_title', 'meta_description', 'meta_keywords', 'google_analytics',
            'robots_index', 'canonical_url', 'og_image', 'twitter_handle'
        ])->delete();

        // Remove the added contact settings
        DB::table('settings')->whereIn('key', [
            'contact_email', 'support_email', 'sales_email', 'phone_number',
            'business_hours', 'address', 'google_maps_embed', 'contact_form_recipients',
            'auto_reply_message'
        ])->delete();
    }
};
