@extends('layouts.admin')

@section('title', 'Adjust Inventory')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Adjust Inventory for {{ $inventory->product->name }}</h5>
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Inventory
            </a>
        </div>
        
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Current Inventory Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex mb-3">
                                @if($inventory->product->primary_image)
                                    <img src="{{ Storage::url($inventory->product->primary_image->path) }}" 
                                        alt="{{ $inventory->product->name }}" 
                                        class="img-thumbnail me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                @elseif($inventory->product->images->count() > 0)
                                    <img src="{{ Storage::url($inventory->product->images->first()->path) }}" 
                                        alt="{{ $inventory->product->name }}" 
                                        class="img-thumbnail me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex justify-content-center align-items-center me-3" 
                                        style="width: 80px; height: 80px;">
                                        <i class="fas fa-image fa-2x text-muted"></i>
                                    </div>
                                @endif
                                
                                <div>
                                    <h5>{{ $inventory->product->name }}</h5>
                                    <div class="text-muted">SKU: {{ $inventory->sku ?? 'N/A' }}</div>
                                    <div class="text-muted">Category: {{ $inventory->product->category->name ?? 'Uncategorized' }}</div>
                                </div>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <div class="text-muted mb-1">Current Quantity</div>
                                        <div class="d-flex align-items-center">
                                            <span class="h3 mb-0 me-2">{{ $inventory->quantity }}</span>
                                            <span class="badge {{ $inventory->quantity <= $inventory->reorder_level ? 'bg-danger' : 'bg-success' }}">
                                                {{ $inventory->quantity <= $inventory->reorder_level ? 'Low Stock' : 'In Stock' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <div class="text-muted mb-1">Reorder Level</div>
                                        <span class="h3 mb-0">{{ $inventory->reorder_level }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Recent Adjustments</h6>
                        </div>
                        <div class="card-body p-0">
                            @if($recentAdjustments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Quantity</th>
                                                <th>Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentAdjustments as $adjustment)
                                                <tr>
                                                    <td>{{ $adjustment->created_at->format('M d, Y H:i') }}</td>
                                                    <td>
                                                        <span class="badge {{ $adjustment->type == 'add' ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $adjustment->type == 'add' ? 'Added' : 'Removed' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ abs($adjustment->quantity) }}</td>
                                                    <td>{{ ucfirst($adjustment->reason) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center p-4">
                                    <i class="fas fa-history fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">No recent adjustments found</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Adjust Inventory</h6>
                        </div>
                        <div class="card-body">
                            <!-- Debug information to help troubleshoot -->
                            <div class="alert alert-info mb-3">
                                <p><strong>Route Information:</strong></p>
                                <p>Expected HTTP Method: POST</p>
                                <p>Route URL: {{ route('admin.inventory.update-stock', $inventory->id) }}</p>
                            </div>

                            <!-- New form with direct absolute URL and explicit POST method -->
                            <form action="{{ url('/admin/inventory/' . $inventory->id . '/update-stock') }}" method="POST">
                                @csrf
                                <!-- No method spoofing directives at all -->
                                
                                <div class="mb-3">
                                    <label for="adjustment_type" class="form-label">Adjustment Type <span class="text-danger">*</span></label>
                                    <select name="adjustment_type" id="adjustment_type" class="form-select @error('adjustment_type') is-invalid @enderror" required>
                                        <option value="add">Add to Stock</option>
                                        <option value="remove">Remove from Stock</option>
                                        <option value="set">Set Exact Quantity</option>
                                    </select>
                                    @error('adjustment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" min="1" value="1" required>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                                    <select name="reason" id="reason" class="form-select @error('reason') is-invalid @enderror" required>
                                        <option value="purchase">New Purchase</option>
                                        <option value="sale">Sale</option>
                                        <option value="return">Customer Return</option>
                                        <option value="damaged">Damaged/Defective</option>
                                        <option value="correction">Inventory Correction</option>
                                        <option value="other">Other</option>
                                    </select>
                                    @error('reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div id="otherReasonGroup" class="mb-3" style="display: none;">
                                    <label for="other_reason" class="form-label">Specify Reason <span class="text-danger">*</span></label>
                                    <input type="text" name="other_reason" id="other_reason" class="form-control @error('other_reason') is-invalid @enderror">
                                    @error('other_reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="reference" class="form-label">Reference Number</label>
                                    <input type="text" name="reference" id="reference" class="form-control @error('reference') is-invalid @enderror" placeholder="Order #, Invoice #, etc.">
                                    @error('reference')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Additional details about this adjustment"></textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Save Adjustment</button>
                                </div>
                            </form>
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
        // Handle "Other" reason selection
        const reasonSelect = document.getElementById('reason');
        const otherReasonGroup = document.getElementById('otherReasonGroup');
        const otherReasonInput = document.getElementById('other_reason');
        
        reasonSelect.addEventListener('change', function() {
            if (this.value === 'other') {
                otherReasonGroup.style.display = 'block';
                otherReasonInput.setAttribute('required', 'required');
            } else {
                otherReasonGroup.style.display = 'none';
                otherReasonInput.removeAttribute('required');
            }
        });
        
        // Initialize select2 if available
        if (typeof $.fn.select2 !== 'undefined') {
            $('#adjustment_type, #reason').select2({
                minimumResultsForSearch: 6,
                width: '100%'
            });
        }
    });
</script>
@endpush 