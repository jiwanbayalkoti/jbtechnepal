@extends('layouts.admin')

@section('title', 'Order Management')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .status-badge {
        cursor: pointer;
    }
    .order-row:hover {
        background-color: rgba(0,0,0,.03);
    }
    .flatpickr-input {
        background-color: #fff !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Order Management</h1>
        <a href="{{ route('admin.orders.create') }}" class="d-none d-sm-inline-block btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Add New Order
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['total'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['pending'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Processing
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['processing'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completed Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['completed'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}" id="filter-form">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4 mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Search order # or customer" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <select class="form-select" id="status" name="status">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-5 mb-3">
                        <div class="row">
                            <div class="col-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Date From" value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Date To" value="{{ request('date_to') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-undo me-1"></i> Reset
                        </a>
                        <a href="{{ route('admin.orders.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" class="btn btn-success">
                            <i class="fas fa-file-excel me-1"></i> Export
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Orders List</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Order Actions:</div>
                    <a class="dropdown-item" href="{{ route('admin.orders.create') }}">
                        <i class="fas fa-plus fa-sm fa-fw me-2 text-gray-400"></i>
                        Add New Order
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.orders.export') }}">
                        <i class="fas fa-file-excel fa-sm fa-fw me-2 text-gray-400"></i>
                        Export All Orders
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(isset($orders) && $orders->count() > 0)
            <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                            @foreach($orders as $order)
                                <tr class="order-row">
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="font-weight-bold text-primary">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.customers.show', $order->customer) }}">
                                            {{ $order->customer->full_name }}
                                        </a>
                                    </td>
                                    <td>${{ number_format($order->total, 2) }}</td>
                                    <td>{{ $order->items->sum('quantity') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <span class="badge {{ $order->status_badge }} status-badge" 
                                                  id="status-{{ $order->id }}" 
                                                  data-bs-toggle="dropdown"
                                                  aria-expanded="false">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                            <ul class="dropdown-menu shadow" aria-labelledby="status-{{ $order->id }}">
                                                <li><h6 class="dropdown-header">Change Status</h6></li>
                                                @foreach(['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled'] as $status)
                                                    @if($status != $order->status)
                                                        <li>
                                                            <button type="button" class="dropdown-item status-change"
                                              data-order-id="{{ $order->id }}" 
                                                                  data-status="{{ $status }}">
                                                                {{ ucfirst($status) }}
                                                            </button>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $order->payment_status_badge }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-info" title="View Order">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-primary" title="Edit Order">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-success" title="View Invoice" target="_blank">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger delete-order" 
                                                   data-order-id="{{ $order->id }}"
                                                   data-order-number="{{ $order->order_number }}"
                                                   title="Delete Order">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                    {{ $orders->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-4x text-gray-300 mb-3"></i>
                    <p class="lead mb-0">No orders found</p>
                    <p class="text-muted">Try adjusting your search criteria or create a new order.</p>
                    <a href="{{ route('admin.orders.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-1"></i> Create New Order
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Order Modal -->
<div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-labelledby="deleteOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteOrderModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete order <span id="delete-order-number" class="fw-bold"></span>?</p>
                <p class="text-danger">This action cannot be undone and will remove all order items.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="delete-order-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Order</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize date pickers
        flatpickr('.datepicker', {
            dateFormat: "Y-m-d",
            allowInput: true
        });
        
        // Delete order confirmation
        const deleteOrderModal = document.getElementById('deleteOrderModal');
        if (deleteOrderModal) {
            const modal = new bootstrap.Modal(deleteOrderModal);
            
            document.querySelectorAll('.delete-order').forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-order-id');
                    const orderNumber = this.getAttribute('data-order-number');
                    
                    document.getElementById('delete-order-number').textContent = orderNumber;
                    document.getElementById('delete-order-form').action = `/admin/orders/${orderId}`;
                    
                    modal.show();
                });
            });
        }
        
        // Status change handling
        document.querySelectorAll('.status-change').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');
                const status = this.getAttribute('data-status');
                
                // Send AJAX request to update status
                fetch(`/admin/orders/${orderId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the badge on the page
                        const statusBadge = document.getElementById(`status-${orderId}`);
                        if (statusBadge) {
                            statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                            
                            // Remove all bg-* classes
                            statusBadge.classList.forEach(className => {
                                if (className.startsWith('bg-')) {
                                    statusBadge.classList.remove(className);
                                }
                            });
                            
                            // Add the appropriate class based on status
                            let badgeClass = '';
                            switch(status) {
                                case 'pending': badgeClass = 'bg-warning'; break;
                                case 'processing': badgeClass = 'bg-info'; break;
                                case 'shipped': badgeClass = 'bg-primary'; break;
                                case 'delivered': badgeClass = 'bg-success'; break;
                                case 'completed': badgeClass = 'bg-success'; break;
                                case 'cancelled': badgeClass = 'bg-danger'; break;
                                default: badgeClass = 'bg-secondary';
                            }
                            
                            statusBadge.classList.add(badgeClass);
                            
                            // Show a small notification
                            const notification = document.createElement('div');
                            notification.classList.add('position-fixed', 'bottom-0', 'end-0', 'p-3');
                            notification.style.zIndex = '5';
                            notification.innerHTML = `
                                <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                                    <div class="d-flex">
                                        <div class="toast-body">
                                            Order status updated successfully!
                                        </div>
                                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                </div>
                            `;
                            
                            document.body.appendChild(notification);
                            const toast = new bootstrap.Toast(notification.querySelector('.toast'));
                            toast.show();
                            
                            setTimeout(() => {
                                notification.remove();
                            }, 3000);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating status:', error);
                });
            });
        });
    });
</script>
@endsection