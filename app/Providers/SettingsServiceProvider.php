<?php

namespace App\Providers;

use App\Models\MenuItem;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Check if the tables exist before trying to access them
        if (Schema::hasTable('settings')) {
            // Share settings with all views
            $settings = Cache::remember('app_settings', 3600, function () {
                $allSettings = Setting::all();
                $settingsArray = [];
                
                foreach ($allSettings as $setting) {
                    $settingsArray[$setting->key] = $setting->value;
                }
                
                return $settingsArray;
            });
            
            View::share('settings', $settings);
        } else {
            View::share('settings', []);
        }
        
        if (Schema::hasTable('menu_items')) {
            // Share menu items with all views
            $mainMenu = Cache::remember('main_menu', 3600, function () {
                return MenuItem::getMenuItems('main');
            });
            
            $footerMenu = Cache::remember('footer_menu', 3600, function () {
                return MenuItem::getMenuItems('footer');
            });
            
            View::share('mainMenu', $mainMenu);
            View::share('footerMenu', $footerMenu);
        } else {
            View::share('mainMenu', collect([]));
            View::share('footerMenu', collect([]));
        }
    }
}
