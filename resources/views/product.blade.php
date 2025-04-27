@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ $product->name }}</h1>
    <a href="{{ route('categories.show', $product->category->slug) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to {{ $product->category->name }}
    </a>
</div>

<!-- Top product page advertisement -->
<div class="ad-container mb-4" data-ad-position="product_page_top"></div>

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-body">
                <!-- Main Image Display -->
                <div class="main-image-container text-center mb-3">
                    <div class="product-image-container position-relative">
                        @if($product->primary_image)
                            <img src="{{ Storage::url($product->primary_image->path) }}" class="img-fluid main-product-image" alt="{{ $product->name }}" id="mainImage">
                        @elseif($product->images->isNotEmpty())
                            <img src="{{ Storage::url($product->images->first()->path) }}" class="img-fluid main-product-image" alt="{{ $product->name }}" id="mainImage">
                        @else
                            <div class="py-5">
                                <i class="fas fa-laptop fa-7x text-secondary"></i>
                            </div>
                        @endif
                        <div class="zoom-hint">
                            <i class="fas fa-search me-1"></i>Hover to zoom
                        </div>
                        <div id="zoomResult" class="zoom-result-popup"></div>
                    </div>
                </div>

                <!-- Thumbnail Images -->
                @if($product->images->isNotEmpty())
                    <div class="thumbnail-container d-flex flex-wrap gap-2 justify-content-center">
                        @foreach($product->images as $image)
                            <div class="thumbnail-item" style="width: 80px; height: 80px; cursor: pointer;">
                                <img src="{{ Storage::url($image->path) }}" 
                                     class="img-thumbnail thumbnail-image" 
                                     alt="{{ $product->name }}"
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onclick="changeMainImage(this.src)">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Left side advertisement -->
        <div class="ad-container mt-4" data-ad-position="product_page_left"></div>
    </div>
    
    <div class="col-md-7">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Product Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h2>${{ number_format($product->price, 2) }}</h2>
                </div>
                
                @if($product->brand || $product->model)
                    <div class="mb-3">
                        @if($product->brand)
                            <p class="mb-1"><strong>Brand:</strong> {{ $product->brand }}</p>
                        @endif
                        @if($product->model)
                            <p class="mb-1"><strong>Model:</strong> {{ $product->model }}</p>
                        @endif
                    </div>
                @endif
                
                <div class="mb-4">
                    <h5>Description</h5>
                    <p>{{ $product->description }}</p>
                </div>
                
                <div class="d-flex gap-2 mb-4">
                    @if($product->inventory && $product->in_stock)
                        <form action="{{ route('cart.add') }}" method="POST" class="d-flex gap-2 w-100">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="input-group">
                                <span class="input-group-text">Qty</span>
                                <input type="number" name="quantity" class="form-control" value="1" min="1" max="{{ $product->stock }}">
                            </div>
                            <button type="submit" class="btn btn-success flex-grow-1">
                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                            </button>
                        </form>
                    @else
                        <button type="button" class="btn btn-secondary w-100" disabled>
                            <i class="fas fa-times-circle me-2"></i>Out of Stock
                        </button>
                    @endif
                </div>
                
                <div class="d-grid">
                    <button type="button" class="btn btn-primary add-to-compare" data-product-id="{{ $product->id }}">
                        <i class="fas fa-balance-scale me-2"></i>Add to Compare
                    </button>
                </div>
            </div>
        </div>
        
        @if($product->specifications->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Specifications</h5>
                </div>
                <div class="card-body">
                    <table class="table spec-table">
                        <tbody>
                            @foreach($product->specifications as $spec)
                                <tr>
                                    <th>{{ $spec->specificationType ? $spec->specificationType->name : 'Unknown' }}</th>
                                    <td>
                                        {{ $spec->value }}
                                        @if($spec->specificationType && $spec->specificationType->unit)
                                            {{ $spec->specificationType->unit }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        
        <!-- Right side advertisement -->
        <div class="ad-container mt-4" data-ad-position="product_page_right"></div>
    </div>
</div>

<!-- Middle advertisement before related products -->
<div class="ad-container my-4" data-ad-position="product_page_middle"></div>

@if($relatedProducts->isNotEmpty())
    <div class="mt-5">
        <h3 class="mb-4">Related Products</h3>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @foreach($relatedProducts as $related)
                <div class="col">
                    <div class="card h-100 product-card">
                        @if($related->primary_image)
                            <img src="{{ Storage::url($related->primary_image->path) }}" class="card-img-top p-3" alt="{{ $related->name }}">
                        @elseif($related->images->isNotEmpty())
                            <img src="{{ Storage::url($related->images->first()->path) }}" class="card-img-top p-3" alt="{{ $related->name }}">
                        @else
                            <div class="text-center pt-3">
                                <i class="fas fa-laptop fa-4x text-secondary"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $related->name }}</h5>
                            <p class="card-text text-primary fw-bold">${{ number_format($related->price, 2) }}</p>
                        </div>
                        <div class="card-footer bg-white border-top-0 d-flex gap-2">
                            <a href="{{ route('product', $related->slug) }}" class="btn btn-outline-primary flex-grow-1">
                                Details
                            </a>
                            @if($related->inventory && $related->in_stock)
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $related->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </form>
                            @endif
                            <button type="button" class="btn btn-primary add-to-compare" data-product-id="{{ $related->id }}">
                                <i class="fas fa-balance-scale"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Bottom advertisement before cart items -->
<div class="ad-container my-4" data-ad-position="product_page_bottom"></div>

<!-- Recently Added to Cart -->
@php
    $cart = session()->get('cart', []);
    $recentlyAdded = [];
    
    // Get the last 4 added items
    $i = 0;
    foreach(array_reverse($cart, true) as $id => $details) {
        if($i >= 4) break;
        $recentlyAdded[$id] = $details;
        $i++;
    }
@endphp

@if(count($recentlyAdded) > 0)
    <div class="mt-5">
        <h3 class="mb-4">Recently Added to Cart</h3>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @foreach($recentlyAdded as $id => $details)
                        @php
                            $cartProduct = App\Models\Product::with(['inventory', 'primaryImage', 'images'])->find($id);
                        @endphp
                        @if($cartProduct)
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <img src="{{ $cartProduct->primary_image ? 
                                            Storage::url($cartProduct->primary_image->path) : 
                                            ($cartProduct->images->first() ? 
                                                Storage::url($cartProduct->images->first()->path) : 
                                                asset('images/placeholder.jpg')) }}" 
                                            alt="{{ $cartProduct->name }}" 
                                            class="img-fluid mb-3" style="max-height: 120px; object-fit: contain;">
                                        <h6>{{ $cartProduct->name }}</h6>
                                        <p class="mb-0">Qty: {{ $details['quantity'] }}</p>
                                        <p class="text-primary">${{ number_format($cartProduct->price * $details['quantity'], 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('cart.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-2"></i>View Cart
                    </a>
                    <a href="{{ route('checkout.index') }}" class="btn btn-success ms-2">
                        <i class="fas fa-credit-card me-2"></i>Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<style>
.main-product-image {
    max-height: 400px;
    width: 100%;
    object-fit: contain;
    transition: all 0.3s ease;
    cursor: crosshair;
}

.thumbnail-image {
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.thumbnail-image:hover {
    border-color: #0d6efd;
}

.thumbnail-item.active .thumbnail-image {
    border-color: #0d6efd;
}

/* Popup zoom styles */
.product-image-container {
    position: relative;
    overflow: visible;
}

.zoom-result-popup {
    position: absolute;
    width: 350px;
    height: 350px;
    border: 1px solid #ddd;
    background-repeat: no-repeat;
    background-color: white;
    overflow: hidden;
    box-shadow: 0 5px 25px rgba(0,0,0,0.2);
    border-radius: 5px;
    z-index: 1000;
    display: none;
    pointer-events: none; /* Prevents the popup from interfering with mouse events */
    top: 0;
    right: -370px; /* Position to the right of the image */
}

.zoom-hint {
    position: absolute;
    top: 10px;
    right: 10px;
    color: #6c757d;
    font-size: 0.75rem;
    background-color: rgba(255, 255, 255, 0.8);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    z-index: 100;
    transition: opacity 0.3s ease;
}
</style>

<script>
function changeMainImage(src) {
    const mainImage = document.getElementById('mainImage');
    mainImage.src = src;
    
    // Update active thumbnail
    document.querySelectorAll('.thumbnail-item').forEach(item => {
        item.classList.remove('active');
        if (item.querySelector('img').src === src) {
            item.classList.add('active');
        }
    });
    
    // Reset zoom view
    const zoomResult = document.getElementById('zoomResult');
    zoomResult.style.backgroundImage = '';
    zoomResult.style.display = 'none';
}

// Set initial active thumbnail
document.addEventListener('DOMContentLoaded', function() {
    const mainImage = document.getElementById('mainImage');
    const zoomResult = document.getElementById('zoomResult');
    const zoomHint = document.querySelector('.zoom-hint');
    
    if (mainImage && zoomResult) {
        // Set active thumbnail
        document.querySelectorAll('.thumbnail-item').forEach(item => {
            if (item.querySelector('img').src === mainImage.src) {
                item.classList.add('active');
            }
        });
        
        // Get main image natural dimensions for accurate zooming
        let imgWidth, imgHeight;
        
        // Initialize with default size
        imgWidth = mainImage.naturalWidth || mainImage.width;
        imgHeight = mainImage.naturalHeight || mainImage.height;
        
        // Update when image is fully loaded
        mainImage.onload = function() {
            imgWidth = this.naturalWidth;
            imgHeight = this.naturalHeight;
        };
        
        // Initialize zoom functionality
        const zoom = 3; // Zoom level
        
        // Add event listeners for zoom
        mainImage.addEventListener('mousemove', function(e) {
            // Show zoom result
            zoomResult.style.display = 'block';
            
            // Hide hint when zooming
            if (zoomHint) zoomHint.style.opacity = '0.3';
            
            // Get cursor position relative to the image
            const bounds = mainImage.getBoundingClientRect();
            const x = (e.clientX - bounds.left) / bounds.width;
            const y = (e.clientY - bounds.top) / bounds.height;
            
            // Calculate background position for zoom result
            const bgX = Math.min(Math.max(x * 100, 0), 100);
            const bgY = Math.min(Math.max(y * 100, 0), 100);
            
            // Display the zoomed result (no need to reposition, only update background)
            zoomResult.style.backgroundImage = `url('${mainImage.src}')`;
            zoomResult.style.backgroundSize = `${zoom * 100}%`;
            zoomResult.style.backgroundPosition = `${bgX}% ${bgY}%`;
        });
        
        // Hide zoom when mouse leaves the image
        mainImage.addEventListener('mouseleave', function() {
            zoomResult.style.display = 'none';
            if (zoomHint) zoomHint.style.opacity = '1';
        });
    }
});
</script>
@endsection 