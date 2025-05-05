@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container">
    <h1 class="mb-4">Shopping Cart</h1>
    
    @if(isset($products) && count($products) > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Cart Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="100">Product</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th width="120">Quantity</th>
                                        <th>Total</th>
                                        <th width="60">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $id => $item)
                                        <tr>
                                            <td>
                                                <img src="{{ $item['product']->primary_image ? 
                                                    Storage::url($item['product']->primary_image->path) : 
                                                    ($item['product']->images->first() ? 
                                                        Storage::url($item['product']->images->first()->path) : 
                                                        asset('images/placeholder.jpg')) }}" 
                                                    alt="{{ $item['product']->name }}" 
                                                    class="img-thumbnail" width="80">
                                            </td>
                                            <td>
                                                <a href="{{ route('product', $item['product']->slug) }}" class="text-decoration-none">
                                                    {{ $item['product']->name }}
                                                </a>
                                                <div class="small text-muted">
                                                    @if($item['product']->inventory)
                                                        <span class="badge bg-{{ $item['product']->in_stock ? 'success' : 'danger' }}">
                                                            {{ $item['product']->in_stock ? 'In Stock' : 'Out of Stock' }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>${{ number_format($item['product']->price, 2) }}</td>
                                            <td>
                                                <form action="{{ route('cart.update') }}" method="POST" class="d-flex">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $id }}">
                                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" 
                                                           min="1" max="{{ $item['product']->stock }}" 
                                                           class="form-control form-control-sm me-2">
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td>${{ number_format($item['subtotal'], 2) }}</td>
                                            <td>
                                                <form action="{{ route('cart.remove') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $id }}">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping:</span>
                            <span>$10.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tax (5%):</span>
                            <span>${{ number_format($total * 0.05, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3 fw-bold">
                            <span>Total:</span>
                            <span>${{ number_format($total + 10.00 + ($total * 0.05), 2) }}</span>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="btn btn-success w-100">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                        </a>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-body">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-shopping-basket me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                <h3>Your cart is empty</h3>
                <p class="mb-4">Looks like you haven't added any products to your cart yet.</p>
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-basket me-2"></i>Start Shopping
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Add any cart-specific JavaScript here
    });
</script>
@endsection 