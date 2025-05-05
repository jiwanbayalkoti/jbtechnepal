@extends('layouts.app')

@section('title', 'Compare Products')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Compare Products</h1>

    @if(count($products) > 0)
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered comparison-table">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Product</th>
                                @foreach($products as $product)
                                    <th style="width: {{ 85 / count($products) }}%;" class="text-center">
                                        <div class="position-relative">
                                            <button type="button" class="btn-close position-absolute top-0 end-0 m-1 remove-product" 
                                                    data-product-id="{{ $product->id }}" 
                                                    aria-label="Remove"></button>
                                            @if($product->images->count() > 0)
                                                <img src="{{ asset('storage/' . $product->images->first()->path) }}" 
                                                     alt="{{ $product->name }}" 
                                                     class="img-fluid mb-2" 
                                                     style="max-height: 120px;">
                                            @else
                                                <div class="bg-light text-center p-3 mb-2">
                                                    <i class="fas fa-image fa-3x text-muted"></i>
                                                </div>
                                            @endif
                                            <h5><a href="{{ route('product', $product->slug) }}" class="text-decoration-none">{{ $product->name }}</a></h5>
                                            <p class="text-muted">{{ $product->brand }} {{ $product->model }}</p>
                                            <div class="mb-2">
                                                @if($product->discount_price && $product->discount_price < $product->price)
                                                    <span class="text-muted text-decoration-line-through">${{ number_format($product->price, 2) }}</span>
                                                    <span class="text-danger fw-bold">${{ number_format($product->discount_price, 2) }}</span>
                                                @else
                                                    <span class="fw-bold">${{ number_format($product->price, 2) }}</span>
                                                @endif
                                            </div>
                                            <button class="btn btn-primary btn-sm add-to-cart-btn" data-product-id="{{ $product->id }}">
                                                <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                                            </button>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Basic details -->
                            <tr class="table-light">
                                <th colspan="{{ count($products) + 1 }}">Basic Details</th>
                            </tr>
                            <tr>
                                <td>Category</td>
                                @foreach($products as $product)
                                    <td>{{ $product->category->name }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td>Subcategory</td>
                                @foreach($products as $product)
                                    <td>{{ $product->subcategory->name ?? 'N/A' }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td>Brand</td>
                                @foreach($products as $product)
                                    <td>{{ $product->brand }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td>Model</td>
                                @foreach($products as $product)
                                    <td>{{ $product->model }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td>Price</td>
                                @foreach($products as $product)
                                    <td>
                                        @if($product->discount_price && $product->discount_price < $product->price)
                                            <span class="text-muted text-decoration-line-through">${{ number_format($product->price, 2) }}</span>
                                            <span class="text-danger fw-bold">${{ number_format($product->discount_price, 2) }}</span>
                                        @else
                                            <span class="fw-bold">${{ number_format($product->price, 2) }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Specifications -->
                            @if($specTypes && $specTypes->isNotEmpty())
                                <tr class="table-light">
                                    <th colspan="{{ count($products) + 1 }}">Specifications</th>
                                </tr>
                                @foreach($specTypes as $specType)
                                    <tr>
                                        <td>{{ $specType->name }}</td>
                                        @foreach($products as $product)
                                            <td>
                                                @php
                                                    $spec = null;
                                                    if ($product->specifications) {
                                                        $spec = $product->specifications->first(function($s) use ($specType) {
                                                            return $s->specification_type_id === $specType->id;
                                                        });
                                                    }
                                                @endphp
                                                {{ $spec ? $spec->value : 'N/A' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between mb-5">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Continue Shopping
            </a>
            <button id="clear-compare" class="btn btn-danger">
                <i class="fas fa-trash me-1"></i> Clear All
            </button>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-balance-scale fa-5x text-muted mb-3"></i>
            <h3>No products to compare</h3>
            <p class="text-muted mb-4">Add products to compare them side by side</p>
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="fas fa-shopping-bag me-1"></i> Browse Products
            </a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Remove product from comparison
    const removeButtons = document.querySelectorAll('.remove-product');
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            removeFromCompare(productId);
        });
    });
    
    // Clear all products from comparison
    const clearButton = document.getElementById('clear-compare');
    if (clearButton) {
        clearButton.addEventListener('click', function() {
            clearCompare();
        });
    }
    
    // Add to cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            addToCart(productId);
        });
    });
    
    // Function to remove product from comparison
    function removeFromCompare(productId) {
        fetch(`/api/compare/remove/${productId}`, {
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
                // Refresh the page to show updated comparison
                window.location.reload();
            } else {
                alert(data.message || 'Failed to remove product from comparison');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
    
    // Function to clear all products from comparison
    function clearCompare() {
        fetch('/api/compare/clear', {
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
                // Refresh the page to show updated comparison
                window.location.reload();
            } else {
                alert(data.message || 'Failed to clear comparison');
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
.comparison-table th,
.comparison-table td {
    vertical-align: middle;
}

.comparison-table .table-light th {
    font-size: 1.1rem;
    background-color: #f8f9fa;
}

.comparison-table img {
    max-width: 100%;
    height: auto;
    object-fit: contain;
}

.remove-product {
    z-index: 5;
}

@media (max-width: 767.98px) {
    .comparison-table {
        min-width: 650px;
    }
}
</style>
@endsection 