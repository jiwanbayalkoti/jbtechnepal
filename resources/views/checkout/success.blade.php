@extends('layouts.app')

@section('title', 'Order Confirmed')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-2">Order Confirmed</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Order Confirmed</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-5x text-success"></i>
                    </div>
                    <h2 class="mb-3">Thank You for Your Order!</h2>
                    <p class="lead mb-1">Your order has been placed successfully.</p>
                    <p class="mb-4">Order Number: <strong>{{ $order->order_number }}</strong></p>
                    
                    <hr class="my-4">
                    
                    <div class="row text-start my-4">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <h5><i class="fas fa-user me-2"></i>Customer Information</h5>
                            <p class="mb-1">{{ $order->customer->first_name }} {{ $order->customer->last_name }}</p>
                            <p class="mb-1">{{ $order->customer->email }}</p>
                            <p>{{ $order->customer->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-shipping-fast me-2"></i>Shipping Address</h5>
                            <p>{!! nl2br(e($order->shipping_address)) !!}</p>
                        </div>
                    </div>
                    
                    <div class="table-responsive my-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="text-start">{{ $item->product_name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->price, 2) }}</td>
                                        <td>${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="3" class="text-end">Subtotal:</td>
                                    <td>${{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">Shipping:</td>
                                    <td>${{ number_format($order->shipping, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">Tax:</td>
                                    <td>${{ number_format($order->tax, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td class="fw-bold">${{ number_format($order->total, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>A confirmation email has been sent to your email address.
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-basket me-2"></i>Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 