@extends('layouts.admin')

@section('title', 'Inventory Management')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Inventory Management</h5>
            <div>
                <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Add New Item
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-lg-12">
                    <form action="{{ route('admin.inventory.index') }}" method="GET" id="filterForm">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm" name="search" 
                                    value="{{ request('search') }}" placeholder="Search by name or SKU...">
                            </div>
                            <div class="col-md-2">
                                <select class="form-select form-select-sm" name="status">
                                    <option value="">- All Statuses -</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select form-select-sm" name="stock_status">
                                    <option value="">- All Stock Levels -</option>
                                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select form-select-sm" name="category_id">
                                    <option value="">- All Categories -</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex">
                                <button type="submit" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                                <a href="{{ route('admin.inventory.export') }}" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-download me-1"></i>Export
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Inventory Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 60px">ID</th>
                            <th style="width: 80px">Image</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th style="width: 150px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventoryItems as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    @if($item->product && $item->product->primary_image)
                                        <img src="{{ Storage::url($item->product->primary_image->path) }}" alt="{{ $item->product->name }}" 
                                            class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    @elseif($item->product && $item->product->images->count() > 0)
                                        <img src="{{ Storage::url($item->product->images->first()->path) }}" alt="{{ $item->product->name }}" 
                                            class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-image fa-2x"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ $item->product_id ? route('admin.products.edit', ['product' => $item->product_id]) : '#' }}" class="text-decoration-none fw-bold">
                                        {{ $item->product ? $item->product->name : 'N/A' }}
                                    </a>
                                    @if($item->product && $item->product->brand)
                                        <div class="small text-muted">Brand: {{ $item->product->brand }}</div>
                                    @endif
                                </td>
                                <td>{{ $item->sku ?? 'N/A' }}</td>
                                <td>
                                    @if($item->product && $item->product->category)
                                        {{ $item->product->category->name }}
                                    @else
                                        <span class="text-muted">Uncategorized</span>
                                    @endif
                                </td>
                                <td class="{{ $item->quantity <= $item->reorder_level ? 'text-danger fw-bold' : '' }}">
                                    {{ $item->quantity }} units
                                    @if($item->quantity <= $item->reorder_level)
                                        <span class="badge bg-danger">Low Stock</span>
                                    @elseif($item->quantity == 0)
                                        <span class="badge bg-dark">Out of Stock</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($item->status == 'inactive')
                                        <span class="badge bg-warning">Inactive</span>
                                    @elseif($item->status == 'discontinued')
                                        <span class="badge bg-danger">Discontinued</span>
                                    @endif
                                </td>
                                <td>{{ $item->updated_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.inventory.edit', $item->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.inventory.adjust', $item->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-exchange-alt"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-id="{{ $item->id }}" 
                                            data-name="{{ $item->product ? $item->product->name : 'Item' }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <form id="delete-form-{{ $item->id }}" 
                                        action="{{ route('admin.inventory.destroy', $item->id) }}" 
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                        <h5>No inventory items found</h5>
                                        <p class="text-muted">Try clearing filters or add a new inventory item</p>
                                        <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary btn-sm mt-2">
                                            <i class="fas fa-plus me-1"></i>Add New Item
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <span class="text-muted">Showing {{ $inventoryItems->firstItem() ?? 0 }} to {{ $inventoryItems->lastItem() ?? 0 }} of {{ $inventoryItems->total() }} items</span>
                </div>
                <div>
                    {{ $inventoryItems->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3 text-danger">
                            <i class="fas fa-exclamation-triangle fa-3x"></i>
                        </div>
                        <div>
                            <p class="mb-0">Are you sure you want to delete the inventory record for <strong id="itemName"></strong>?</p>
                            <p class="text-danger mb-0"><small>This action cannot be undone.</small></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stock Alerts Card -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Stock Alerts</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-danger bg-opacity-10 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-exclamation-circle fa-3x text-danger"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Out of Stock Items</h6>
                                    <h3 class="mb-0">{{ $outOfStockCount }}</h3>
                                    <a href="{{ route('admin.inventory.index', ['stock_status' => 'out_of_stock']) }}" class="small text-danger">
                                        View all <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-warning bg-opacity-10 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-battery-quarter fa-3x text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Low Stock Items</h6>
                                    <h3 class="mb-0">{{ $lowStockCount }}</h3>
                                    <a href="{{ route('admin.inventory.index', ['stock_status' => 'low_stock']) }}" class="small text-warning">
                                        View all <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-success bg-opacity-10 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-box fa-3x text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Total Inventory Value</h6>
                                    <h3 class="mb-0">${{ number_format($totalValue, 2) }}</h3>
                                    <span class="small text-success">
                                        {{ $totalItems }} items in inventory
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete modal functionality
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            let deleteItemId = null;
            
            deleteModal.addEventListener('show.bs.modal', function (event) {
                // Button that triggered the modal
                const button = event.relatedTarget;
                
                // Extract info from data attributes
                deleteItemId = button.getAttribute('data-id');
                const itemName = button.getAttribute('data-name');
                
                // Update the modal's content
                const nameElement = deleteModal.querySelector('#itemName');
                if (nameElement) {
                    nameElement.textContent = itemName;
                }
            });
            
            // Handle confirm delete button
            const confirmDeleteBtn = document.getElementById('confirmDelete');
            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', function() {
                    if (deleteItemId) {
                        const form = document.getElementById('delete-form-' + deleteItemId);
                        if (form) {
                            form.submit();
                        }
                    }
                });
            }
        }
        
        // Apply select2 to dropdowns if available
        if (typeof $.fn.select2 !== 'undefined') {
            $('select[name="status"]').select2({
                minimumResultsForSearch: 6,
                width: '100%'
            });
            
            $('select[name="stock_status"]').select2({
                minimumResultsForSearch: 6,
                width: '100%'
            });
            
            $('select[name="category_id"]').select2({
                placeholder: 'Select category',
                allowClear: true,
                width: '100%'
            });
        }
    });
</script>
@endpush 