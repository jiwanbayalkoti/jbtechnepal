@php
    $siteTitle = \App\Models\Setting::get('site_title', config('app.name', 'Product Compare'));
    $siteLogo = \App\Models\Setting::get('site_logo');
    $favicon = \App\Models\Setting::get('favicon');
    $primaryColor = \App\Models\Setting::get('primary_color', '#0d6efd');
    
    // Promotional banner settings - for preview in admin panel
    $bannerEnabled = (bool)\App\Models\Setting::get('promotional_banner_enabled', false);
    $bannerText = \App\Models\Setting::get('promotional_banner_text', '');
    $bannerBgColor = \App\Models\Setting::get('promotional_banner_bg_color', '#ffc107');
    $bannerTextColor = \App\Models\Setting::get('promotional_banner_text_color', '#000000');
    $bannerLink = \App\Models\Setting::get('promotional_banner_link');
    $isSettingsPage = request()->routeIs('admin.settings.*');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - {{ $siteTitle }} - @yield('title')</title>
    
    @if($favicon)
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $favicon) }}">
    @endif
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.40.0/dist/apexcharts.css">
    
    <!-- Custom Admin Styles -->
    @if(request()->routeIs('admin.settings.*'))
    <link rel="stylesheet" href="{{ asset('css/admin-settings.css') }}">
    @endif
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    <style>
        :root {
            --bs-primary: {{ $primaryColor }};
            --bs-primary-rgb: {{ implode(', ', sscanf($primaryColor, "#%02x%02x%02x")) }};
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

        .sidebar {
            min-height: calc(100vh - 56px);
        }
        .admin-content {
            padding-top: 20px;
            padding-bottom: 40px;
        }
        .chart-container {
            height: 350px;
            width: 100%;
        }
        
        /* Submenu styles */
        .list-group-item.p-0 > a {
            color: #212529;
            display: block;
            transition: all 0.3s ease;
        }
        .list-group-item.active.p-0 > a {
            color: #fff;
        }
        .list-group-item.p-0 .collapse {
            transition: all 0.3s ease;
        }
        .list-group-item.p-0 .collapse .list-group-item {
            background-color: #f8f9fa;
            padding-left: 2.5rem;
            transition: all 0.2s ease;
        }
        .list-group-item.p-0 .collapse .list-group-item.active {
            background-color: #0d6efd;
            color: #fff;
        }
        .list-group-item.p-0 .collapse .list-group-item:hover {
            background-color: #e9ecef;
        }
        .list-group-item.p-0 .collapse .list-group-item.active:hover {
            background-color: #0d6efd;
        }
        .list-group-item.p-0 .fa-chevron-down {
            transition: transform 0.3s ease;
        }
        
        /* Admin Pagination Styles */
        .pagination {
            justify-content: center;
        }
        .page-item.active .page-link {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .page-link {
            color: #4e73df;
            padding: 0.5rem 0.85rem;
            margin: 0 3px;
            border-radius: 5px;
            transition: all 0.2s ease-in-out;
        }
        .page-link:hover {
            background-color: #eaecf4;
            color: #224abe;
            z-index: 2;
        }
        .page-item.disabled .page-link {
            color: #858796;
        }
        
        /* Responsive adjustments for smaller screens */
        @media (max-width: 768px) {
            .pagination {
                flex-wrap: wrap;
            }
            .page-link {
                padding: 0.4rem 0.7rem;
                margin: 0 2px;
                font-size: 0.9rem;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    @if($bannerEnabled && $isSettingsPage)
    <div class="promotional-banner" style="background-color: {{ $bannerBgColor }}; color: {{ $bannerTextColor }}">
        <div class="container">
            @if($bannerLink)
                <a href="{{ $bannerLink }}">{{ $bannerText }}</a>
            @else
                {{ $bannerText }}
            @endif
            <div class="badge bg-dark ms-2">Preview</div>
        </div>
    </div>
    @endif

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-user-cog me-2"></i>{{ $siteTitle }} Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}" target="_blank">
                            <i class="fas fa-home me-1"></i>View Site
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 bg-light sidebar py-3">
                <div class="list-group">
                    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a href="{{ route('admin.customers.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                        <i class="fas fa-users me-2"></i>Customers
                    </a>
                    <!-- Categories dropdown with submenu -->
                    <div class="list-group-item list-group-item-action p-0 {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.subcategories.*') ? 'active' : '' }}">
                        <a href="#categoriesSubmenu" data-bs-toggle="collapse" 
                           class="d-flex justify-content-between align-items-center text-decoration-none px-3 py-2 {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.subcategories.*') ? 'text-white' : '' }}">
                            <span><i class="fas fa-tags me-2"></i>Categories</span>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="collapse {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.subcategories.*') ? 'show' : '' }}" id="categoriesSubmenu">
                            <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <i class="fas fa-folder me-2"></i>Manage Categories
                            </a>
                            <a href="{{ route('admin.subcategories.index') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}">
                                <i class="fas fa-sitemap me-2"></i>Manage Subcategories
                            </a>
                        </div>
                    </div>
                    
                    <!-- Products dropdown with submenu -->
                    <div class="list-group-item list-group-item-action p-0 {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.imports.*') || request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                        <a href="#productsSubmenu" data-bs-toggle="collapse" 
                           class="d-flex justify-content-between align-items-center text-decoration-none px-3 py-2 {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.imports.*') || request()->routeIs('admin.brands.*') ? 'text-white' : '' }}">
                            <span><i class="fas fa-shopping-cart me-2"></i>Products</span>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="collapse {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.imports.*') || request()->routeIs('admin.brands.*') ? 'show' : '' }}" id="productsSubmenu">
                            <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                <i class="fas fa-laptop me-2"></i>Manage Products
                            </a>
                            <a href="{{ route('admin.brands.index') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                                <i class="fas fa-trademark me-2"></i>Manage Brands
                            </a>
                            <a href="{{ route('admin.imports.index') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.imports.*') ? 'active' : '' }}">
                                <i class="fas fa-cloud-download-alt me-2"></i>API Imports
                            </a>
                        </div>
                    </div>
                    
                    <!-- Inventory Management -->
                    <div class="list-group-item list-group-item-action p-0 {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                        <a href="#inventorySubmenu" data-bs-toggle="collapse" 
                           class="d-flex justify-content-between align-items-center text-decoration-none px-3 py-2 {{ request()->routeIs('admin.inventory.*') ? 'text-white' : '' }}">
                            <span><i class="fas fa-boxes me-2"></i>Inventory</span>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="collapse {{ request()->routeIs('admin.inventory.*') ? 'show' : '' }}" id="inventorySubmenu">
                            <a href="{{ route('admin.inventory.index') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.inventory.index') ? 'active' : '' }}">
                                <i class="fas fa-list me-2"></i>Manage Inventory
                            </a>
                            <a href="{{ route('admin.inventory.create') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.inventory.create') ? 'active' : '' }}">
                                <i class="fas fa-plus-circle me-2"></i>Add Inventory
                            </a>
                            <a href="{{ route('admin.inventory.index', ['stock_status' => 'low_stock']) }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.inventory.index') && request()->stock_status == 'low_stock' ? 'active' : '' }}">
                                <i class="fas fa-exclamation-triangle me-2"></i>Low Stock
                                @php
                                    $lowStockCount = \App\Models\Inventory::whereRaw('quantity <= reorder_level')
                                        ->where('quantity', '>', 0)
                                        ->count();
                                @endphp
                                @if($lowStockCount > 0)
                                    <span class="badge bg-warning float-end">{{ $lowStockCount }}</span>
                                @endif
                            </a>
                        </div>
                    </div>

                    <!-- Order & Return Management -->
                    <div class="list-group-item list-group-item-action p-0 {{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.returns.*') ? 'active' : '' }}">
                        <a href="#orderSubmenu" data-bs-toggle="collapse" 
                        class="d-flex justify-content-between align-items-center text-decoration-none px-3 py-2 {{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.returns.*') ? 'text-white' : '' }}">
                            <span><i class="fas fa-shopping-bag me-2"></i>Order Management</span>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="collapse {{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.returns.*') ? 'show' : '' }}" id="orderSubmenu">
                            <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.orders.*') && !request()->routeIs('admin.orders.create') ? 'active' : '' }}">
                                <i class="fas fa-list-alt me-2"></i>All Orders
                                @php
                                    $pendingOrderCount = \App\Models\Order::where('status', 'pending')->count();
                                @endphp
                                @if($pendingOrderCount > 0)
                                    <span class="badge bg-warning float-end">{{ $pendingOrderCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.orders.create') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.orders.create') ? 'active' : '' }}">
                                <i class="fas fa-plus-circle me-2"></i>Create Order
                            </a>
                            <a href="{{ route('admin.returns.index') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.returns.*') && !request()->routeIs('admin.returns.create') ? 'active' : '' }}">
                                <i class="fas fa-undo-alt me-2"></i>Returns
                                @php
                                    $pendingReturnCount = \App\Models\ReturnRequest::where('status', 'requested')->count();
                                @endphp
                                @if($pendingReturnCount > 0)
                                    <span class="badge bg-danger float-end">{{ $pendingReturnCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.returns.create') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.returns.create') ? 'active' : '' }}">
                                <i class="fas fa-plus-circle me-2"></i>Create Return
                            </a>
                        </div>
                    </div>
                    
                    <a href="{{ route('admin.contact.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}">
                        <i class="fas fa-envelope me-2"></i>Contact Messages
                        @php
                            $pendingCount = \App\Models\ContactMessage::where('status', 'pending')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="badge bg-danger float-end">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    
                    <!-- Advertisements Management -->
                    <div class="list-group-item list-group-item-action p-0 {{ request()->routeIs('admin.advertisements.*') ? 'active' : '' }}">
                        <a href="#advertisementSubmenu" data-bs-toggle="collapse" 
                           class="d-flex justify-content-between align-items-center text-decoration-none px-3 py-2 {{ request()->routeIs('admin.advertisements.*') ? 'text-white' : '' }}">
                            <span><i class="fas fa-ad me-2"></i>Advertisements</span>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="collapse {{ request()->routeIs('admin.advertisements.*') ? 'show' : '' }}" id="advertisementSubmenu">
                            <a href="{{ route('admin.advertisements.index') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.advertisements.index') ? 'active' : '' }}">
                                <i class="fas fa-list me-2"></i>All Advertisements
                            </a>
                            <a href="{{ route('admin.advertisements.create') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.advertisements.create') ? 'active' : '' }}">
                                <i class="fas fa-plus-circle me-2"></i>Create Advertisement
                            </a>
                            <a href="{{ route('admin.advertisements.statistics') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.advertisements.statistics') ? 'active' : '' }}">
                                <i class="fas fa-chart-line me-2"></i>Performance Stats
                            </a>
                            <a href="{{ route('admin.advertisements.test') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.advertisements.test') ? 'active' : '' }}">
                                <i class="fas fa-vial me-2"></i>Test Advertisements
                            </a>
                        </div>
                    </div>
                    
                    <!-- User Management - Only visible to admins -->
                    @if(Auth::user()->isAdmin())
                    <div class="list-group-item list-group-item-action p-0 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <a href="#usersSubmenu" data-bs-toggle="collapse" 
                           class="d-flex justify-content-between align-items-center text-decoration-none px-3 py-2 {{ request()->routeIs('admin.users.*') ? 'text-white' : '' }}">
                            <span><i class="fas fa-user-shield me-2"></i>User Management</span>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="collapse {{ request()->routeIs('admin.users.*') ? 'show' : '' }}" id="usersSubmenu">
                            <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                                <i class="fas fa-users me-2"></i>All Users
                            </a>
                            <a href="{{ route('admin.users.create') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                                <i class="fas fa-user-plus me-2"></i>Add New User
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Settings dropdown with submenu -->
                    <div class="list-group-item list-group-item-action p-0 {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.menus.*') || request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                        <a href="#settingsSubmenu" data-bs-toggle="collapse" 
                           class="d-flex justify-content-between align-items-center text-decoration-none px-3 py-2 {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.menus.*') || request()->routeIs('admin.pages.*') ? 'text-white' : '' }}">
                            <span><i class="fas fa-cogs me-2"></i>Settings</span>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="collapse {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.menus.*') || request()->routeIs('admin.pages.*') ? 'show' : '' }}" id="settingsSubmenu">
                            <a href="{{ route('admin.settings.index') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                <i class="fas fa-sliders-h me-2"></i>General Settings
                            </a>
                            <a href="{{ route('admin.menus.index') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                                <i class="fas fa-bars me-2"></i>Menu Management
                            </a>
                            <a href="{{ route('admin.pages.index') }}" class="list-group-item list-group-item-action border-0 ps-5 py-2 {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                                <i class="fas fa-file-alt me-2"></i>Pages Management
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9 col-lg-10 admin-content">
                <div class="container-fluid">
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

                    <h1 class="mb-4">@yield('title')</h1>
                    
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.40.0/dist/apexcharts.min.js"></script>
    <script>
        // Make sure jQuery and Bootstrap are properly loaded
        console.log('jQuery version:', typeof $ !== 'undefined' ? $.fn.jquery : 'not loaded');
        console.log('Bootstrap version:', typeof bootstrap !== 'undefined' ? bootstrap.Modal.VERSION : 'not loaded');
        
        // Global AJAX setup for CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script src="{{ asset('js/admin-modals.js') }}"></script>
    
    <script>
        // Submenu toggle handling
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize submenu state based on active state
            const activeSubmenus = document.querySelectorAll('.list-group-item.p-0.active .collapse');
            activeSubmenus.forEach(submenu => {
                const bsCollapse = new bootstrap.Collapse(submenu, {
                    toggle: true
                });
            });
            
            // Toggle chevron icon orientation when submenu is toggled
            const submenus = document.querySelectorAll('.list-group-item.p-0 a[data-bs-toggle="collapse"]');
            submenus.forEach(submenuToggle => {
                const targetId = submenuToggle.getAttribute('href');
                const target = document.querySelector(targetId);
                
                if (target) {
                    // Icon is down by default, rotate it if submenu is expanded
                    const icon = submenuToggle.querySelector('.fa-chevron-down');
                    if (icon && target.classList.contains('show')) {
                        icon.style.transform = 'rotate(180deg)';
                    }
                    
                    // Listen for Bootstrap's collapse events
                    target.addEventListener('show.bs.collapse', function () {
                        if (icon) icon.style.transform = 'rotate(180deg)';
                    });
                    
                    target.addEventListener('hide.bs.collapse', function () {
                        if (icon) icon.style.transform = 'rotate(0deg)';
                    });
                }
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html> 