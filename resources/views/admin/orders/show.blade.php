@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Order #{{ $order->order_number }}</h1>
        <div>
            <a href="{{ route('admin.orders.edit', $order) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm me-2">
                <i class="fas fa-edit fa-sm text-white-50 me-1"></i> Edit Order
            </a>
            <a href="{{ route('admin.orders.invoice', $order) }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm me-2" target="_blank">
                <i class="fas fa-file-invoice fa-sm text-white-50 me-1"></i> Invoice
            </a>
            <a href="{{ route('admin.returns.create', ['order_id' => $order->id]) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm me-2">
                <i class="fas fa-undo-alt fa-sm text-white-50 me-1"></i> Create Return
            </a>
            <a href="{{ route('admin.orders.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Back to Orders
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Order Information Card -->
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Order Information</h6>
                    <span class="badge {{ $order->status_badge }} text-white">{{ ucfirst($order->status) }}</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Order Details</h6>
                        <hr>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Order Number:</div>
                            <div class="col-7">{{ $order->order_number }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Date:</div>
                            <div class="col-7">{{ $order->created_at->format('M d, Y h:i A') }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Status:</div>
                            <div class="col-7">
                                <span class="badge {{ $order->status_badge }} text-white">{{ ucfirst($order->status) }}</span>
                            </div>
                        </div>
                        @if($order->shipped_at)
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Shipped Date:</div>
                            <div class="col-7">{{ $order->shipped_at->format('M d, Y') }}</div>
                        </div>
                        @endif
                        @if($order->delivered_at)
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Delivered Date:</div>
                            <div class="col-7">{{ $order->delivered_at->format('M d, Y') }}</div>
                        </div>
                        @endif
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Payment Method:</div>
                            <div class="col-7">{{ ucfirst($order->payment_method) }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Payment Status:</div>
                            <div class="col-7">{{ ucfirst($order->payment_status) }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Shipping Method:</div>
                            <div class="col-7">{{ $order->shipping_method ?? 'N/A' }}</div>
                        </div>
                        @if($order->tracking_number)
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Tracking #:</div>
                            <div class="col-7">{{ $order->tracking_number }}</div>
                        </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <h6 class="font-weight-bold">Customer Information</h6>
                        <hr>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Name:</div>
                            <div class="col-7">
                                <a href="{{ route('admin.customers.show', $order->customer) }}">
                                    {{ $order->customer->full_name }}
                                </a>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Email:</div>
                            <div class="col-7">
                                <a href="mailto:{{ $order->customer->email }}">{{ $order->customer->email }}</a>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Phone:</div>
                            <div class="col-7">
                                @if($order->customer->phone)
                                    <a href="tel:{{ $order->customer->phone }}">{{ $order->customer->phone }}</a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="font-weight-bold">Shipping Address</h6>
                        <hr>
                        <address>
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}<br>
                            {{ $order->shipping_country }}
                        </address>
                    </div>

                    @if($order->notes)
                        <div class="mb-3">
                            <h6 class="font-weight-bold">Order Notes</h6>
                            <hr>
                            <p>{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Items Card -->
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Items</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Unit Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                @if($item->product && $item->product->primary_image)
                                                    <img src="{{ Storage::url($item->product->primary_image->path) }}" 
                                                         alt="{{ $item->product_name }}" 
                                                         class="img-thumbnail me-2" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @elseif($item->product && $item->product->images->isNotEmpty())
                                                    <img src="{{ Storage::url($item->product->images->first()->path) }}" 
                                                         alt="{{ $item->product_name }}" 
                                                         class="img-thumbnail me-2" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="img-thumbnail d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 50px; height: 50px; background-color: #f8f9fa;">
                                                        <i class="fas fa-image text-secondary"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="font-weight-bold">{{ $item->product_name }}</div>
                                                    @if(!empty($item->options))
                                                        <small class="text-muted">
                                                            @foreach($item->options as $key => $value)
                                                                {{ ucfirst($key) }}: {{ is_array($value) ? implode(', ', $value) : $value }}<br>
                                                            @endforeach
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>${{ number_format($item->price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                                    <td>${{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                @if($order->tax > 0)
                                <tr>
                                    <td colspan="3" class="text-end">Tax:</td>
                                    <td>${{ number_format($order->tax, 2) }}</td>
                                </tr>
                                @endif
                                @if($order->shipping > 0)
                                <tr>
                                    <td colspan="3" class="text-end">Shipping:</td>
                                    <td>${{ number_format($order->shipping, 2) }}</td>
                                </tr>
                                @endif
                                @if($order->discount > 0)
                                <tr>
                                    <td colspan="3" class="text-end">Discount:</td>
                                    <td>-${{ number_format($order->discount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td class="fw-bold">${{ number_format($order->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Return Requests Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Return Requests</h6>
                    <a href="{{ route('admin.returns.create', ['order_id' => $order->id]) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-plus me-1"></i> Create Return
                    </a>
                </div>
                <div class="card-body">
                    @if($order->returns && $order->returns->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Return #</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Status</th>
                                        <th>Refund Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->returns as $return)
                                        <tr>
                                            <td>{{ $return->return_number }}</td>
                                            <td>{{ $return->created_at->format('M d, Y') }}</td>
                                            <td>{{ $return->items->sum('quantity') }}</td>
                                            <td>
                                                <span class="badge {{ $return->status_badge }} text-white">
                                                    {{ ucfirst($return->status) }}
                                                </span>
                                            </td>
                                            <td>${{ number_format($return->refund_amount ?? 0, 2) }}</td>
                                            <td>
                                                <a href="{{ route('admin.returns.show', $return) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-undo-alt fa-4x text-gray-300 mb-3"></i>
                            <p class="mb-0">No return requests have been created for this order.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection