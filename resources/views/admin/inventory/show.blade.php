@extends('layouts.admin')

@section('title', 'Inventory Details - ' . $inventory->product->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Inventory Details</h5>
                    <div>
                        <a href="{{ route('admin.inventory.edit', $inventory->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-list me-1"></i>Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    @if($inventory->product->primary_image)
                                        <img src="{{ Storage::url($inventory->product->primary_image->path) }}" 
                                            alt="{{ $inventory->product->name }}" class="img-thumbnail" 
                                            style="width: 100px; height: 100px; object-fit: cover;">
                                    @elseif($inventory->product->images->isNotEmpty())
                                        <img src="{{ Storage::url($inventory->product->images->first()->path) }}" 
                                            alt="{{ $inventory->product->name }}" class="img-thumbnail"
                                            style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex justify-content-center align-items-center" 
                                            style="width: 100px; height: 100px;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4>{{ $inventory->product->name }}</h4>
                                    <div class="text-muted">
                                        <span class="badge bg-{{ $inventory->status == 'active' ? 'success' : 'secondary' }} mb-1">
                                            {{ ucfirst($inventory->status) }}
                                        </span>
                                        <div>
                                            @if($inventory->product->category)
                                                <i class="fas fa-folder me-1"></i>{{ $inventory->product->category->name }}
                                            @endif
                                            @if($inventory->product->brand)
                                                <span class="mx-2">|</span>
                                                <i class="fas fa-tag me-1"></i>{{ $inventory->product->brand }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Stock Information</h5>
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <div class="text-muted small">Current Stock</div>
                                            <div class="fs-4 fw-bold {{ $inventory->quantity <= 0 ? 'text-danger' : ($inventory->quantity <= $inventory->reorder_level ? 'text-warning' : 'text-success') }}">
                                                {{ $inventory->quantity }}
                                            </div>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <div class="text-muted small">Reorder Level</div>
                                            <div class="fs-4 fw-bold">{{ $inventory->reorder_level }}</div>
                                        </div>
                                        <div class="col-12">
                                            <div class="text-muted small">Stock Status</div>
                                            <div>
                                                @if($inventory->quantity <= 0)
                                                    <span class="text-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Out of Stock
                                                    </span>
                                                @elseif($inventory->quantity <= $inventory->reorder_level)
                                                    <span class="text-warning">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>Low Stock
                                                    </span>
                                                @else
                                                    <span class="text-success">
                                                        <i class="fas fa-check-circle me-1"></i>In Stock
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 mb-4">
                            <h5 class="card-title">Inventory Details</h5>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th style="width: 200px;">SKU</th>
                                        <td>{{ $inventory->sku ?? 'Not assigned' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Storage Location</th>
                                        <td>{{ $inventory->storage_location ?? 'Not specified' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>{{ ucfirst($inventory->status) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Transaction</th>
                                        <td>{{ $lastTransaction ? $lastTransaction->created_at->format('M d, Y H:i') . ' - ' . ucfirst($lastTransaction->type) . ' ' . $lastTransaction->quantity : 'No transactions' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated</th>
                                        <td>{{ $inventory->updated_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ $inventory->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Notes</th>
                                        <td>{{ $inventory->notes ?? 'No notes' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Stock Movement History</h5>
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#quickAdjustModal" 
                                    data-inventory-id="{{ $inventory->id }}" 
                                    data-product-name="{{ $inventory->product->name }}" 
                                    data-current-qty="{{ $inventory->quantity }}">
                                    <i class="fas fa-plus-minus me-1"></i>Adjust Stock
                                </button>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Quantity</th>
                                            <th>Reason</th>
                                            <th>Reference</th>
                                            <th>By User</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $transaction->type == 'add' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($transaction->type) }}
                                                    </span>
                                                </td>
                                                <td class="{{ $transaction->type == 'add' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->type == 'add' ? '+' : '-' }}{{ $transaction->quantity }}
                                                </td>
                                                <td>{{ ucfirst($transaction->reason) }}</td>
                                                <td>{{ $transaction->reference_id ? $transaction->reference_type . ' #' . $transaction->reference_id : 'N/A' }}</td>
                                                <td>{{ $transaction->user->name ?? 'System' }}</td>
                                                <td>{{ $transaction->notes ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-3">No transaction history available</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-3">
                                {{ $transactions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Product Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width: 120px;">ID</th>
                                <td>{{ $inventory->product->id }}</td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td>{{ $inventory->product->name }}</td>
                            </tr>
                            <tr>
                                <th>Price</th>
                                <td>{{ number_format($inventory->product->price, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td>{{ $inventory->product->category->name ?? 'Uncategorized' }}</td>
                            </tr>
                            <tr>
                                <th>Brand</th>
                                <td>{{ $inventory->product->brand ?? 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-{{ $inventory->product->status ? 'success' : 'danger' }}">
                                        {{ $inventory->product->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('admin.products.edit', ['product' => $inventory->product->id]) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit Product
                        </a>
                        <a href="{{ route('admin.products.show', $inventory->product->id) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-eye me-1"></i>View Product Details
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#quickAdjustModal" 
                            data-inventory-id="{{ $inventory->id }}" 
                            data-product-name="{{ $inventory->product->name }}" 
                            data-current-qty="{{ $inventory->quantity }}">
                            <i class="fas fa-plus-minus me-1"></i>Adjust Stock
                        </button>
                        <a href="{{ route('admin.inventory.edit', $inventory->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>Edit Inventory
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" 
                            data-id="{{ $inventory->id }}" 
                            data-name="{{ $inventory->product->name }}">
                            <i class="fas fa-trash me-1"></i>Delete Inventory
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Adjust Modal -->
<div class="modal fade" id="quickAdjustModal" tabindex="-1" aria-labelledby="quickAdjustModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.inventory.quick-adjust') }}" method="POST">
                @csrf
                <input type="hidden" name="inventory_id" id="adjust-inventory-id">
                <div class="modal-header">
                    <h5 class="modal-title" id="quickAdjustModalLabel">Quick Adjust Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <input type="text" class="form-control" id="adjust-product-name" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Quantity</label>
                        <input type="text" class="form-control" id="adjust-current-qty" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="adjustment_type" class="form-label">Adjustment Type <span class="text-danger">*</span></label>
                        <select name="adjustment_type" id="adjustment_type" class="form-select" required>
                            <option value="add">Add to Stock</option>
                            <option value="remove">Remove from Stock</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adjustment_quantity" class="form-label">Quantity to Adjust <span class="text-danger">*</span></label>
                        <input type="number" name="adjustment_quantity" id="adjustment_quantity" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                        <select name="reason" id="reason" class="form-select" required>
                            <option value="purchase">New Purchase</option>
                            <option value="sale">Sale</option>
                            <option value="return">Customer Return</option>
                            <option value="damaged">Damaged/Defective</option>
                            <option value="correction">Inventory Correction</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adjustment_notes" class="form-label">Notes</label>
                        <textarea name="adjustment_notes" id="adjustment_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" action="{{ route('admin.inventory.destroy', $inventory->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete inventory for <span class="fw-bold">{{ $inventory->product->name }}</span>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i> This action cannot be undone and will remove all inventory history.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize select2 for better dropdown experience if available
        if (typeof $.fn.select2 !== 'undefined') {
            $('.form-select').select2({
                dropdownParent: $('#quickAdjustModal'),
                minimumResultsForSearch: 6
            });
        }
        
        // Quick adjust modal
        $('#quickAdjustModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const inventoryId = button.data('inventory-id');
            const productName = button.data('product-name');
            const currentQty = button.data('current-qty');
            
            const modal = $(this);
            modal.find('#adjust-inventory-id').val(inventoryId);
            modal.find('#adjust-product-name').val(productName);
            modal.find('#adjust-current-qty').val(currentQty);
        });
    });
</script>
@endpush 