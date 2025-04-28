@extends('layouts.app')

@section('title', 'Home - Electronics Product Comparison System')

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="row">
        <!-- Left Sidebar with Filters -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Find Products</h5>
                </div>
                <div class="card-body">
                    <form id="filterForm" action="{{ route('search') }}" method="GET">
                        <div class="search-container mb-4">
                            <div class="input-group">
                                <input type="text" id="searchInput" class="form-control" name="search" placeholder="Search products..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Price Range -->
                        <div class="mb-3">
                            <label class="form-label">Price Range:</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" class="form-control" id="min_price" name="min_price" placeholder="Min" value="{{ request('min_price') }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control" id="max_price" name="max_price" placeholder="Max" value="{{ request('max_price') }}">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Categories -->
                        <div class="mb-3">
                            <label class="form-label">Categories:</label>
                            <div class="overflow-auto" style="max-height: 150px;">
                                @foreach($featuredCategories as $category)
                                <div class="category-item mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input category-checkbox" type="checkbox" name="category[]" 
                                               value="{{ $category->id }}" id="category_{{ $category->id }}"
                                               {{ in_array($category->id, (array)request('category')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="category_{{ $category->id }}">
                                            {{ $category->name }} ({{ $category->products->count() }})
                                        </label>
                                    </div>
                                    <!-- Subcategories for this category -->
                                    <div class="subcategory-container ms-3 mt-1" id="subcategory_container_{{ $category->id }}" 
                                         style="display: {{ in_array($category->id, (array)request('category')) ? 'block' : 'none' }};">
                                        @foreach($subcategories->where('category_id', $category->id) as $subcategory)
                                        <div class="form-check">
                                            <input class="form-check-input subcategory-checkbox" type="checkbox" name="subcategory[]" 
                                                   value="{{ $subcategory->id }}" id="subcategory_{{ $subcategory->id }}"
                                                   {{ in_array($subcategory->id, (array)request('subcategory')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="subcategory_{{ $subcategory->id }}">
                                                {{ $subcategory->name }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Brands -->
                        <div class="mb-3">
                            <label class="form-label">Brands:</label>
                            <div class="overflow-auto" style="max-height: 150px;">
                                @foreach($brands as $brand)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="brand[]" 
                                           value="{{ $brand->brand }}" id="brand_{{ str_replace(' ', '_', $brand->brand) }}"
                                           {{ in_array($brand->brand, (array)request('brand')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="brand_{{ str_replace(' ', '_', $brand->brand) }}">
                                        {{ $brand->brand }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Sort Order -->
                        <div class="mb-3">
                            <label for="sort" class="form-label">Sort By:</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    </form>
                </div>
            </div>

            <!-- Sidebar Advertisement -->
            <!-- <div class="ad-container" data-ad-position="sidebar"></div> -->
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9">
            <!-- Top Banner Advertisement -->
            <div class="ad-container mb-4" data-ad-position="homepage_top"></div>

            <!-- Latest Products Section -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Latest Products</h5>
                </div>
                <div class="card-body">
                    <div class="row" id="products-container">
                        @foreach($latestProducts as $product)
                            @include('partials.product-item', ['product' => $product])
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <button id="load-more-btn" class="btn btn-outline-primary">Load More Products</button>
                        <p id="no-more-products" class="mt-2 d-none">No more products to load.</p>
                    </div>
                </div>
            </div>

            <!-- Featured Categories Section -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Product Categories</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($featuredCategories->take(4) as $category)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $category->name }}</h5>
                                    <p class="card-text">{{ $category->products->count() }} products available</p>
                                    <a href="{{ route('categories.show', $category->slug) }}" class="btn btn-sm btn-outline-primary">Browse Products</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Middle Banner Advertisement -->
            <div class="ad-container mb-4" data-ad-position="homepage_middle"></div>
            
            <!-- How to Compare Section -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">How to Compare Products</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <div class="p-3 bg-light rounded">
                                <i class="fas fa-search fa-2x text-primary mb-2"></i>
                                <h6>1. Browse Products</h6>
                                <p class="small">Find products you're interested in comparing.</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="p-3 bg-light rounded">
                                <i class="fas fa-plus-circle fa-2x text-primary mb-2"></i>
                                <h6>2. Add to Compare</h6>
                                <p class="small">Select products to add to your comparison list.</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="p-3 bg-light rounded">
                                <i class="fas fa-balance-scale fa-2x text-primary mb-2"></i>
                                <h6>3. View Comparison</h6>
                                <p class="small">See detailed side-by-side comparison of specifications.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Banner Advertisement -->
            <div class="ad-container mt-4" data-ad-position="homepage_bottom"></div>
        </div>
    </div>

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
</div>
@endsection 

@push('styles')
<link href="{{ asset('css/banner-styles.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('js/banner-slider.js') }}"></script>
<script src="{{ asset('js/home.js') }}"></script>
<script>
    // Define URLs that will be used in the home.js file
    const loadMoreProductsUrl = '{{ route("load.more.products") }}';
    const addToCompareUrl = '{{ route("add.to.compare") }}';
</script>
@endpush 