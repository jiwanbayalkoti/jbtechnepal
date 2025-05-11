@extends('layouts.app')

@section('title', "$brand $model Products")

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('brands.all') }}">Brands</a></li>
            <li class="breadcrumb-item"><a href="{{ route('brand.show', $brand) }}">{{ ucwords(str_replace('-', ' ', $brand)) }}</a></li>
            <li class="breadcrumb-item active">{{ ucwords(str_replace('-', ' ', $model)) }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="category-header bg-light p-4 rounded">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        @if($brandObj && $brandObj->logo)
                            <img src="{{ asset('storage/' . $brandObj->logo) }}" alt="{{ $brandObj->name }}" class="img-fluid" style="max-height: 80px;">
                        @else
                            <i class="fas fa-building fa-4x text-primary"></i>
                        @endif
                    </div>
                    <div class="col-md-10">
                        <h1 class="h2 mb-2">{{ ucwords(str_replace('-', ' ', $brand)) }} {{ ucwords(str_replace('-', ' ', $model)) }} Products</h1>
                        <p class="mb-0">Browse our selection of {{ ucwords(str_replace('-', ' ', $brand)) }} {{ ucwords(str_replace('-', ' ', $model)) }} products.</p>
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
                    <form action="{{ route('brand.model.show', [$brand, $model]) }}" method="GET" id="filterForm">
                        @if(request()->filled('sort_by'))
                            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                        @endif
                        @if(request()->filled('sort_dir'))
                            <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
                        @endif

                        <!-- Categories Filter -->
                        @if($categories->count() > 0)
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Categories</h6>
                                <div class="list-group">
                                    @foreach($categories as $category)
                                        <label class="list-group-item d-flex">
                                            <input class="form-check-input me-2" type="radio" name="category" 
                                                value="{{ $category->id }}" 
                                                {{ request('category') == $category->id ? 'checked' : '' }}
                                                onchange="document.getElementById('filterForm').submit()">
                                            {{ $category->name }}
                                        </label>
                                    @endforeach
                                    @if(request()->filled('category'))
                                        <a href="{{ route('brand.model.show', [$brand, $model]) }}" 
                                           class="list-group-item list-group-item-action text-primary">
                                            <i class="fas fa-times-circle me-2"></i> Clear Selection
                                        </a>
                                    @endif
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

                        @if(request()->anyFilled(['category', 'price_min', 'price_max']))
                            <div class="d-grid">
                                <a href="{{ route('brand.model.show', [$brand, $model]) }}" class="btn btn-outline-danger">
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
                    @if(request()->anyFilled(['category', 'price_min', 'price_max']))
                        <a href="{{ route('brand.model.show', [$brand, $model]) }}" class="alert-link">Clear all filters</a> to see all products.
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
                                            data-product-id="{{ $product->id }}"
                                            title="Add to Compare">
                                        <i class="fas fa-balance-scale"></i>
                                    </button>
                                </div>
                                
                                <div class="card-body">
                                    <p class="product-category">
                                        <span class="badge bg-primary">{{ $product->category->name }}</span>
                                    </p>
                                    <h5 class="card-title product-title">
                                        <a href="{{ route('product', $product->slug) }}" class="text-decoration-none text-dark">
                                            {{ $product->name }}
                                        </a>
                                    </h5>
                                    <p class="card-text product-brand">{{ $product->brand }} {{ $product->model }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="product-price">${{ number_format($product->price, 2) }}</span>
                                        <div>
                                            <a href="{{ route('product', $product->slug) }}" class="btn btn-sm btn-outline-primary">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateSort(value) {
        const [sortBy, sortDir] = value.split('-');
        const url = new URL(window.location.href);
        url.searchParams.set('sort_by', sortBy);
        url.searchParams.set('sort_dir', sortDir);
        window.location.href = url.toString();
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Add to compare functionality
        document.querySelectorAll('.add-to-compare-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                
                // Send AJAX request
                fetch('{{ route("add.to.compare") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        updateCompareCounter(data.count);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while adding the product to compare.');
                });
            });
        });
    });
</script>
@endpush 