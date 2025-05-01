<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsHelper
{
    /**
     * Get a setting value by key with caching
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return Cache::remember('setting_' . $key, 3600, function () use ($key, $default) {
            return Setting::get($key, $default);
        });
    }

    /**
     * Get all settings for a specific group with caching
     *
     * @param string $group
     * @return \Illuminate\Support\Collection
     */
    public static function getGroup($group)
    {
        return Cache::remember('settings_group_' . $group, 3600, function () use ($group) {
            return Setting::getByGroup($group);
        });
    }

    /**
     * Get active social media links
     * 
     * @return array
     */
    public static function getSocialMedia()
    {
        return Cache::remember('settings_social_media', 3600, function () {
            $socialSettings = self::getGroup('social');
            $socialLinks = [];
            
            $socialIcons = [
                'facebook_url' => 'fab fa-facebook-f',
                'instagram_url' => 'fab fa-instagram',
                'twitter_url' => 'fab fa-twitter',
                'linkedin_url' => 'fab fa-linkedin-in',
                'youtube_url' => 'fab fa-youtube',
                'pinterest_url' => 'fab fa-pinterest-p',
                'tiktok_url' => 'fab fa-tiktok'
            ];
            
            foreach ($socialSettings as $setting) {
                $key = $setting->key;
                
                // Only include actual social media URLs, not settings like "show_social_share"
                if (isset($socialIcons[$key]) && !empty($setting->value)) {
                    $socialLinks[$key] = [
                        'url' => $setting->value,
                        'icon' => $socialIcons[$key],
                        'label' => $setting->label
                    ];
                }
            }
            
            return $socialLinks;
        });
    }

    /**
     * Get contact information
     * 
     * @return array
     */
    public static function getContactInfo()
    {
        return Cache::remember('settings_contact_info', 3600, function () {
            $contactSettings = self::getGroup('contact');
            $contactInfo = [];
            
            $contactIcons = [
                'contact_email' => 'fas fa-envelope',
                'support_email' => 'fas fa-headset',
                'sales_email' => 'fas fa-tags',
                'phone_number' => 'fas fa-phone-alt',
                'business_hours' => 'fas fa-clock',
                'address' => 'fas fa-map-marker-alt'
            ];
            
            foreach ($contactSettings as $setting) {
                $key = $setting->key;
                
                // Only include actual contact info fields, not settings like "contact_form_recipients"
                if (isset($contactIcons[$key]) && !empty($setting->value)) {
                    $contactInfo[$key] = [
                        'value' => $setting->value,
                        'icon' => $contactIcons[$key],
                        'label' => $setting->label
                    ];
                }
            }
            
            return $contactInfo;
        });
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache()
    {
        Cache::forget('app_settings');
        Cache::forget('settings_social_media');
        Cache::forget('settings_contact_info');
        
        // Clear individual setting caches
        $settings = Setting::all();
        foreach ($settings as $setting) {
            Cache::forget('setting_' . $setting->key);
        }
        
        // Clear group caches
        $groups = Setting::distinct()->pluck('group');
        foreach ($groups as $group) {
            Cache::forget('settings_group_' . $group);
        }
    }
} 