@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('category.all', $product->category->slug) }}">{{ $product->category->name }}</a></li>
            @if($product->subcategory)
                <li class="breadcrumb-item"><a href="{{ route('category.all', $product->category->slug) }}?subcategory={{ $product->subcategory_id }}">{{ $product->subcategory->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-md-5 mb-4">
            <div class="product-image-container position-relative">
                @if($product->images->count() > 0)
                    <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($product->images as $index => $image)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/' . $image->path) }}" class="d-block w-100 product-image" alt="{{ $product->name }}">
                                </div>
                            @endforeach
                        </div>
                        @if($product->images->count() > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        @endif
                    </div>
                    @if($product->images->count() > 1)
                        <div class="product-thumbnails row mt-2">
                            @foreach($product->images as $index => $image)
                                <div class="col-3 mb-2">
                                    <img src="{{ asset('storage/' . $image->path) }}" 
                                         class="img-thumbnail thumbnail-img {{ $index === 0 ? 'active' : '' }}" 
                                         data-bs-target="#productCarousel" 
                                         data-bs-slide-to="{{ $index }}" 
                                         alt="{{ $product->name }}">
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="text-center bg-light p-5">
                        <i class="fas fa-image fa-5x text-muted"></i>
                        <p class="mt-3">No image available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-md-7">
            <h1 class="product-title mb-2">{{ $product->name }}</h1>
            
            <div class="product-meta mb-3">
                <span class="badge bg-primary">{{ $product->category->name }}</span>
                @if($product->subcategory)
                    <span class="badge bg-secondary">{{ $product->subcategory->name }}</span>
                @endif
                <span class="badge bg-info">{{ $product->brand }}</span>
                <span class="badge bg-dark">Model: {{ $product->model }}</span>
            </div>
            
            <div class="product-price mb-4">
                @if($product->discount_price && $product->discount_price < $product->price)
                    <span class="original-price text-muted text-decoration-line-through">${{ number_format($product->price, 2) }}</span>
                    <span class="current-price text-danger fs-3 fw-bold">${{ number_format($product->discount_price, 2) }}</span>
                    <span class="discount-badge ms-2 bg-success text-white p-1 rounded">
                        {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% OFF
                    </span>
                @else
                    <span class="current-price fs-3 fw-bold">${{ number_format($product->price, 2) }}</span>
                @endif
            </div>
            
            <div class="product-actions mb-4">
                <div class="d-flex align-items-center">
                    <button class="btn btn-primary add-to-cart-btn me-2" data-product-id="{{ $product->id }}">
                        <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                    </button>
                    <button class="btn btn-outline-secondary add-to-compare-btn" data-product-id="{{ $product->id }}">
                        <i class="fas fa-balance-scale me-1"></i> Add to Compare
                    </button>
                </div>
            </div>
            
            <div class="product-description mb-4">
                <h5>Description</h5>
                <p>{{ $product->description }}</p>
            </div>
        </div>
    </div>

    <!-- Product Specifications -->
    @if($product->specifications->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Specifications</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                @foreach($product->specifications as $spec)
                                    <tr>
                                        <th style="width: 30%">{{ $spec->specificationType->name }}</th>
                                        <td>{{ $spec->value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="section-title">Related Products</h3>
            <div class="row">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="col-md-3 mb-4">
                        <div class="card product-card h-100">
                            <div class="card-img-top-wrapper">
                                @if($relatedProduct->images->count() > 0)
                                    <img src="{{ asset('storage/' . $relatedProduct->images->first()->path) }}" class="card-img-top" alt="{{ $relatedProduct->name }}">
                                @else
                                    <div class="text-center bg-light p-4">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <h5 class="card-title product-name">{{ $relatedProduct->name }}</h5>
                                <p class="card-text text-muted mb-2">{{ $relatedProduct->brand }} {{ $relatedProduct->model }}</p>
                                <div class="product-price mb-3">
                                    @if($relatedProduct->discount_price && $relatedProduct->discount_price < $relatedProduct->price)
                                        <span class="original-price text-muted text-decoration-line-through">${{ number_format($relatedProduct->price, 2) }}</span>
                                        <span class="current-price text-danger fw-bold">${{ number_format($relatedProduct->discount_price, 2) }}</span>
                                    @else
                                        <span class="current-price fw-bold">${{ number_format($relatedProduct->price, 2) }}</span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('product', $relatedProduct->slug) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                                    <button class="btn btn-sm btn-outline-secondary add-to-compare-btn" data-product-id="{{ $relatedProduct->id }}">
                                        <i class="fas fa-balance-scale"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Same Brand Products -->
    @if($brandProducts->count() > 0)
    <div class="row mt-5 mb-5">
        <div class="col-12">
            <h3 class="section-title">More {{ $product->brand }} Products</h3>
            <div class="row">
                @foreach($brandProducts as $brandProduct)
                    <div class="col-md-3 mb-4">
                        <div class="card product-card h-100">
                            <div class="card-img-top-wrapper">
                                @if($brandProduct->images->count() > 0)
                                    <img src="{{ asset('storage/' . $brandProduct->images->first()->path) }}" class="card-img-top" alt="{{ $brandProduct->name }}">
                                @else
                                    <div class="text-center bg-light p-4">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <h5 class="card-title product-name">{{ $brandProduct->name }}</h5>
                                <p class="card-text text-muted mb-2">{{ $brandProduct->brand }} {{ $brandProduct->model }}</p>
                                <div class="product-price mb-3">
                                    @if($brandProduct->discount_price && $brandProduct->discount_price < $brandProduct->price)
                                        <span class="original-price text-muted text-decoration-line-through">${{ number_format($brandProduct->price, 2) }}</span>
                                        <span class="current-price text-danger fw-bold">${{ number_format($brandProduct->discount_price, 2) }}</span>
                                    @else
                                        <span class="current-price fw-bold">${{ number_format($brandProduct->price, 2) }}</span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('product', $brandProduct->slug) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                                    <button class="btn btn-sm btn-outline-secondary add-to-compare-btn" data-product-id="{{ $brandProduct->id }}">
                                        <i class="fas fa-balance-scale"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Thumbnail click handler
    const thumbnails = document.querySelectorAll('.thumbnail-img');
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            const slideIndex = this.getAttribute('data-bs-slide-to');
            const carousel = document.querySelector('#productCarousel');
            const bsCarousel = new bootstrap.Carousel(carousel);
            bsCarousel.to(parseInt(slideIndex));
            
            // Update active class
            thumbnails.forEach(thumb => thumb.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Add to compare functionality
    const compareButtons = document.querySelectorAll('.add-to-compare-btn');
    compareButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            addToCompare(productId);
        });
    });
    
    // Add to cart functionality
    const cartButtons = document.querySelectorAll('.add-to-cart-btn');
    cartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            addToCart(productId);
        });
    });
    
    // Function to add product to compare list
    function addToCompare(productId) {
        fetch(`/api/compare/add/${productId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert('Product added to compare list');
                
                // Update compare counter if exists
                const compareCounter = document.querySelector('.compare-counter');
                if (compareCounter) {
                    compareCounter.textContent = data.count;
                    compareCounter.classList.remove('d-none');
                }
            } else {
                alert(data.message || 'Failed to add product to compare list');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
    
    // Function to add product to cart
    function addToCart(productId) {
        fetch(`/api/cart/add`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                product_id: productId,
                quantity: 1 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert('Product added to cart');
                
                // Update cart counter if exists
                const cartCounter = document.querySelector('.cart-counter');
                if (cartCounter) {
                    cartCounter.textContent = data.count;
                    cartCounter.classList.remove('d-none');
                }
            } else {
                alert(data.message || 'Failed to add product to cart');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
});
</script>
@endsection

@section('styles')
<style>
.product-image {
    max-height: 400px;
    object-fit: contain;
    background-color: #f8f9fa;
}

.thumbnail-img {
    cursor: pointer;
    height: 80px;
    object-fit: cover;
}

.thumbnail-img.active {
    border: 2px solid #0d6efd;
}

.product-title {
    font-size: 1.8rem;
    font-weight: 600;
}

.product-card {
    transition: transform 0.3s, box-shadow 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.card-img-top-wrapper {
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.card-img-top {
    max-height: 100%;
    object-fit: contain;
}

.product-name {
    font-size: 1rem;
    height: 2.5rem;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.section-title {
    position: relative;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
}

.section-title:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    background-color: #0d6efd;
}
</style>
@endsection 