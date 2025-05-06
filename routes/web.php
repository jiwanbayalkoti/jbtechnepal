<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\ApiImportController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\BrandController;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BannerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Auth Routes
Route::get('/login', [UserAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserAuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');
Route::get('/register', [UserAuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [UserAuthController::class, 'register'])->name('register.submit');

// User Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [UserProfileController::class, 'update'])->name('profile.update');
});

// Admin Auth Routes
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Frontend Routes
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/load-more-products', [App\Http\Controllers\HomeController::class, 'loadMoreProducts'])->name('load.more.products');
Route::get('/product/{slug}', [CompareController::class, 'product'])->name('product');
Route::post('/add-to-compare', [CompareController::class, 'addToCompare'])->name('add.to.compare');
Route::post('/remove-from-compare', [CompareController::class, 'removeFromCompare'])->name('remove.from.compare');
Route::get('/compare', [CompareController::class, 'compare'])->name('compare');
Route::post('/ai-recommendations', [CompareController::class, 'getAiRecommendations'])->name('ai.recommendations');
Route::get('/clear-compare', [CompareController::class, 'clearCompare'])->name('clear.compare');
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Brand routes
Route::get('/brands', [BrandController::class, 'index'])->name('brands.all');
Route::get('/brand/{slug}', [BrandController::class, 'show'])->name('brand.show');

// Cart & Checkout Routes
Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/update', [App\Http\Controllers\CartController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/remove', [App\Http\Controllers\CartController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/process', [App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/success/{order}', [App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');

// Contact Routes
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/my-messages', [ContactController::class, 'myMessages'])->name('contact.my-messages');

// AI Search Route
Route::post('/ai-search', [HomeController::class, 'aiSearch'])->name('ai.search');

// Advertisement Routes
Route::get('/ads/{position}', [AdvertisementController::class, 'getByPosition'])->name('ads.position');
Route::get('/ad/click/{id}', [AdvertisementController::class, 'recordClick'])->name('ad.click');

// Banner Routes
Route::get('/api/banners', [BannerController::class, 'getActiveBanners'])->name('api.banners');
Route::get('/api/banners/{id}', [BannerController::class, 'getBanner'])->name('api.banners.show');

// Temporary debug route - remove after testing
Route::get('/debug-images', function() {
    $products = \App\Models\Product::with('images')->take(5)->get();
    $debug = [];
    foreach ($products as $product) {
        $debug[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'images' => $product->images->map(function($image) {
                return [
                    'path' => $image->path,
                    'is_primary' => $image->is_primary,
                    'full_url' => Storage::url($image->path),
                    'exists' => Storage::exists($image->path)
                ];
            })->toArray()
        ];
    }
    return response()->json($debug);
});

// Check customers table structure - remove after testing
Route::get('/debug-customers', function() {
    $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM customers');
    return response()->json($columns);
});

// Page routes
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');
Route::get('/dynamic/{slug}', [PageController::class, 'dynamicPage'])->name('dynamic.page');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact/submit', [PageController::class, 'storeContactForm'])->name('contact.submit');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Categories
    Route::resource('categories', CategoryController::class);
    
    // Subcategories AJAX route (must be before resource route to avoid conflict)
    Route::get('subcategories/{categoryId}', [App\Http\Controllers\Admin\ProductController::class, 'getSubcategories'])->name('subcategories.get');

    // Subcategories resource routes
    Route::resource('subcategories', App\Http\Controllers\Admin\SubCategoryController::class);

    // Products
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    Route::get('specification-types/{categoryId}', [App\Http\Controllers\Admin\ProductController::class, 'getSpecificationTypes'])->name('specification.types');
    
    // Specification Types
    Route::get('category/{categoryId}/specifications', [AdminController::class, 'specificationTypes'])->name('specifications');
    Route::get('category/{categoryId}/specifications/create', [AdminController::class, 'createSpecificationType'])->name('specifications.create');
    Route::post('category/{categoryId}/specifications', [AdminController::class, 'storeSpecificationType'])->name('specifications.store');
    Route::get('category/{categoryId}/specifications/{id}/edit', [AdminController::class, 'editSpecificationType'])->name('specifications.edit');
    Route::put('category/{categoryId}/specifications/{id}', [AdminController::class, 'updateSpecificationType'])->name('specifications.update');
    Route::delete('category/{categoryId}/specifications/{id}', [AdminController::class, 'destroySpecificationType'])->name('specifications.destroy');

    // Contact Messages Management
    Route::get('/contact', [ContactMessageController::class, 'index'])->name('contact.index');
    Route::get('/contact/{id}', [ContactMessageController::class, 'show'])->name('contact.show');
    Route::post('/contact/{id}/reply', [ContactMessageController::class, 'reply'])->name('contact.reply');
    Route::delete('/contact/{id}', [ContactMessageController::class, 'destroy'])->name('contact.destroy');
    
    // Settings Management
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::put('/settings/seo', [SettingController::class, 'updateSeoSettings'])->name('settings.seo.update');
    Route::put('/settings/contact', [SettingController::class, 'updateContactSettings'])->name('settings.contact.update');
    Route::put('/settings/social', [SettingController::class, 'updateSocialSettings'])->name('settings.social.update');
    
    // Menu Management
    Route::resource('menus', App\Http\Controllers\Admin\MenuController::class);
    
    // API Import Management
    Route::get('/imports', [ApiImportController::class, 'index'])->name('imports.index');
    Route::post('/imports/run', [ApiImportController::class, 'runImport'])->name('imports.run');

    // Brand routes
    Route::resource('brands', AdminBrandController::class);
    
    // Inventory Management
    Route::get('/inventory/{id}/adjust', [App\Http\Controllers\Admin\InventoryController::class, 'showAdjust'])->name('inventory.adjust');
    Route::match(['post', 'patch'], '/inventory/{id}/update-stock', [App\Http\Controllers\Admin\InventoryController::class, 'updateStock'])->name('inventory.update-stock');
    Route::get('/inventory/{id}/history', [App\Http\Controllers\Admin\InventoryController::class, 'history'])->name('inventory.history');
    Route::get('/inventory/export', [App\Http\Controllers\Admin\InventoryController::class, 'export'])->name('inventory.export');
    Route::resource('inventory', App\Http\Controllers\Admin\InventoryController::class);
    
    // Customer Management
    Route::get('/customers/export', [App\Http\Controllers\Admin\CustomerController::class, 'export'])->name('customers.export');
    Route::put('/customers/{customer}/status', [App\Http\Controllers\Admin\CustomerController::class, 'updateStatus'])->name('customers.update-status');
    Route::resource('customers', App\Http\Controllers\Admin\CustomerController::class);
    
    // Order Management
    Route::get('/orders/export', [App\Http\Controllers\Admin\OrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/{order}/invoice', [App\Http\Controllers\Admin\OrderController::class, 'generateInvoice'])->name('orders.invoice');
    Route::patch('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('/orders/{order}/payment', [App\Http\Controllers\Admin\OrderController::class, 'updatePayment'])->name('orders.update-payment');
    Route::post('/orders/{order}/send-email', [App\Http\Controllers\Admin\OrderController::class, 'sendEmail'])->name('orders.send-email');
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class);
    Route::post('/orders/{orderId}', [App\Http\Controllers\Admin\OrderController::class, 'getOrderItem']);
    
    // Return Management
    Route::get('returns/get-order-items/{order}', [App\Http\Controllers\Admin\ReturnController::class, 'getOrderItems'])->name('returns.get-order-items');
    Route::put('returns/{return}/status', [App\Http\Controllers\Admin\ReturnController::class, 'updateStatus'])->name('returns.update-status');
    Route::resource('returns', App\Http\Controllers\Admin\ReturnController::class);

    // Advertisement routes
    Route::get('advertisements/statistics', [App\Http\Controllers\Admin\AdvertisementController::class, 'statistics'])->name('advertisements.statistics');
    Route::get('advertisements/export', [App\Http\Controllers\Admin\AdvertisementController::class, 'export'])->name('advertisements.export');
    Route::get('advertisements/test', [App\Http\Controllers\Admin\AdvertisementController::class, 'test'])->name('advertisements.test');
    Route::post('advertisements/{advertisement}/status', [App\Http\Controllers\Admin\AdvertisementController::class, 'updateStatus'])->name('advertisements.update-status');
    Route::resource('advertisements', App\Http\Controllers\Admin\AdvertisementController::class);

    // Banner routes
    Route::patch('banners/{banner}/toggle-status', [App\Http\Controllers\Admin\BannerController::class, 'toggleStatus'])->name('banners.toggle-status');
    Route::post('banners/update-order', [App\Http\Controllers\Admin\BannerController::class, 'updateOrder'])->name('banners.update-order');
    Route::get('banners/demo', [App\Http\Controllers\Admin\BannerController::class, 'demo'])->name('banners.demo');
    Route::resource('banners', App\Http\Controllers\Admin\BannerController::class);

    // Pages Management
    Route::resource('pages', App\Http\Controllers\Admin\PageController::class);

    // Resource routes
    Route::resource('models', \App\Http\Controllers\Admin\ModelController::class);
    
    // API routes for cascading dropdowns
    Route::get('api/categories-by-brand', [\App\Http\Controllers\Admin\ModelController::class, 'getCategoriesByBrand'])
        ->name('api.categories-by-brand');
    Route::get('api/subcategories-by-category', [\App\Http\Controllers\Admin\ModelController::class, 'getSubcategoriesByCategory'])
        ->name('api.subcategories-by-category');
    Route::get('models-by-subcategory/{subcategory_id}', [\App\Http\Controllers\Admin\ModelController::class, 'getModelsBySubcategory'])
        ->name('models-by-subcategory');
});

// Admin User Management Routes
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::get('/admin/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
    Route::put('/admin/users/update-role', [App\Http\Controllers\Admin\UserController::class, 'updateRole'])->name('admin.users.update-role');
    Route::delete('/admin/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
});

// Product routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/category/{slug}/all', [ProductController::class, 'categoryProducts'])->name('category.all');
Route::get('/{category}-by-brand/{brand}', [ProductController::class, 'productsByBrand'])->name('products.by.brand');
Route::get('/{category}-by-brand/{brand}/{model}', [ProductController::class, 'productsByBrandAndModel'])->name('products.by.brand.model');
Route::get('/api/newest-products/{limit?}', [HomeController::class, 'getNewestProducts'])->name('api.newest-products');

// Debug routes - remove in production
Route::get('/debug-returns/{order}', [App\Http\Controllers\Admin\ReturnController::class, 'getOrderItems'])->name('debug.returns.get-order-items');

// Test route for product creation - remove in production
Route::get('/test-product-create', function() {
    try {
        $product = new \App\Models\Product();
        $product->name = 'Test Product ' . time();
        $product->slug = \Illuminate\Support\Str::slug('Test Product ' . time());
        $product->category_id = \App\Models\Category::first()->id;
        $product->model = 'Test-' . rand(1000, 9999);
        $product->price = 99.99;
        $product->description = 'This is a test product created via direct route.';
        $product->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'product' => $product->toArray()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error creating product: ' . $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});

// API Routes for AJAX requests
Route::prefix('api')->name('api.')->group(function () {
    // Cart API
    Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'addToCartAjax'])->name('cart.add');
    Route::post('/cart/update', [App\Http\Controllers\CartController::class, 'updateCartAjax'])->name('cart.update');
    Route::post('/cart/remove/{id}', [App\Http\Controllers\CartController::class, 'removeFromCartAjax'])->name('cart.remove');
    
    // Compare API
    Route::post('/compare/add/{id}', [App\Http\Controllers\CompareController::class, 'addToCompareAjax'])->name('compare.add');
    Route::post('/compare/remove/{id}', [App\Http\Controllers\CompareController::class, 'removeFromCompareAjax'])->name('compare.remove');
    Route::post('/compare/clear', [App\Http\Controllers\CompareController::class, 'clearCompareAjax'])->name('compare.clear');

    // Existing API routes
    Route::get('/newest-products/{limit?}', [HomeController::class, 'getNewestProducts'])->name('newest-products');
});

// Test route for debugging
Route::get('/test-product-creation', function () {
    try {
        // Create a test product
        $product = new \App\Models\Product([
            'name' => 'Test Product ' . uniqid(),
            'category_id' => 1, // Make sure this category exists
            'brand' => 'Test Brand',
            'model' => 'Test Model',
            'price' => 99.99,
            'description' => 'Test description',
            'slug' => 'test-product-' . uniqid()
        ]);
        
        $product->save();
        
        return "Product saved successfully with ID: " . $product->id;
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage() . " in " . $e->getFile() . " at line " . $e->getLine();
    }
});
