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
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
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
    @else
    <!-- Banner is disabled in settings -->
    @endif
    
    @if($bannerEnabled)
    <div class="promotional-banner" style="background-color: {{ $bannerBgColor }}; color: {{ $bannerTextColor }}; display: block !important;">
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
    @else
    <!-- Banner is disabled in settings -->
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
                                            <a class="dropdown-item {{ request()->routeIs($child->route_name) ? 'active' : '' }}" 
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
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i>
                                {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                @if(Auth::user()->is_admin)
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-1"></i>Admin Dashboard
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                @endif
                                
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                                        <i class="fas fa-id-card me-1"></i>My Profile
                                    </a>
                                </li>
                                
                                @if(optional(Auth::user()->watchlists)->isNotEmpty())
                                <li>
                                    <a class="dropdown-item" href="{{ route('watchlists.index') }}">
                                        <i class="fas fa-eye me-1"></i>My Watchlists
                                    </a>
                                </li>
                                @endif
                                
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" 
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                                    </a>
                                </li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light me-2">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-light">
                            <i class="fas fa-user-plus me-1"></i>Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="py-4">
        @if(Session::has('success'))
            <div class="container">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ Session::get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        
        @if(Session::has('error'))
            <div class="container">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ Session::get('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        
        @if($errors->any())
            <div class="container">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        
        <!-- Product Slider Section (if enabled) -->
        @if($sliderEnabled)
        <div class="product-slider-section" style="background-color: {{ $sliderBgColor }};">
            <div class="container">
                <div class="product-slider-header">
                    <h2>{{ $sliderTitle }}</h2>
                    <p>{{ $sliderSubtitle }}</p>
                </div>
                
                <div class="product-slider">
                    <div class="swiper product-swiper">
                        <div class="swiper-wrapper" id="product-slider-items">
                            <!-- Skeleton loaders -->
                            @for($i = 0; $i < 5; $i++)
                            <div class="swiper-slide product-slide skeleton-loader">
                                <div class="product-slide-inner">
                                    <div class="product-slide-image skeleton"></div>
                                    <div class="product-slide-category skeleton"></div>
                                    <div class="product-slide-title skeleton"></div>
                                    <div class="product-slide-regular-price skeleton"></div>
                                    <div class="product-slide-button skeleton"></div>
                                </div>
                            </div>
                            @endfor
                        </div>
                        
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-3">About Us</h5>
                    <p class="small">{{ \App\Models\Setting::get('site_description', 'Our comparison system helps you make informed decisions on electronics purchases.') }}</p>
                    <div class="social-icons mt-3">
                        @if($facebook = \App\Models\Setting::get('facebook_url'))
                            <a href="{{ $facebook }}" target="_blank" class="text-light me-2"><i class="fab fa-facebook-f"></i></a>
                        @endif
                        @if($twitter = \App\Models\Setting::get('twitter_url'))
                            <a href="{{ $twitter }}" target="_blank" class="text-light me-2"><i class="fab fa-twitter"></i></a>
                        @endif
                        @if($instagram = \App\Models\Setting::get('instagram_url'))
                            <a href="{{ $instagram }}" target="_blank" class="text-light me-2"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if($youtube = \App\Models\Setting::get('youtube_url'))
                            <a href="{{ $youtube }}" target="_blank" class="text-light"><i class="fab fa-youtube"></i></a>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('home') }}" class="text-light text-decoration-none">Home</a></li>
                        <li class="mb-2"><a href="{{ route('about') }}" class="text-light text-decoration-none">About Us</a></li>
                        <li class="mb-2"><a href="{{ route('contact') }}" class="text-light text-decoration-none">Contact Us</a></li>
                        <li class="mb-2"><a href="{{ url('/faq') }}" class="text-light text-decoration-none">FAQ</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-3">Categories</h5>
                    <ul class="list-unstyled">
                        @if(isset($footerCategories) && count($footerCategories) > 0)
                            @foreach($footerCategories as $category)
                                <li class="mb-2">
                                    <a href="{{ route('categories.show', $category->slug) }}" class="text-light text-decoration-none">
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li class="mb-2">
                                <a href="{{ route('home') }}" class="text-light text-decoration-none">All Products</a>
                            </li>
                        @endif
                    </ul>
                </div>
                
                <div class="col-md-3">
                    <h5 class="text-uppercase mb-3">Contact Info</h5>
                    <ul class="list-unstyled">
                        @if($address = \App\Models\Setting::get('company_address'))
                            <li class="mb-2 d-flex">
                                <i class="fas fa-map-marker-alt me-2 mt-1"></i>
                                <span>{{ $address }}</span>
                            </li>
                        @endif
                        @if($email = \App\Models\Setting::get('contact_email'))
                            <li class="mb-2 d-flex">
                                <i class="fas fa-envelope me-2 mt-1"></i>
                                <a href="mailto:{{ $email }}" class="text-light text-decoration-none">{{ $email }}</a>
                            </li>
                        @endif
                        @if($phone = \App\Models\Setting::get('contact_phone'))
                            <li class="d-flex">
                                <i class="fas fa-phone-alt me-2 mt-1"></i>
                                <a href="tel:{{ $phone }}" class="text-light text-decoration-none">{{ $phone }}</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <hr class="mt-4 mb-3">
            
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="small mb-0">&copy; {{ date('Y') }} {{ $siteTitle }}. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="small mb-0">
                        <a href="{{ url('/privacy') }}" class="text-light text-decoration-none">Privacy Policy</a> | 
                        <a href="{{ url('/terms') }}" class="text-light text-decoration-none">Terms of Service</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Compare Products Badge -->
    <div id="compareCounter" class="compare-badge">
        @if(session('compare_products') && count(session('compare_products')) > 0)
            <a href="{{ route('compare') }}" class="btn btn-danger rounded-circle position-relative p-3">
                <i class="fas fa-balance-scale fa-lg"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">
                    {{ count(session('compare_products')) }}
                </span>
            </a>
        @endif
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.40.0/dist/apexcharts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    
    <script>
        // Define variables for JavaScript files
        const csrfToken = '{{ csrf_token() }}';
        const addToCompareUrl = '{{ route("add.to.compare") }}';
        const removeFromCompareUrl = '{{ route("remove.from.compare") }}';
        const compareUrl = '{{ route("compare") }}';
    </script>
    
    <!-- App-wide JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/advertisements.js') }}"></script>
    <script src="{{ asset('js/banner-slider.js') }}"></script>
    
    @if($sliderEnabled)
    <script>
        // Product slider variables
        const newestProductsUrl = '{{ route("api.newest-products", ["limit" => $sliderCount]) }}';
        const sliderAutoplay = {{ $sliderAutoplay ? 'true' : 'false' }};
    </script>
    <script src="{{ asset('js/product-slider.js') }}"></script>
    @endif
    
    @stack('scripts')
    @yield('scripts')
    
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