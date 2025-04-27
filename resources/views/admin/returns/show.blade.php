@extends('layouts.admin')

@section('title', 'Return Request Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Return #{{ $return->return_number }}</h1>
        <div>
            <a href="{{ route('admin.returns.edit', $return) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm me-2">
                <i class="fas fa-edit fa-sm text-white-50 me-1"></i> Process Return
            </a>
            <a href="{{ route('admin.orders.show', $return->order) }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm me-2">
                <i class="fas fa-shopping-cart fa-sm text-white-50 me-1"></i> View Order
            </a>
            <a href="{{ route('admin.returns.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Back to Returns
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Return Information Card -->
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Return Information</h6>
                    <span class="badge {{ $return->status_badge }} text-white">{{ ucfirst($return->status) }}</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Return Details</h6>
                        <hr>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Return Number:</div>
                            <div class="col-7">{{ $return->return_number }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Order Number:</div>
                            <div class="col-7">
                                <a href="{{ route('admin.orders.show', $return->order) }}">
                                    {{ $return->order->order_number }}
                                </a>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Request Date:</div>
                            <div class="col-7">{{ $return->created_at->format('M d, Y h:i A') }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Status:</div>
                            <div class="col-7">
                                <span class="badge {{ $return->status_badge }} text-white">{{ ucfirst($return->status) }}</span>
                            </div>
                        </div>
                        @if($return->processed_at)
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Processed Date:</div>
                            <div class="col-7">{{ $return->processed_at->format('M d, Y') }}</div>
                        </div>
                        @endif
                        @if($return->refund_method)
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Refund Method:</div>
                            <div class="col-7">{{ ucfirst(str_replace('_', ' ', $return->refund_method)) }}</div>
                        </div>
                        @endif
                        @if($return->refund_amount)
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Refund Amount:</div>
                            <div class="col-7">${{ number_format($return->refund_amount, 2) }}</div>
                        </div>
                        @endif
                        @if($return->return_tracking_number)
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Tracking #:</div>
                            <div class="col-7">{{ $return->return_tracking_number }}</div>
                        </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <h6 class="font-weight-bold">Customer Information</h6>
                        <hr>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Name:</div>
                            <div class="col-7">
                                <a href="{{ route('admin.customers.show', $return->customer) }}">
                                    {{ $return->customer->full_name }}
                                </a>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Email:</div>
                            <div class="col-7">
                                <a href="mailto:{{ $return->customer->email }}">{{ $return->customer->email }}</a>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Phone:</div>
                            <div class="col-7">
                                @if($return->customer->phone)
                                    <a href="tel:{{ $return->customer->phone }}">{{ $return->customer->phone }}</a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="font-weight-bold">Return Reason</h6>
                        <hr>
                        <p>{{ $return->reason }}</p>
                    </div>

                    @if($return->admin_notes)
                        <div class="mb-3">
                            <h6 class="font-weight-bold">Admin Notes</h6>
                            <hr>
                            <p>{{ $return->admin_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Return Items Card -->
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Return Items</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Condition</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($return->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                @if($item->orderItem->product && $item->orderItem->product->primary_image)
                                                    <img src="{{ Storage::url($item->orderItem->product->primary_image->path) }}" 
                                                         alt="{{ $item->orderItem->product_name }}" 
                                                         class="img-thumbnail me-2" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @elseif($item->orderItem->product && $item->orderItem->product->images->isNotEmpty())
                                                    <img src="{{ Storage::url($item->orderItem->product->images->first()->path) }}" 
                                                         alt="{{ $item->orderItem->product_name }}" 
                                                         class="img-thumbnail me-2" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="img-thumbnail d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 50px; height: 50px; background-color: #f8f9fa;">
                                                        <i class="fas fa-image text-secondary"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="font-weight-bold">{{ $item->orderItem->product_name }}</div>
                                                    <small class="text-muted">Price: ${{ number_format($item->orderItem->price, 2) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>
                                            <span class="badge {{ $item->condition_badge }} text-white">
                                                {{ ucfirst($item->condition) }}
                                            </span>
                                        </td>
                                        <td>{{ $item->reason }}</td>
                                        <td>
                                            @if($item->approved)
                                                <span class="badge bg-success text-white">Approved</span>
                                            @else
                                                <span class="badge bg-warning text-white">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Refund Details Card -->
            @if($return->status == 'completed' || $return->status == 'processed')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Refund Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Refund Method:</strong> {{ ucfirst(str_replace('_', ' ', $return->refund_method)) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Refund Amount:</strong> ${{ number_format($return->refund_amount, 2) }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Processed Date:</strong> {{ $return->processed_at ? $return->processed_at->format('M d, Y') : 'Not processed yet' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                <span class="badge {{ $return->status_badge }} text-white">{{ ucfirst($return->status) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Admin Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Admin Actions</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.returns.update', $return) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">Update Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="requested" {{ $return->status == 'requested' ? 'selected' : '' }}>Requested</option>
                                        <option value="approved" {{ $return->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="received" {{ $return->status == 'received' ? 'selected' : '' }}>Received</option>
                                        <option value="processed" {{ $return->status == 'processed' ? 'selected' : '' }}>Processed</option>
                                        <option value="completed" {{ $return->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="rejected" {{ $return->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="return_tracking_number">Return Tracking # (Optional)</label>
                                    <input type="text" class="form-control" id="return_tracking_number" name="return_tracking_number" value="{{ $return->return_tracking_number }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="admin_notes">Admin Notes</label>
                                    <textarea class="form-control" id="admin_notes" name="admin_notes" rows="1">{{ $return->admin_notes }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row refund-fields" style="{{ in_array($return->status, ['processed', 'completed']) ? '' : 'display: none;' }}">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="refund_method">Refund Method</label>
                                    <select class="form-select" id="refund_method" name="refund_method">
                                        <option value="credit" {{ $return->refund_method == 'credit' ? 'selected' : '' }}>Store Credit</option>
                                        <option value="original_payment" {{ $return->refund_method == 'original_payment' ? 'selected' : '' }}>Original Payment Method</option>
                                        <option value="exchange" {{ $return->refund_method == 'exchange' ? 'selected' : '' }}>Exchange</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="refund_amount">Refund Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" min="0" class="form-control" id="refund_amount" name="refund_amount" value="{{ $return->refund_amount ?? 0 }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Return
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
