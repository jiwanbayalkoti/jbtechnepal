@extends('layouts.app')

@section('title', 'Search Results')

@section('content')
<div class="row">
    <!-- Filter Sidebar -->
    <div class="col-md-3 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Filters</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('search') }}" method="GET" id="filterForm">
                    <!-- Keep the search query -->
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    
                    <!-- Categories Filter -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Categories</label>
                        <div class="border p-2 rounded overflow-auto" style="max-height: 200px;">
                            @foreach($categories as $category)
                                <div class="form-check">
                                    <input class="form-check-input category-checkbox" type="checkbox" 
                                           name="category[]" id="category{{ $category->id }}" 
                                           value="{{ $category->id }}" 
                                           {{ (is_array(request('category')) && in_array($category->id, request('category'))) 
                                              || request('category') == $category->id ? 'checked' : '' }}>
                                    <label class="form-check-label" for="category{{ $category->id }}">
                                        {{ $category->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Subcategories Filter -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subcategories</label>
                        <div class="border p-2 rounded overflow-auto" style="max-height: 200px;">
                            @foreach($subcategories as $subcategory)
                                <div class="form-check subcategory-item" data-category="{{ $subcategory->category_id }}">
                                    <input class="form-check-input" type="checkbox" 
                                           name="subcategory[]" id="subcategory{{ $subcategory->id }}" 
                                           value="{{ $subcategory->id }}" 
                                           {{ (is_array(request('subcategory')) && in_array($subcategory->id, request('subcategory'))) 
                                              || request('subcategory') == $subcategory->id ? 'checked' : '' }}>
                                    <label class="form-check-label" for="subcategory{{ $subcategory->id }}">
                                        {{ $subcategory->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Brands Filter -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Brands</label>
                        <div class="border p-2 rounded overflow-auto" style="max-height: 200px;">
                            @foreach($brands as $brand)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="brand[]" id="brand{{ str_replace(' ', '_', $brand->brand) }}" 
                                           value="{{ $brand->brand }}" 
                                           {{ (is_array(request('brand')) && in_array($brand->brand, request('brand'))) 
                                              || request('brand') == $brand->brand ? 'checked' : '' }}>
                                    <label class="form-check-label" for="brand{{ str_replace(' ', '_', $brand->brand) }}">
                                        {{ $brand->brand }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Price Range Filter -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Price Range</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="number" class="form-control" name="min_price" placeholder="Min" value="{{ request('min_price') }}" min="0">
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control" name="max_price" placeholder="Max" value="{{ request('max_price') }}" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sort By -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sort By</label>
                        <select class="form-select" name="sort">
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price (Low to High)</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price (High to Low)</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        </select>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="{{ route('search') }}" class="btn btn-outline-secondary">Clear Filters</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Product Results -->
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">
                @if(request('search'))
                    Search Results for "{{ request('search') }}"
                @else
                    All Products
                @endif
            </h2>
            <span class="text-muted">{{ $products->total() }} products found</span>
        </div>
        
        @if($products->count() > 0)
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach($products as $product)
                    <div class="col">
                        <div class="card h-100 product-card">
                            <div class="position-relative">
                                @if($product->primary_image)
                                    <img src="{{ Storage::url($product->primary_image->path) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 180px; object-fit: contain;">
                                @elseif($product->images->isNotEmpty())
                                    <img src="{{ Storage::url($product->images->first()->path) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 180px; object-fit: contain;">
                                @else
                                    <div class="bg-light text-center py-5">
                                        <i class="fas fa-image text-secondary fa-3x"></i>
                                    </div>
                                @endif
                                
                                <button class="btn btn-sm btn-primary position-absolute top-0 end-0 m-2 add-to-compare" 
                                        data-product-id="{{ $product->id }}">
                                    <i class="fas fa-plus me-1"></i>Compare
                                </button>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="card-text">
                                    <span class="badge bg-info">{{ $product->category->name }}</span>
                                    @if($product->subcategory)
                                        <span class="badge bg-secondary">{{ $product->subcategory->name }}</span>
                                    @endif
                                    @if($product->brand)
                                        <span class="badge bg-light text-dark">{{ $product->brand }}</span>
                                    @endif
                                </p>
                                <p class="card-text text-muted small">
                                    {{ Str::limit($product->description, 100) }}
                                </p>
                                <h4 class="text-primary">${{ number_format($product->price, 2) }}</h4>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <a href="{{ route('product', $product->slug) }}" class="btn btn-outline-primary flex-grow-1">
                                    <i class="fas fa-info-circle me-1"></i>Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>No products found matching your criteria. Try adjusting your filters.
            </div>
            
            <div class="text-center my-5">
                <i class="fas fa-search fa-5x text-muted mb-3"></i>
                <h3>No results found</h3>
                <p class="text-muted">Try a different search term or adjust your filters.</p>
                <a href="{{ route('home') }}" class="btn btn-primary mt-3">Back to Home</a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle category and subcategory filtering
        const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
        const subcategoryItems = document.querySelectorAll('.subcategory-item');
        
        function updateSubcategoryVisibility() {
            // Get all selected category IDs
            const selectedCategories = Array.from(document.querySelectorAll('.category-checkbox:checked'))
                .map(checkbox => checkbox.value);
            
            // Show/hide subcategories based on selected categories
            subcategoryItems.forEach(item => {
                const categoryId = item.getAttribute('data-category');
                if (selectedCategories.length === 0 || selectedCategories.includes(categoryId)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                    // Uncheck if hidden
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    if (checkbox.checked) {
                        checkbox.checked = false;
                    }
                }
            });
        }
        
        // Initialize subcategory visibility
        updateSubcategoryVisibility();
        
        // Update subcategory visibility when category selection changes
        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSubcategoryVisibility);
        });
    });
</script>
@endsection 