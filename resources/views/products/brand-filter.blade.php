@extends('layouts.app')

@section('title', ($brand === 'all' ? 'All' : $brand) . ' ' . $categoryObj->name . ' Products')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('category.all', $categoryObj->slug) }}">{{ $categoryObj->name }}</a></li>
            <li class="breadcrumb-item active">{{ $brand === 'all' ? 'All Brands' : $brand }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="category-header bg-light p-4 rounded">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        @if($categoryObj->icon)
                            <i class="{{ $categoryObj->icon }} fa-4x text-primary"></i>
                        @else
                            <i class="fas fa-shopping-bag fa-4x text-primary"></i>
                        @endif
                    </div>
                    <div class="col-md-10">
                        <h1 class="h2 mb-2">{{ $brand === 'all' ? 'All' : $brand }} {{ $categoryObj->name }} Products</h1>
                        <p class="mb-0">Browse our selection of {{ $brand === 'all' ? '' : $brand . ' ' }}{{ $categoryObj->name }} products.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.by.brand', [$category, $brand]) }}" method="GET" id="filterForm">
                        @if(request()->filled('sort_by'))
                            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                        @endif
                        @if(request()->filled('sort_dir'))
                            <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
                        @endif

                        <!-- Subcategories Filter -->
                        @if($subcategories->count() > 0)
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Subcategories</h6>
                                <div class="list-group">
                                    @foreach($subcategories as $subcategory)
                                        <label class="list-group-item d-flex">
                                            <input class="form-check-input me-2" type="radio" name="subcategory" 
                                                value="{{ $subcategory->id }}" 
                                                {{ request('subcategory') == $subcategory->id ? 'checked' : '' }}
                                                onchange="document.getElementById('filterForm').submit()">
                                            {{ $subcategory->name }}
                                        </label>
                                    @endforeach
                                    @if(request()->filled('subcategory'))
                                        <a href="{{ route('products.by.brand', [$category, $brand]) }}" 
                                           class="list-group-item list-group-item-action text-primary">
                                            <i class="fas fa-times-circle me-2"></i> Clear Selection
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <!-- Brands Filter -->
                        @if($brands->count() > 0)
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Brands</h6>
                                <div class="list-group">
                                    <a href="{{ route('products.by.brand', [$category, 'all']) }}" 
                                       class="list-group-item list-group-item-action {{ $brand === 'all' ? 'active' : '' }}">
                                       All Brands
                                    </a>
                                    @foreach($brands as $brandItem)
                                        <a href="{{ route('products.by.brand', [$category, $brandItem]) }}" 
                                           class="list-group-item list-group-item-action {{ $brand === $brandItem ? 'active' : '' }}">
                                           {{ $brandItem }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Price Range Filter -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Price Range</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label for="price_min" class="form-label">Min</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="price_min" name="price_min" 
                                            value="{{ request('price_min', '') }}" 
                                            min="{{ $priceRange['min'] }}" max="{{ $priceRange['max'] }}" step="1">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="price_max" class="form-label">Max</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="price_max" name="price_max" 
                                            value="{{ request('price_max', '') }}" 
                                            min="{{ $priceRange['min'] }}" max="{{ $priceRange['max'] }}" step="1">
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                        Apply Price Filter
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if(request()->anyFilled(['subcategory', 'price_min', 'price_max']))
                            <div class="d-grid">
                                <a href="{{ route('products.by.brand', [$category, $brand]) }}" class="btn btn-outline-danger">
                                    <i class="fas fa-filter me-2"></i> Clear All Filters
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="text-muted">Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</span>
                </div>
                <div class="d-flex align-items-center">
                    <label for="sortOrder" class="me-2 mb-0">Sort by:</label>
                    <select id="sortOrder" class="form-select form-select-sm" style="width: 200px;" onchange="updateSort(this.value)">
                        <option value="created_at-desc" {{ $sortBy == 'created_at' && $sortDir == 'desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="created_at-asc" {{ $sortBy == 'created_at' && $sortDir == 'asc' ? 'selected' : '' }}>Oldest First</option>
                        <option value="price-asc" {{ $sortBy == 'price' && $sortDir == 'asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price-desc" {{ $sortBy == 'price' && $sortDir == 'desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name-asc" {{ $sortBy == 'name' && $sortDir == 'asc' ? 'selected' : '' }}>Name: A-Z</option>
                        <option value="name-desc" {{ $sortBy == 'name' && $sortDir == 'desc' ? 'selected' : '' }}>Name: Z-A</option>
                    </select>
                </div>
            </div>

            @if($products->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No products found matching your criteria.
                    @if(request()->anyFilled(['subcategory', 'price_min', 'price_max']))
                        <a href="{{ route('products.by.brand', [$category, $brand]) }}" class="alert-link">Clear all filters</a> to see all products in this category.
                    @endif
                </div>
            @else
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
                    @foreach($products as $product)
                        <div class="col">
                            <div class="card h-100 product-card shadow-sm">
                                <div class="position-relative">
                                    @if($product->images->isNotEmpty())
                                        <a href="{{ route('product', $product->slug) }}">
                                            <img src="{{ asset('storage/' . $product->images->first()->path) }}" 
                                                class="card-img-top product-img" 
                                                alt="{{ $product->name }}">
                                        </a>
                                    @else
                                        <a href="{{ route('product', $product->slug) }}">
                                            <div class="card-img-top product-img-placeholder d-flex align-items-center justify-content-center bg-light">
                                                <i class="fas fa-image fa-3x text-muted"></i>
                                            </div>
                                        </a>
                                    @endif
                                    
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary rounded-circle add-to-compare-btn"
                                            onclick="addToCompare({{ $product->id }}, '{{ $product->name }}')">
                                        <i class="fas fa-balance-scale"></i>
                                    </button>
                                </div>
                                
                                <div class="card-body">
                                    <p class="product-category">
                                        <span class="badge bg-primary">{{ $product->category->name }}</span>
                                        @if($product->subcategory)
                                            <span class="badge bg-secondary">{{ $product->subcategory->name }}</span>
                                        @endif
                                    </p>
                                    <h5 class="card-title product-title">
                                        <a href="{{ route('product', $product->slug) }}" class="text-decoration-none text-dark">
                                            {{ $product->name }}
                                        </a>
                                    </h5>
                                    <p class="product-brand mb-1">
                                        <strong>Brand:</strong> {{ $product->brand }}
                                    </p>
                                    <p class="product-model mb-2">
                                        <strong>Model:</strong> {{ $product->model }}
                                    </p>
                                    <div class="product-price mb-3">
                                        <span class="fs-5 fw-bold text-primary">${{ number_format($product->price, 2) }}</span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('product', $product->slug) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-info-circle me-1"></i> Details
                                        </a>
                                        <button class="btn btn-success" onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})">
                                            <i class="fas fa-cart-plus me-1"></i> Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .product-card {
        transition: all 0.3s ease;
        border: none;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .product-img {
        height: 200px;
        object-fit: contain;
        padding: 1rem;
    }
    .product-img-placeholder {
        height: 200px;
    }
    .product-title {
        height: 48px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .add-to-compare-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: white;
        opacity: 0.8;
    }
    .add-to-compare-btn:hover {
        opacity: 1;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
    function updateSort(sortValue) {
        const [sortBy, sortDir] = sortValue.split('-');
        const currentUrl = new URL(window.location.href);
        
        currentUrl.searchParams.set('sort_by', sortBy);
        currentUrl.searchParams.set('sort_dir', sortDir);
        
        window.location.href = currentUrl.toString();
    }
    
    function addToCompare(productId, productName) {
        fetch('/api/compare/add/' + productId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                toastr.success(productName + ' added to comparison');
                
                // Update compare button in navbar if it exists
                const compareCount = document.getElementById('compare-count');
                if (compareCount) {
                    compareCount.textContent = data.count;
                    compareCount.classList.remove('d-none');
                }
            } else {
                toastr.warning(data.message);
            }
        })
        .catch(error => {
            console.error('Error adding product to compare:', error);
            toastr.error('Failed to add product to comparison');
        });
    }
    
    function addToCart(productId, productName, productPrice) {
        fetch('/api/cart/add', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: productId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                toastr.success(productName + ' added to cart');
                
                // Update cart count in navbar if it exists
                const cartCount = document.getElementById('cart-count');
                if (cartCount) {
                    cartCount.textContent = data.count;
                    cartCount.classList.remove('d-none');
                }
            } else {
                toastr.warning(data.message);
            }
        })
        .catch(error => {
            console.error('Error adding product to cart:', error);
            toastr.error('Failed to add product to cart');
        });
    }
</script>
@endpush 