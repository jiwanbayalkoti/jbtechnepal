<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register components
        Blade::component('admin-form-modal', \App\View\Components\AdminFormModal::class);
        
        // Share $footerCategories with the app layout
        view()->composer('layouts.app', function ($view) {
            // Get categories for the footer - we'll limit to 5 categories
            $footerCategories = \App\Models\Category::where('is_featured', 1)
                                ->orderBy('name')
                                ->take(5)
                                ->get();
            
            $view->with('footerCategories', $footerCategories);
        });

        // Share $footerCategories with the app layout
        view()->composer('layouts.app', function ($view) {
            // Get categories for the footer - we'll limit to 5 categories
            $footerCategories = \App\Models\Category::orderBy('name')
                                ->take(5)
                                ->get();
            
            $view->with('footerCategories', $footerCategories);
        });
    }
}
