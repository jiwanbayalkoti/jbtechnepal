@extends('layouts.app')

@section('title', "$brand $model " . $categoryObj->name . ' Products')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('category.all', $categoryObj->slug) }}">{{ $categoryObj->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.by.brand', [$categoryObj->slug, $brand]) }}">{{ $brand }}</a></li>
            <li class="breadcrumb-item active">{{ $model }}</li>
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
                        <h1 class="h2 mb-2">{{ $brand }} {{ $model }} {{ $categoryObj->name }} Products</h1>
                        <p class="mb-0">Browse our selection of {{ $brand }} {{ $model }} {{ $categoryObj->name }} products.</p>
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
                    <form action="{{ route('products.by.brand.model', [$categoryObj->slug, $brand, $model]) }}" method="GET" id="filterForm">
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
                                        <a href="{{ route('products.by.brand.model', [$categoryObj->slug, $brand, $model, 'subcategory' => $subcategory->id]) }}" 
                                           class="list-group-item list-group-item-action {{ request('subcategory') == $subcategory->id ? 'active' : '' }}">
                                           {{ $subcategory->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Models Filter -->
                        @if($models->count() > 0)
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">{{ $brand }} Models</h6>
                                <div class="list-group">
                                    <a href="{{ route('products.by.brand', [$categoryObj->slug, $brand]) }}" 
                                       class="list-group-item list-group-item-action">
                                       All {{ $brand }} Models
                                    </a>
                                    @foreach($models as $modelItem)
                                        <a href="{{ route('products.by.brand.model', [$categoryObj->slug, $brand, $modelItem]) }}" 
                                           class="list-group-item list-group-item-action {{ $model === $modelItem ? 'active' : '' }}">
                                           {{ $modelItem }}
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
                                    <input type="number" class="form-control form-control-sm" name="price_min" 
                                           placeholder="Min" value="{{ request('price_min', $priceRange['min']) }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" name="price_max" 
                                           placeholder="Max" value="{{ request('price_max', $priceRange['max']) }}">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="text-muted">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</span>
                </div>
                <div>
                    <select class="form-select form-select-sm" id="sortProducts">
                        <option value="newest" {{ request('sort_by') == 'created_at' && request('sort_dir') == 'desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="price_low" {{ request('sort_by') == 'price' && request('sort_dir') == 'asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort_by') == 'price' && request('sort_dir') == 'desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name_asc" {{ request('sort_by') == 'name' && request('sort_dir') == 'asc' ? 'selected' : '' }}>Name: A to Z</option>
                        <option value="name_desc" {{ request('sort_by') == 'name' && request('sort_dir') == 'desc' ? 'selected' : '' }}>Name: Z to A</option>
                    </select>
                </div>
            </div>

            @if($products->isEmpty())
                <div class="alert alert-info">
                    No products found matching your criteria. Try adjusting your filters.
                </div>
            @else
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach($products as $product)
                        <div class="col">
                            <div class="card h-100 shadow-sm product-card">
                                <div class="position-relative">
                                    @if($product->images->isNotEmpty())
                                        <img src="{{ asset('storage/' . $product->images->first()->path) }}" 
                                             alt="{{ $product->name }}" 
                                             class="card-img-top product-image">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <button class="btn btn-sm btn-light rounded-circle compare-btn" 
                                                data-product-id="{{ $product->id }}"
                                                title="Add to Compare">
                                            <i class="fas fa-balance-scale"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="{{ route('product', $product->slug) }}" class="text-decoration-none text-dark">
                                            {{ $product->name }}
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted small mb-2">{{ $product->model }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            @if($product->discount_price)
                                                <span class="text-decoration-line-through text-muted">${{ number_format($product->price, 2) }}</span>
                                                <span class="text-danger fw-bold ms-2">${{ number_format($product->discount_price, 2) }}</span>
                                            @else
                                                <span class="fw-bold">${{ number_format($product->price, 2) }}</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('product', $product->slug) }}" class="btn btn-sm btn-outline-primary">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $products->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle sorting
    const sortSelect = document.getElementById('sortProducts');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const form = document.getElementById('filterForm');
            const sortBy = document.createElement('input');
            sortBy.type = 'hidden';
            sortBy.name = 'sort_by';
            
            const sortDir = document.createElement('input');
            sortDir.type = 'hidden';
            sortDir.name = 'sort_dir';
            
            switch(this.value) {
                case 'newest':
                    sortBy.value = 'created_at';
                    sortDir.value = 'desc';
                    break;
                case 'price_low':
                    sortBy.value = 'price';
                    sortDir.value = 'asc';
                    break;
                case 'price_high':
                    sortBy.value = 'price';
                    sortDir.value = 'desc';
                    break;
                case 'name_asc':
                    sortBy.value = 'name';
                    sortDir.value = 'asc';
                    break;
                case 'name_desc':
                    sortBy.value = 'name';
                    sortDir.value = 'desc';
                    break;
            }
            
            form.appendChild(sortBy);
            form.appendChild(sortDir);
            form.submit();
        });
    }

    // Handle compare button
    const compareButtons = document.querySelectorAll('.compare-btn');
    compareButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            // Add your compare functionality here
            alert('Compare functionality will be implemented here');
        });
    });
});
</script>
@endpush
@endsection 