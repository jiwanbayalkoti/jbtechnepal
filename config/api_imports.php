<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Import Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for different brand API integrations.
    | Add credentials and settings for each API you want to integrate with.
    |
    */

    'enabled' => env('API_IMPORTS_ENABLED', true),

    'samsung' => [
        'enabled' => env('API_SAMSUNG_ENABLED', true),
        'endpoint' => env('API_SAMSUNG_ENDPOINT', 'https://api.samsung.com/products'),
        'api_key' => env('API_SAMSUNG_KEY', ''),
        'api_secret' => env('API_SAMSUNG_SECRET', ''),
    ],

    'apple' => [
        'enabled' => env('API_APPLE_ENABLED', false),
        'endpoint' => env('API_APPLE_ENDPOINT', 'https://api.apple.com/products'),
        'api_key' => env('API_APPLE_KEY', ''),
        'api_secret' => env('API_APPLE_SECRET', ''),
    ],

    'sony' => [
        'enabled' => env('API_SONY_ENABLED', false),
        'endpoint' => env('API_SONY_ENDPOINT', 'https://api.sony.com/products'),
        'api_key' => env('API_SONY_KEY', ''),
        'api_secret' => env('API_SONY_SECRET', ''),
    ],

    /*
     * Add more brand configurations as needed
     */

    // Global import settings
    'import_settings' => [
        'batch_size' => env('API_IMPORT_BATCH_SIZE', 100),
        'timeout' => env('API_IMPORT_TIMEOUT', 60),
        'retry_attempts' => env('API_IMPORT_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('API_IMPORT_RETRY_DELAY', 5), // In seconds
    ],
]; 