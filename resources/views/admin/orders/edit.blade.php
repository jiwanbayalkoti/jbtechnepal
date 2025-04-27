@extends('layouts.admin')

@section('title', 'Edit Order')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Order #{{ $order->order_number }}</h1>
        <div>
            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye fa-sm"></i> View Details
            </a>
            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-primary btn-sm" target="_blank">
                <i class="fas fa-file-invoice fa-sm"></i> Invoice
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Back to Orders
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Order Details Card -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Order Status</label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="payment_status" class="form-label">Payment Status</label>
                                <select name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror">
                                    <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                    <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                                @error('payment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <input type="text" name="payment_method" id="payment_method" class="form-control @error('payment_method') is-invalid @enderror" value="{{ $order->payment_method }}">
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="shipping" class="form-label">Shipping Cost</label>
                                <input type="number" step="0.01" name="shipping" id="shipping" class="form-control @error('shipping') is-invalid @enderror" value="{{ $order->shipping }}">
                                @error('shipping')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tax" class="form-label">Tax</label>
                                <input type="number" step="0.01" name="tax" id="tax" class="form-control @error('tax') is-invalid @enderror" value="{{ $order->tax }}">
                                @error('tax')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="discount" class="form-label">Discount</label>
                                <input type="number" step="0.01" name="discount" id="discount" class="form-control @error('discount') is-invalid @enderror" value="{{ $order->discount }}">
                                @error('discount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Shipping Address</label>
                            <textarea name="shipping_address" id="shipping_address" rows="4" class="form-control @error('shipping_address') is-invalid @enderror">{{ $order->shipping_address }}</textarea>
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Admin Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ $order->notes ?? '' }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Update Order</button>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Order Summary Card -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="mb-1"><strong>Order Number:</strong> {{ $order->order_number }}</p>
                        <p class="mb-1"><strong>Customer:</strong> {{ $order->customer->full_name }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $order->customer->email }}</p>
                        <p class="mb-1"><strong>Phone:</strong> {{ $order->customer->phone }}</p>
                        <p class="mb-1"><strong>Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="font-weight-bold">Items</h6>
                        <ul class="list-group">
                            @foreach($order->items as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="font-weight-bold">{{ $item->product_name }}</span>
                                    <br>
                                    <small>${{ number_format($item->price, 2) }} x {{ $item->quantity }}</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">${{ number_format($item->subtotal, 2) }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mb-3">
                        <table class="table table-sm">
                            <tr>
                                <td>Subtotal:</td>
                                <td class="text-end">${{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Shipping:</td>
                                <td class="text-end">${{ number_format($order->shipping, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Tax:</td>
                                <td class="text-end">${{ number_format($order->tax, 2) }}</td>
                            </tr>
                            @if($order->discount > 0)
                            <tr>
                                <td>Discount:</td>
                                <td class="text-end">-${{ number_format($order->discount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="table-primary">
                                <th>Total:</th>
                                <th class="text-end">${{ number_format($order->total, 2) }}</th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success btn-sm mb-2" data-toggle="modal" data-target="#markPaidModal">
                            <i class="fas fa-check-circle"></i> Mark as Paid
                        </button>
                        <button type="button" class="btn btn-info btn-sm mb-2" data-toggle="modal" data-target="#shipOrderModal">
                            <i class="fas fa-shipping-fast"></i> Mark as Shipped
                        </button>
                        <button type="button" class="btn btn-warning btn-sm mb-2" data-toggle="modal" data-target="#sendEmailModal">
                            <i class="fas fa-envelope"></i> Send Email to Customer
                        </button>
                        <a href="{{ route('admin.returns.create', ['order_id' => $order->id]) }}" class="btn btn-secondary btn-sm mb-2">
                            <i class="fas fa-exchange-alt"></i> Create Return
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#cancelOrderModal">
                            <i class="fas fa-times-circle"></i> Cancel Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Paid Modal -->
<div class="modal fade" id="markPaidModal" tabindex="-1" aria-labelledby="markPaidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="markPaidModalLabel">Mark Order as Paid</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.orders.update-payment', $order->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p>Are you sure you want to mark order #{{ $order->order_number }} as paid?</p>
                    <div class="mb-3">
                        <label for="payment_reference" class="form-label">Payment Reference</label>
                        <input type="text" class="form-control" id="payment_reference" name="payment_reference">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Mark as Paid</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ship Order Modal -->
<div class="modal fade" id="shipOrderModal" tabindex="-1" aria-labelledby="shipOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shipOrderModalLabel">Mark Order as Shipped</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="shipped">
                <div class="modal-body">
                    <p>Are you sure you want to mark order #{{ $order->order_number }} as shipped?</p>
                    <div class="mb-3">
                        <label for="tracking_number" class="form-label">Tracking Number</label>
                        <input type="text" class="form-control" id="tracking_number" name="tracking_number" value="{{ $order->tracking_number ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label for="shipping_carrier" class="form-label">Shipping Carrier</label>
                        <select class="form-control" id="shipping_carrier" name="shipping_carrier">
                            <option value="usps" {{ ($order->shipping_carrier ?? '') == 'usps' ? 'selected' : '' }}>USPS</option>
                            <option value="ups" {{ ($order->shipping_carrier ?? '') == 'ups' ? 'selected' : '' }}>UPS</option>
                            <option value="fedex" {{ ($order->shipping_carrier ?? '') == 'fedex' ? 'selected' : '' }}>FedEx</option>
                            <option value="dhl" {{ ($order->shipping_carrier ?? '') == 'dhl' ? 'selected' : '' }}>DHL</option>
                            <option value="other" {{ ($order->shipping_carrier ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="notify_customer" name="notify_customer" value="1" checked>
                        <label class="form-check-label" for="notify_customer">
                            Send shipping notification to customer
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Mark as Shipped</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelOrderModalLabel">Cancel Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="cancelled">
                <div class="modal-body">
                    <p class="text-danger">Are you sure you want to cancel order #{{ $order->order_number }}?</p>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Cancellation Reason</label>
                        <select class="form-control" id="cancellation_reason" name="cancellation_reason">
                            <option value="customer_request">Customer Request</option>
                            <option value="out_of_stock">Out of Stock</option>
                            <option value="payment_failed">Payment Failed</option>
                            <option value="fraud_suspected">Fraud Suspected</option>
                            <option value="duplicate_order">Duplicate Order</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cancellation_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="cancellation_notes" name="cancellation_notes" rows="3"></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="restock_items" name="restock_items" value="1" checked>
                        <label class="form-check-label" for="restock_items">
                            Restock items to inventory
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="notify_customer_cancel" name="notify_customer" value="1" checked>
                        <label class="form-check-label" for="notify_customer_cancel">
                            Send cancellation notification to customer
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send Email Modal -->
<div class="modal fade" id="sendEmailModal" tabindex="-1" aria-labelledby="sendEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendEmailModalLabel">Send Email to Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.orders.send-email', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email_subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="email_subject" name="subject" value="Your Order #{{ $order->order_number }} Update">
                    </div>
                    <div class="mb-3">
                        <label for="email_message" class="form-label">Message</label>
                        <textarea class="form-control" id="email_message" name="message" rows="5"></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="include_order_details" name="include_order_details" value="1" checked>
                        <label class="form-check-label" for="include_order_details">
                            Include order details in email
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send Email</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 