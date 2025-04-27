@extends('layouts.admin')

@section('title', 'Customer Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Customer Details</h1>
        <div>
            <a href="{{ route('admin.customers.edit', $customer) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50 mr-1"></i> Edit Customer
            </a>
            <a href="{{ route('admin.customers.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm ml-2">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Back to Customers
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Customer Information Card -->
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Information</h6>
                    <span class="badge {{ $customer->status_badge }} text-white">{{ ucfirst($customer->status) }}</span>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="img-profile rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; font-size: 40px;">
                            {{ substr($customer->first_name, 0, 1) }}{{ substr($customer->last_name, 0, 1) }}
                        </div>
                        <h4 class="mt-3">{{ $customer->full_name }}</h4>
                        <p class="text-muted">
                            Customer since {{ $customer->created_at->format('M d, Y') }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <h6 class="font-weight-bold">Contact Information</h6>
                        <hr>
                        <div class="row mb-2">
                            <div class="col-4 font-weight-bold">Email:</div>
                            <div class="col-8">
                                <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 font-weight-bold">Phone:</div>
                            <div class="col-8">
                                @if($customer->phone)
                                    <a href="tel:{{ $customer->phone }}">{{ $customer->phone }}</a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="font-weight-bold">Address</h6>
                        <hr>
                        @if($customer->address || $customer->city || $customer->state || $customer->postal_code || $customer->country)
                            <address>
                                {{ $customer->address }}<br>
                                @if($customer->city || $customer->state || $customer->postal_code)
                                    {{ $customer->city }}{{ $customer->city && ($customer->state || $customer->postal_code) ? ', ' : '' }}
                                    {{ $customer->state }} {{ $customer->postal_code }}<br>
                                @endif
                                {{ $customer->country }}
                            </address>
                        @else
                            <p class="text-muted">No address information provided</p>
                        @endif
                    </div>

                    @if($customer->notes)
                        <div class="mb-3">
                            <h6 class="font-weight-bold">Notes</h6>
                            <hr>
                            <p>{{ $customer->notes }}</p>
                        </div>
                    @endif

                    <div class="mt-4">
                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash mr-1"></i> Delete Customer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Card -->
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order History</h6>
                </div>
                <div class="card-body">
                    @if($customer->orders && $customer->orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->orders as $order)
                                        <tr>
                                            <td>{{ $order->order_number }}</td>
                                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                                            <td>{{ $order->items_count }}</td>
                                            <td>${{ number_format($order->total, 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $order->status_badge }} text-white">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-info btn-sm">
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
                            <i class="fas fa-shopping-cart fa-4x text-gray-300 mb-3"></i>
                            <p class="mb-0">This customer has not placed any orders yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Log Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Activity Log</h6>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-4x text-gray-300 mb-3"></i>
                        <p class="mb-0">No activity has been recorded for this customer yet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Add any JavaScript specific to this page here
    });
</script>
@endsection 