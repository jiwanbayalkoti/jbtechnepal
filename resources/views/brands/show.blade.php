@extends('layouts.app')

@section('title', $brand->name)

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('brands.all') }}">Brands</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $brand->name }}</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="row mb-5">
        <div class="col-lg-3 col-md-4 mb-4 mb-md-0">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-center p-4">
                    @if($brand->logo)
                        <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" class="mb-4 img-fluid brand-logo" style="max-height: 120px; width: auto;">
                    @else
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-4" style="height: 120px; width: 120px;">
                            <i class="fas fa-trademark text-primary fa-3x"></i>
                        </div>
                    @endif
                    <h1 class="fs-2 fw-bold mb-3">{{ $brand->name }}</h1>
                    
                    @if($brand->website)
                        <a href="{{ $brand->website }}" target="_blank" class="btn btn-outline-primary btn-sm mb-3">
                            <i class="fas fa-external-link-alt me-1"></i> Visit Website
                        </a>
                    @endif
                    
                    <div class="mt-auto text-muted small">
                        <p class="mb-0">{{ $products->total() }} products</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9 col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <h2 class="fs-4 fw-bold mb-3">About {{ $brand->name }}</h2>
                    <div class="mb-4">
                        @if($brand->description)
                            <p>{{ $brand->description }}</p>
                        @else
                            <p class="text-muted">No description available for this brand.</p>
                        @endif
                    </div>
                    
                    <h3 class="fs-5 fw-bold mb-3 border-bottom pb-2">Popular Products</h3>
                    <div class="row row-cols-1 row-cols-md-3 g-4">
                        @forelse($products->take(3) as $product)
                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm product-card">
                                    <div class="position-relative">
                                        @if($product->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $product->images->where('is_primary', true)->first()->path ?? $product->images->first()->path) }}" 
                                                class="card-img-top" alt="{{ $product->name }}" style="height: 180px; object-fit: contain; padding: 1rem;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                                <i class="fas fa-image text-muted fa-3x"></i>
                                            </div>
                                        @endif
                                        
                                        @if($product->discount_price)
                                            <div class="position-absolute top-0 start-0 bg-danger text-white py-1 px-2 m-2 rounded-pill small">
                                                {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% OFF
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title fs-6">{{ $product->name }}</h5>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <div>
                                                @if($product->discount_price)
                                                    <span class="text-danger fw-bold">${{ number_format($product->discount_price, 2) }}</span>
                                                    <small class="text-muted text-decoration-line-through">${{ number_format($product->price, 2) }}</small>
                                                @else
                                                    <span class="fw-bold">${{ number_format($product->price, 2) }}</span>
                                                @endif
                                            </div>
                                            
                                            <a href="{{ route('product', $product->slug) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted">No products available from this brand.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <h3 class="fs-4 fw-bold mb-4">All {{ $brand->name }} Products</h3>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="text-muted">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</span>
                </div>
                <div>
                    <select class="form-select form-select-sm" id="sortProducts">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                    </select>
                </div>
            </div>
            
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-4">
                @forelse($products as $product)
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm product-card">
                            <div class="position-relative">
                                @if($product->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $product->images->where('is_primary', true)->first()->path ?? $product->images->first()->path) }}" 
                                        class="card-img-top" alt="{{ $product->name }}" style="height: 180px; object-fit: contain; padding: 1rem;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                        <i class="fas fa-image text-muted fa-3x"></i>
                                    </div>
                                @endif
                                
                                @if($product->discount_price)
                                    <div class="position-absolute top-0 start-0 bg-danger text-white py-1 px-2 m-2 rounded-pill small">
                                        {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% OFF
                                    </div>
                                @endif
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fs-6">{{ $product->name }}</h5>
                                
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <div>
                                        @if($product->discount_price)
                                            <span class="text-danger fw-bold">${{ number_format($product->discount_price, 2) }}</span>
                                            <small class="text-muted text-decoration-line-through">${{ number_format($product->price, 2) }}</small>
                                        @else
                                            <span class="fw-bold">${{ number_format($product->price, 2) }}</span>
                                        @endif
                                    </div>
                                    
                                    <a href="{{ route('product', $product->slug) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="py-5">
                            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                            <h3 class="fs-5">No products found</h3>
                            <p class="text-muted">There are no products available for this brand at the moment.</p>
                        </div>
                    </div>
                @endforelse
            </div>
            
            <div class="d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .product-card {
        transition: all 0.3s ease;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .brand-logo {
        max-width: 100%;
        transition: all 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('sortProducts').addEventListener('change', function() {
        let currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('sort', this.value);
        window.location.href = currentUrl.toString();
    });
</script>
@endpush
@endsection 