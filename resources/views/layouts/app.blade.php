@php
    use Illuminate\Support\Facades\Storage;
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
                        @if($menuItem->children->isEmpty())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs($menuItem->route_name) ? 'active' : '' }}" 
                                   href="{{ $menuItem->url ?? ($menuItem->route_name ? route($menuItem->route_name) : '#') }}">
                                    @if($menuItem->icon)
                                        <i class="{{ $menuItem->icon }} me-1"></i>
                                    @endif
                                    {{ $menuItem->name }}
                                </a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown{{ $menuItem->id }}" 
                                   role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    @if($menuItem->icon)
                                        <i class="{{ $menuItem->icon }} me-1"></i>
                                    @endif
                                    {{ $menuItem->name }}
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown{{ $menuItem->id }}">
                                    @foreach($menuItem->children as $child)
                                        <li>
                                            <a class="dropdown-item" 
                                               href="{{ $child->url ?? ($child->route_name ? route($child->route_name) : '#') }}">
                                                @if($child->icon)
                                                    <i class="{{ $child->icon }} me-1"></i>
                                                @endif
                                                {{ $child->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
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
                <div class="col-md-6">
                    <h5>{{ $siteTitle }}</h5>
                    <p>Compare electronic products like laptops, PCs, and mobile phones to make informed purchase decisions.</p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        @foreach($footerMenu as $menuItem)
                            <li>
                                <a class="text-white" 
                                   href="{{ $menuItem->url ?? ($menuItem->route_name ? route($menuItem->route_name) : '#') }}">
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
</body>
</html> 