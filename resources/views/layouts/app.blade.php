@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Cache;
    $siteTitle = \App\Models\Setting::get('site_title', config('app.name', 'Product Compare'));
    $siteLogo = \App\Models\Setting::get('site_logo');
    $favicon = \App\Models\Setting::get('favicon');
    $primaryColor = \App\Models\Setting::get('primary_color', '#0d6efd');
    
    // Promotional banner settings
    $bannerEnabled = (bool)\App\Models\Setting::get('promotional_banner_enabled', false);
    $bannerText = \App\Models\Setting::get('promotional_banner_text', '');
    $bannerBgColor = \App\Models\Setting::get('promotional_banner_bg_color', '#ffc107');
    $bannerTextColor = \App\Models\Setting::get('promotional_banner_text_color', '#000000');
    $bannerLink = \App\Models\Setting::get('promotional_banner_link');

    // Product slider settings
    $sliderEnabled = (bool)\App\Models\Setting::get('product_slider_enabled', true);
    $sliderTitle = \App\Models\Setting::get('product_slider_title', 'New Arrivals');
    $sliderSubtitle = \App\Models\Setting::get('product_slider_subtitle', 'Check out our latest products');
    $sliderCount = (int)\App\Models\Setting::get('product_slider_count', 10);
    $sliderBgColor = \App\Models\Setting::get('product_slider_bg_color', '#f8f9fa');
    $sliderAutoplay = (bool)\App\Models\Setting::get('product_slider_autoplay', true);
    
    // Load brands for mega menu
    $allBrands = Cache::remember('all_brands', 300, function () {
        return \App\Models\Brand::where('is_active', true)->orderBy('name')->get();
    });
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $siteTitle }} - @yield('title')</title>
    
    @if($favicon)
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $favicon) }}">
    @endif
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.40.0/dist/apexcharts.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css">
    <link rel="stylesheet" href="{{ asset('css/banner-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @if(request()->routeIs('about'))
    <link rel="stylesheet" href="{{ asset('css/about.css') }}">
    @endif
    @if(request()->routeIs('contact'))
    <link rel="stylesheet" href="{{ asset('css/contact.css') }}">
    @endif
    @stack('styles')
    @yield('styles')
    
    <style>
        :root {
            --bs-primary: {{ $primaryColor }};
            --bs-primary-rgb: {{ implode(', ', sscanf($primaryColor, "#%02x%02x%02x")) }};
        }

        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        .bg-primary {
            background-color: var(--bs-primary) !important;
        }

        .text-primary {
            color: var(--bs-primary) !important;
        }
        
        /* Promotional Banner Styles */
        .promotional-banner {
            padding: 10px 0;
            text-align: center;
            font-weight: 500;
            position: relative;
        }
        
        .promotional-banner .container {
            position: relative;
        }
        
        .promotional-banner a {
            color: inherit;
            text-decoration: none;
            display: block;
        }
        
        .promotional-banner a:hover {
            text-decoration: underline;
        }
        
        .promotional-banner .close-banner {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            font-size: 16px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.2s;
        }
        
        .promotional-banner .close-banner:hover {
            opacity: 1;
        }
        
        /* Mega Menu Styles */
        .mega-menu {
            width: 100%;
            left: 0;
            right: 0;
            padding: 1.5rem 0;
            border-radius: 0;
            border-top: 3px solid var(--bs-primary);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .mega-menu-column {
            padding: 0 1.5rem;
        }
        
        .mega-menu-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--bs-primary);
        }
        
        .mega-menu .dropdown-item {
            padding: 8px 5px;
            font-size: 0.95rem;
            border-radius: 5px;
            transition: all 0.2s;
        }
        
        .mega-menu .dropdown-item:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
            transform: translateX(3px);
        }
        
        .mega-menu .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 5px;
            font-size: 0.9em;
        }
        
        .mega-menu .brand-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        
        .mega-menu .brand-item {
            padding: 5px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-radius: 5px;
            transition: all 0.2s;
        }
        
        .mega-menu .brand-item:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
            transform: translateY(-2px);
        }
        
        .mega-menu .brand-logo {
            height: 40px;
            width: 40px;
            object-fit: contain;
            margin-bottom: 5px;
        }
        
        .mega-menu .brand-name {
            font-size: 0.8rem;
            font-weight: 500;
            margin: 0;
        }
        
        .mega-menu .see-all {
            display: block;
            text-align: right;
            margin-top: 10px;
            font-weight: 500;
            color: var(--bs-primary);
        }
        
        /* Position the dropdown to show on hover */
        .dropdown-mega:hover .dropdown-menu {
            display: block;
        }
        
        /* Makes the dropdown stay open while hovering over it */
        .dropdown-mega .dropdown-menu:hover {
            display: block;
        }
        
        .compare-badge {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 999;
        }
        .product-card {
            height: 100%;
            transition: all 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .spec-table th {
            width: 30%;
        }
        .chart-container {
            height: 350px;
            width: 100%;
        }
        .ad-container {
            margin-bottom: 1.5rem;
            overflow: hidden;
            border-radius: 0.25rem;
        }
        .sidebar-ad {
            border-radius: 0.25rem;
            overflow: hidden;
        }
        .banner-ad {
            border-radius: 0.25rem;
            overflow: hidden;
        }
        
        /* Product Slider Styles */
        .product-slider-section {
            padding: 2rem 0;
            margin-bottom: 1.5rem;
        }
        
        .product-slider-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .product-slider-header h2 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .product-slider-header p {
            color: #6c757d;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .product-slider {
            position: relative;
            padding: 0 40px;
        }
        
        .product-slide {
            padding: 15px;
        }
        
        .product-slide-inner {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .product-slide-inner:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .product-slide-image {
            position: relative;
            padding-top: 75%; /* 4:3 Aspect Ratio */
            overflow: hidden;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        
        .product-slide-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .product-slide-category {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .product-slide-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.5rem;
        }
        
        .product-slide-price {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .product-slide-regular-price {
            font-size: 1.1rem;
            font-weight: 700;
        }
        
        .product-slide-discount-price {
            font-size: 0.9rem;
            color: #dc3545;
            text-decoration: line-through;
        }
        
        .product-slide-button {
            margin-top: auto;
        }
        
        .swiper-button-next, .swiper-button-prev {
            color: var(--bs-primary);
            background: #fff;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            opacity: 0.9;
        }
        
        .swiper-button-next:after, .swiper-button-prev:after {
            font-size: 16px;
            font-weight: bold;
        }
        
        .swiper-button-next:hover, .swiper-button-prev:hover {
            opacity: 1;
        }
        
        .swiper-pagination-bullet {
            background: var(--bs-primary);
        }
        
        @media (max-width: 768px) {
            .product-slider {
                padding: 0 25px;
            }
            
            .product-slide-title {
                font-size: 0.9rem;
            }
        }
        
        /* Skeleton Loader */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 4px;
        }
        
        .skeleton-loader .product-slide-image {
            height: 150px;
        }
        
        .skeleton-loader .product-slide-category {
            height: 12px;
            width: 60%;
            margin-bottom: 10px;
        }
        
        .skeleton-loader .product-slide-title {
            height: 40px;
            margin-bottom: 15px;
        }
        
        .skeleton-loader .product-slide-regular-price {
            height: 24px;
            width: 80px;
        }
        
        .skeleton-loader .product-slide-button {
            height: 32px;
            margin-top: 15px;
        }
        
        @keyframes loading {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }
        
        /* Quick View Styles */
        .quick-view-btn {
            width: 38px;
            height: 38px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px 0 0 4px;
            transition: all 0.2s;
        }
        
        .quick-view-btn:hover {
            background-color: #0dcaf0;
        }
        
        #quickViewModal .product-image-container {
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        #quickViewModal img {
            max-height: 100%;
            object-fit: contain;
        }
        
        /* End Quick View Styles */
        
        /* Mega menu styles */
        .dropdown-mega .mega-menu {
            width: 100%;
            border-radius: 0;
            margin-top: 0;
            border-top: 1px solid rgba(0,0,0,.1);
        }
        
        .mega-menu-header {
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 5px;
        }
        
        .mega-menu-column {
            margin-bottom: 15px;
        }
        
        .mega-menu .dropdown-item {
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 4px;
        }
        
        .mega-menu .dropdown-item:hover {
            background-color: #f8f9fa;
        }
        
        .see-all {
            display: block;
            margin-top: 10px;
            color: #007bff;
            font-size: 14px;
            font-weight: 500;
        }
        
        /* Brand grid styles */
        .brand-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .brand-item {
            text-align: center;
            color: #333;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .brand-item:hover {
            transform: translateY(-3px);
            color: #007bff;
        }
        
        .brand-logo {
            width: 60px;
            height: 60px;
            margin-bottom: 5px;
            object-fit: contain;
        }
        
        .brand-name {
            font-size: 12px;
            display: block;
        }
        
        /* Search box styles */
        .search-box-wrapper {
            position: relative;
        }
        
        /* Dropdown submenu support */
        .dropdown-submenu {
            position: relative;
        }
        
        .dropdown-submenu .submenu-indicator {
            font-size: 10px;
        }
        
        .dropdown-submenu .submenu {
            top: 0;
            left: 100%;
            margin-top: -1px;
            display: none;
            min-width: 200px;
            border-radius: 4px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
        }
        
        .dropdown-submenu:hover .submenu {
            display: block;
            opacity: 1;
            visibility: visible;
        }
        
        .dropdown-submenu > a:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
        }
        
        .dropdown-submenu .dropdown-item {
            padding: 10px 15px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .dropdown-submenu .dropdown-item i {
            margin-right: 8px;
            width: 16px;
            text-align: center;
        }
        
        /* Mobile support for submenu */
        @media (max-width: 991.98px) {
            .dropdown-submenu .submenu {
                left: 0;
                position: relative;
                box-shadow: none;
                margin-left: 15px;
                border-left: 2px solid #ddd;
                opacity: 1;
                visibility: visible;
            }
        }
        
        /* Custom Child Menu Animations */
        .dropdown-submenu .submenu {
            transform: translateX(20px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform, opacity;
        }
        
        .dropdown-submenu:hover .submenu {
            transform: translateX(0);
        }
        
        .dropdown-mega .dropdown-menu {
            transform: translateY(15px);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
            will-change: transform, opacity;
        }
        
        .dropdown-mega:hover .dropdown-menu {
            transform: translateY(0);
        }
        
        /* Child menu item hover effect */
        .mega-menu .dropdown-item {
            position: relative;
            transition: all 0.25s ease;
        }
        
        .mega-menu .dropdown-item:not(.submenu .dropdown-item):after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: var(--bs-primary);
            transition: all 0.25s ease;
        }
        
        .mega-menu .dropdown-item:not(.submenu .dropdown-item):hover:after {
            width: 60%;
            left: 20%;
        }
    </style>
</head>
<body>
    @if($bannerEnabled)
    <div class="promotional-banner" style="background-color: {{ $bannerBgColor }}; color: {{ $bannerTextColor }}">
        <div class="container">
            @if($bannerLink)
                <a href="{{ $bannerLink }}">{{ $bannerText }}</a>
            @else
                {{ $bannerText }}
            @endif
            <button type="button" class="close-banner" aria-label="Close" style="color: {{ $bannerTextColor }}">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                @if($siteLogo)
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ $siteTitle }}" height="40">
                @else
                    <i class="fas fa-poll me-2"></i>{{ $siteTitle }}
                @endif
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @foreach($mainMenu as $menuItem)
                        @if($menuItem->children->isEmpty() && $menuItem->name !== 'Brands')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs($menuItem->route_name) ? 'active' : '' }}" 
                                   href="{{ $menuItem->is_dynamic_page ? route('dynamic.page', $menuItem->slug) : ($menuItem->url ?? ($menuItem->route_name ? route($menuItem->route_name) : '#')) }}">
                                    @if($menuItem->icon)
                                        <i class="{{ $menuItem->icon }} me-1"></i>
                                    @endif
                                    {{ $menuItem->name }}
                                </a>
                            </li>
                        @elseif($menuItem->name === 'Brands')
                            <li class="nav-item dropdown dropdown-mega position-static">
                                <a class="nav-link dropdown-toggle" href="#" id="brandsDropdown" 
                                   role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-trademark me-1"></i>
                                    Brands
                                </a>
                                <div class="dropdown-menu mega-menu p-4" aria-labelledby="brandsDropdown">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <h5 class="mega-menu-header">Popular Brands</h5>
                                                <div class="brand-grid">
                                                    @foreach($allBrands->take(15) as $brand)
                                                        <a href="{{ route('brand.show', $brand->slug) }}" class="brand-item">
                                                            @if($brand->logo)
                                                                <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" class="brand-logo">
                                                            @else
                                                                <div class="brand-logo d-flex align-items-center justify-content-center bg-light rounded-circle">
                                                                    <i class="fas fa-trademark text-primary"></i>
                                                                </div>
                                                            @endif
                                                            <span class="brand-name">{{ $brand->name }}</span>
                                                        </a>
                                                    @endforeach
                                                </div>
                                                <a href="{{ route('brands.all') }}" class="see-all">See all brands <i class="fas fa-arrow-right ms-1"></i></a>
                                            </div>
                                            <div class="col-md-3">
                                                <h5 class="mega-menu-header">Featured</h5>
                                                <div class="card border-0 shadow-sm">
                                                    @if($allBrands->isNotEmpty() && $allBrands->first()->logo)
                                                        <img src="{{ asset('storage/' . $allBrands->first()->logo) }}" class="card-img-top p-3" alt="Featured Brand">
                                                    @endif
                                                    <div class="card-body">
                                                        <h6 class="card-title">Browse by Category</h6>
                                                        <p class="card-text small">Find the best brands in each category for your needs.</p>
                                                        <a href="{{ route('categories') }}" class="btn btn-sm btn-primary">Explore Categories</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @else
                            <li class="nav-item dropdown dropdown-mega position-static">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown{{ $menuItem->id }}" 
                                   role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    @if($menuItem->icon)
                                        <i class="{{ $menuItem->icon }} me-1"></i>
                                    @endif
                                    {{ $menuItem->name }}
                                </a>
                                <div class="dropdown-menu mega-menu p-4" aria-labelledby="navbarDropdown{{ $menuItem->id }}">
                                    <div class="container">
                                        <div class="row">
                                            @if($menuItem->children->count() > 0)
                                                <div class="col-md-9">
                                                    <div class="row">
                                                        @foreach($menuItem->children->chunk(4) as $chunk)
                                                            <div class="col-md-4 mega-menu-column">
                                                                @foreach($chunk as $child)
                                                                    <div class="dropdown-submenu">
                                                                        <a class="dropdown-item d-flex justify-content-between align-items-center" 
                                                                           href="{{ $child->is_dynamic_page ? route('dynamic.page', $child->slug) : ($child->url ?? ($child->route_name ? route($child->route_name) : '#')) }}">
                                                                            <span>
                                                                                @if($child->icon)
                                                                                    <i class="{{ $child->icon }} me-2"></i>
                                                                                @endif
                                                                                {{ $child->name }}
                                                                            </span>
                                                                            @if($child->children && $child->children->count() > 0)
                                                                            <i class="fas fa-chevron-right submenu-indicator ms-2"></i>
                                                                            @endif
                                                                        </a>
                                                                        
                                                                        @if($child->children && $child->children->count() > 0)
                                                                        <div class="submenu dropdown-menu">
                                                                            @foreach($child->children as $grandchild)
                                                                            <a class="dropdown-item" 
                                                                                href="{{ $grandchild->is_dynamic_page ? route('dynamic.page', $grandchild->slug) : ($grandchild->url ?? ($grandchild->route_name ? route($grandchild->route_name) : '#')) }}">
                                                                                @if($grandchild->icon)
                                                                                    <i class="{{ $grandchild->icon }} me-2"></i>
                                                                                @else
                                                                                    <i class="fas fa-angle-right me-2 text-secondary"></i>
                                                                                @endif
                                                                                <span>{{ $grandchild->name }}</span>
                                                                            </a>
                                                                            @endforeach
                                                                        </div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="p-3 bg-light rounded">
                                                        <h5 class="mega-menu-header">{{ $menuItem->name }}</h5>
                                                        <p class="mb-2 small">Explore all options in our {{ strtolower($menuItem->name) }} section.</p>
                                                        @if($menuItem->category_id && $menuItem->category && $menuItem->category->slug)
                                                            <a href="{{ route('category.all', ['slug' => $menuItem->category->slug]) }}" class="btn btn-sm btn-primary">
                                                                View All <i class="fas fa-arrow-right ms-1"></i>
                                                            </a>
                                                        @elseif($menuItem->slug)
                                                            <a href="{{ route('category.all', ['slug' => $menuItem->slug]) }}" class="btn btn-sm btn-primary">
                                                                View All <i class="fas fa-arrow-right ms-1"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ $menuItem->url ?? ($menuItem->route_name ? route($menuItem->route_name) : '#') }}" class="btn btn-sm btn-primary">
                                                                View All <i class="fas fa-arrow-right ms-1"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-12 text-center py-4">
                                                    <p>No items found in this category.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>
                
                <div class="d-flex">
                    @auth
                        <div class="dropdown me-3">
                            <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a href="{{ route('profile.show') }}" class="dropdown-item">
                                        <i class="fas fa-user-circle me-1"></i>My Profile
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('contact.my-messages') }}" class="dropdown-item">
                                        <i class="fas fa-envelope me-1"></i>My Messages
                                        @php
                                            $unreadMessagesCount = Auth::user()->contactMessages()->whereIn('status', ['read', 'replied'])->count();
                                        @endphp
                                        @if($unreadMessagesCount > 0)
                                            <span class="badge bg-success rounded-pill ms-2">{{ $unreadMessagesCount }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-warning">
                                <i class="fas fa-user-cog me-1"></i>Admin Panel
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light me-2">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-light">
                            <i class="fas fa-user-plus me-1"></i>Register
                        </a>
                    @endauth
                    
                    <!-- Cart Icon -->
                    <a href="{{ route('cart.index') }}" class="btn btn-success ms-2 position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        @php
                            $cartCount = session('cart') ? count(session('cart')) : 0;
                        @endphp
                        @if($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </nav>

    @if($sliderEnabled)
    <div class="product-slider-section" style="background-color: {{ $sliderBgColor }};">
        <div class="container">
             <!-- SWAPPED: Banner Slider Section SECOND -->
     <div id="homeBannerSlider" class="mb-4" data-banner-slider data-autoplay="true" data-interval="5000"></div>
            <div class="product-slider-header">
                <h2>{{ $sliderTitle }}</h2>
                <p>{{ $sliderSubtitle }}</p>
            </div>
            
            <div class="product-slider">
                <div class="swiper product-swiper">
                    <div class="swiper-wrapper" id="product-slider-items">
                        <!-- Products will be loaded here via JavaScript -->
                        <div class="swiper-slide product-slide">
                            <div class="product-slide-inner skeleton-loader">
                                <div class="product-slide-image skeleton"></div>
                                <div class="product-slide-category skeleton"></div>
                                <div class="product-slide-title skeleton"></div>
                                <div class="product-slide-price">
                                    <div class="product-slide-regular-price skeleton"></div>
                                </div>
                                <div class="product-slide-button skeleton"></div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="container my-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <div class="compare-badge" id="compareCounter">
        @php $compareCount = session('compare_list') ? count(session('compare_list')) : 0; @endphp
        @if($compareCount > 0)
            <a href="{{ route('compare') }}" class="btn btn-danger rounded-circle position-relative p-3">
                <i class="fas fa-balance-scale fa-lg"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">
                    {{ $compareCount }}
                </span>
            </a>
        @endif
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5>{{ $siteTitle }}</h5>
                    <p>Compare electronic products like laptops, PCs, and mobile phones to make informed purchase decisions.</p>
                    
                    @php
                        $showSocialFollow = \App\Helpers\SettingsHelper::get('show_social_follow', true);
                        $socialMedia = \App\Helpers\SettingsHelper::getSocialMedia();
                    @endphp
                    
                    @if($showSocialFollow && count($socialMedia) > 0)
                    <div class="social-links mt-3">
                        @foreach($socialMedia as $key => $social)
                            <a href="{{ $social['url'] }}" target="_blank" class="social-icon me-2" data-bs-toggle="tooltip" title="{{ $social['label'] }}">
                                <i class="{{ $social['icon'] }}"></i>
                            </a>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        @foreach($footerMenu as $menuItem)
                            <li>
                                <a class="text-white" 
                                   href="{{ $menuItem->is_dynamic_page ? route('dynamic.page', $menuItem->slug) : ($menuItem->url ?? ($menuItem->route_name ? route($menuItem->route_name) : '#')) }}">
                                    @if($menuItem->icon)
                                        <i class="{{ $menuItem->icon }} me-1"></i>
                                    @endif
                                    {{ $menuItem->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact Us</h5>
                    @php
                        $contactInfo = \App\Helpers\SettingsHelper::getContactInfo();
                    @endphp
                    
                    <ul class="list-unstyled contact-info">
                        @if(isset($contactInfo['phone_number']))
                        <li class="mb-2">
                            <i class="{{ $contactInfo['phone_number']['icon'] }} me-2"></i>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactInfo['phone_number']['value']) }}" class="text-white">
                                {{ $contactInfo['phone_number']['value'] }}
                            </a>
                        </li>
                        @endif
                        
                        @if(isset($contactInfo['contact_email']))
                        <li class="mb-2">
                            <i class="{{ $contactInfo['contact_email']['icon'] }} me-2"></i>
                            <a href="mailto:{{ $contactInfo['contact_email']['value'] }}" class="text-white">
                                {{ $contactInfo['contact_email']['value'] }}
                            </a>
                        </li>
                        @endif
                        
                        @if(isset($contactInfo['business_hours']))
                        <li class="mb-2">
                            <i class="{{ $contactInfo['business_hours']['icon'] }} me-2"></i>
                            {{ $contactInfo['business_hours']['value'] }}
                        </li>
                        @endif
                        
                        @if(isset($contactInfo['address']))
                        <li>
                            <i class="{{ $contactInfo['address']['icon'] }} me-2"></i>
                            {{ $contactInfo['address']['value'] }}
                        </li>
                        @endif
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Admin</h5>
                    <ul class="list-unstyled">
                        @auth
                            @if(Auth::user()->is_admin)
                                <li><a class="text-white" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li><a class="text-white" href="{{ route('admin.categories.index') }}">Categories</a></li>
                                <li><a class="text-white" href="{{ route('admin.products.index') }}">Products</a></li>
                                <li><a class="text-white" href="{{ route('admin.menus.index') }}">Menu Management</a></li>
                                <li><a class="text-white" href="{{ route('admin.advertisements.index') }}">Advertisements</a></li>
                            @endif
                        @endauth
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; {{ date('Y') }} {{ $siteTitle }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.40.0/dist/apexcharts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    
    <!-- AJAX CSRF Setup -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    
    <script src="{{ asset('js/advertisements.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            $('.add-to-compare').click(function(e) {
                e.preventDefault();
                
                const productId = $(this).data('product-id');
                
                $.ajax({
                    url: '{{ route("add.to.compare") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId
                    },
                    success: function(response) {
                        if (response.success) {
                            updateCompareCounter(response.count);
                            alert(response.message);
                        } else {
                            alert(response.message);
                        }
                    }
                });
            });
            
            $('.remove-from-compare').click(function(e) {
                e.preventDefault();
                
                const productId = $(this).data('product-id');
                
                $.ajax({
                    url: '{{ route("remove.from.compare") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId
                    },
                    success: function(response) {
                        if (response.success) {
                            updateCompareCounter(response.count);
                            location.reload();
                        }
                    }
                });
            });
        });
        
        function updateCompareCounter(count) {
            if (count > 0) {
                $('#compareCounter').html(`
                    <a href="{{ route('compare') }}" class="btn btn-danger rounded-circle position-relative p-3">
                        <i class="fas fa-balance-scale fa-lg"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">
                            ${count}
                        </span>
                    </a>
                `);
            } else {
                $('#compareCounter').html('');
            }
        }
    </script>
    
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Promotional banner close functionality
        $(document).ready(function() {
            // Check if the banner was closed before
            if (getCookie('promotional_banner_closed') !== 'true') {
                $('.promotional-banner').show();
            } else {
                $('.promotional-banner').hide();
            }
            
            // Close banner button click
            $('.close-banner').on('click', function() {
                $(this).closest('.promotional-banner').slideUp();
                setCookie('promotional_banner_closed', 'true', 1); // Set cookie to expire in 1 day
            });
            
            // Helper function to set a cookie
            function setCookie(name, value, days) {
                var expires = "";
                if (days) {
                    var date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "") + expires + "; path=/";
            }
            
            // Helper function to get a cookie
            function getCookie(name) {
                var nameEQ = name + "=";
                var ca = document.cookie.split(';');
                for(var i=0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0)==' ') c = c.substring(1,c.length);
                    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
                }
                return null;
            }
        });
    </script>
    
    @if($sliderEnabled)
    <script>
        // Initialize product slider
        $(document).ready(function() {
            // Load newest products
            $.ajax({
                url: '{{ route("api.newest-products", ["limit" => $sliderCount]) }}',
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        // Clear skeleton loader
                        $('#product-slider-items').empty();
                        
                        // Add products to slider
                        $.each(response.data, function(index, product) {
                            let priceHtml = '';
                            
                            if (product.discount_price) {
                                priceHtml = `
                                    <div class="product-slide-regular-price">${formatPrice(product.discount_price)}</div>
                                    <div class="product-slide-discount-price">${formatPrice(product.price)}</div>
                                `;
                            } else {
                                priceHtml = `
                                    <div class="product-slide-regular-price">${formatPrice(product.price)}</div>
                                `;
                            }
                            
                            let slide = `
                                <div class="swiper-slide product-slide">
                                    <div class="product-slide-inner">
                                        <a href="${product.url}" class="product-slide-image">
                                            <img src="${product.image}" alt="${product.name}">
                                        </a>
                                        <div class="product-slide-category">${product.category || 'Uncategorized'}</div>
                                        <h3 class="product-slide-title">
                                            <a href="${product.url}" class="text-decoration-none text-dark">${product.name}</a>
                                        </h3>
                                        <div class="product-slide-price">
                                            ${priceHtml}
                                        </div>
                                        <div class="product-slide-button">
                                            <div class="btn-group w-100">
                                                <button class="btn btn-info btn-sm quick-view-btn" 
                                                        data-product-id="${product.id}"
                                                        data-product-slug="${product.slug || ''}"
                                                        data-product-name="${product.name}"
                                                        data-product-price="${product.price || ''}"
                                                        data-product-brand="${product.brand || ''}"
                                                        data-product-model="${product.model || ''}"
                                                        data-product-description="${product.description || ''}"
                                                        data-product-image="${product.image || ''}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#quickViewModal">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="${product.url}" class="btn btn-primary btn-sm flex-grow-1">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            $('#product-slider-items').append(slide);
                        });
                        
                        // Initialize Swiper
                        initProductSwiper();
                    } else {
                        // If no products found, hide the slider section
                        $('.product-slider-section').hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading newest products:', error);
                    // Hide the slider section on error
                    $('.product-slider-section').hide();
                }
            });
            
            // Helper function to format price
            function formatPrice(price) {
                return '$' + parseFloat(price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }
            
            // Initialize Swiper
            function initProductSwiper() {
                new Swiper('.product-swiper', {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    loop: true,
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    autoplay: {{ $sliderAutoplay ? 'true' : 'false' }} ? {
                        delay: 5000,
                        disableOnInteraction: false,
                    } : false,
                    breakpoints: {
                        // when window width is >= 576px
                        576: {
                            slidesPerView: 2,
                        },
                        // when window width is >= 768px
                        768: {
                            slidesPerView: 3,
                        },
                        // when window width is >= 992px
                        992: {
                            slidesPerView: 4,
                        },
                        // when window width is >= 1200px
                        1200: {
                            slidesPerView: 5,
                        },
                    }
                });
            }
            
            // Quick View Modal functionality
            const quickViewModal = document.getElementById('quickViewModal');
            if (quickViewModal) {
                // When the modal is about to be shown
                quickViewModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    
                    // Extract product info from data attributes
                    const productId = button.getAttribute('data-product-id');
                    const productSlug = button.getAttribute('data-product-slug');
                    const productName = button.getAttribute('data-product-name');
                    const productPrice = button.getAttribute('data-product-price');
                    const productBrand = button.getAttribute('data-product-brand');
                    const productModel = button.getAttribute('data-product-model');
                    const productDesc = button.getAttribute('data-product-description');
                    const productImage = button.getAttribute('data-product-image');
                    
                    // Update modal content
                    document.getElementById('quickViewName').textContent = productName;
                    document.getElementById('quickViewPrice').textContent = productPrice ? '$' + parseFloat(productPrice).toFixed(2) : 'Price not available';
                    
                    let brandModelText = '';
                    if (productBrand) brandModelText += productBrand;
                    if (productModel) brandModelText += ' | ' + productModel;
                    document.getElementById('quickViewBrandModel').textContent = brandModelText;
                    
                    document.getElementById('quickViewDescription').textContent = productDesc || 'No description available.';
                    
                    const imageEl = document.getElementById('quickViewImage');
                    if (productImage) {
                        imageEl.src = productImage;
                        imageEl.style.display = 'block';
                    } else {
                        imageEl.style.display = 'none';
                    }
                    
                    // Set product ID for the compare button
                    document.querySelector('.quick-view-compare').setAttribute('data-product-id', productId);
                    
                    // Set details link
                    const detailsBtn = document.getElementById('quickViewDetailsBtn');
                    detailsBtn.href = productSlug ? `/product/${productSlug}` : `/products/${productId}`;
                });
                
                // Handle add to compare from quick view
                document.querySelector('.quick-view-compare').addEventListener('click', function(e) {
                    const productId = this.getAttribute('data-product-id');
                    if (productId) {
                        // Implement the comparison logic
                        addToCompare(productId);
                    }
                });
            }
            
            // Function to add product to compare
            function addToCompare(productId) {
                $.ajax({
                    url: '{{ route("add.to.compare") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId
                    },
                    success: function(response) {
                        if (response.success) {
                            updateCompareCounter(response.count);
                            alert(response.message);
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }
        });
    </script>
    @endif
    
    @stack('scripts')
    @yield('scripts')
    
    <!-- Banner JS -->
    <script src="{{ asset('js/banner-slider.js') }}"></script>
    
    <!-- Quick View Modal -->
    <div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quickViewModalLabel">Product Quick View</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="product-image-container text-center mb-3">
                                <img id="quickViewImage" src="" alt="Product" class="img-fluid">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4 id="quickViewName" class="mb-3"></h4>
                            <div class="mb-3">
                                <h5 class="mb-1" id="quickViewPrice"></h5>
                                <div id="quickViewBrandModel" class="text-muted"></div>
                            </div>
                            <p id="quickViewDescription" class="mb-3"></p>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary quick-view-compare" data-product-id="" data-bs-dismiss="modal">
                                    <i class="fas fa-plus me-1"></i>Add to Compare
                                </button>
                                <a id="quickViewDetailsBtn" href="#" class="btn btn-outline-secondary">
                                    View Full Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Mobile navigation submenu toggle
        document.addEventListener('DOMContentLoaded', function() {
            // Handle promotional banner close
            const bannerCloseBtn = document.querySelector('.close-banner');
            if (bannerCloseBtn) {
                bannerCloseBtn.addEventListener('click', function() {
                    const banner = this.closest('.promotional-banner');
                    banner.style.display = 'none';
                });
            }
            
            // Mobile submenu toggle functionality
            const isMobile = window.innerWidth < 992;
            if (isMobile) {
                const submenuParents = document.querySelectorAll('.dropdown-submenu > a');
                
                submenuParents.forEach(function(item) {
                    item.addEventListener('click', function(e) {
                        // Only if there's a submenu
                        if (this.nextElementSibling && this.nextElementSibling.classList.contains('submenu')) {
                            e.preventDefault();
                            
                            // Toggle the submenu
                            const submenu = this.nextElementSibling;
                            const isVisible = submenu.style.display === 'block';
                            
                            // Close any other open submenus
                            document.querySelectorAll('.dropdown-submenu > .submenu').forEach(function(menu) {
                                if (menu !== submenu) {
                                    menu.style.display = 'none';
                                    // Reset the chevron icon
                                    const chevron = menu.previousElementSibling.querySelector('.submenu-indicator');
                                    if (chevron) {
                                        chevron.classList.remove('fa-chevron-down');
                                        chevron.classList.add('fa-chevron-right');
                                    }
                                }
                            });
                            
                            // Toggle this submenu
                            submenu.style.display = isVisible ? 'none' : 'block';
                            
                            // Rotate chevron icon
                            const chevron = this.querySelector('.submenu-indicator');
                            if (chevron) {
                                if (isVisible) {
                                    chevron.classList.remove('fa-chevron-down');
                                    chevron.classList.add('fa-chevron-right');
                                } else {
                                    chevron.classList.remove('fa-chevron-right');
                                    chevron.classList.add('fa-chevron-down');
                                }
                            }
                        }
                    });
                });
            }
        });
    </script>
</body>
</html> 