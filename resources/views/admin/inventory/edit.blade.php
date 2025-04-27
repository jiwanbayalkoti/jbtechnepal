@extends('layouts.admin')

@section('title', 'Edit Inventory Item')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Inventory Item</h1>
        <div>
            <a href="{{ route('admin.inventory.adjust', $inventory->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm mr-2">
                <i class="fas fa-boxes fa-sm text-white-50 mr-1"></i> Adjust Stock
            </a>
            <a href="{{ route('admin.inventory.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Back to Inventory
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Product Information Card -->
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Product Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($inventory->product->primary_image)
                            <img src="{{ Storage::url($inventory->product->primary_image->path) }}" 
                                 alt="{{ $inventory->product->name }}" class="img-fluid rounded" style="max-height: 150px;">
                        @elseif($inventory->product->images->count() > 0)
                            <img src="{{ Storage::url($inventory->product->images->first()->path) }}" 
                                 alt="{{ $inventory->product->name }}" class="img-fluid rounded" style="max-height: 150px;">
                        @else
                            <div class="bg-light rounded py-5">
                                <i class="fas fa-image fa-4x text-gray-300"></i>
                                <p class="mt-2 text-gray-500">No image available</p>
                            </div>
                        @endif
                    </div>
                    
                    <h5 class="font-weight-bold text-primary">{{ $inventory->product->name }}</h5>
                    
                    <div class="py-2 border-bottom">
                        <small class="text-muted d-block">Brand:</small>
                        <div>{{ $inventory->product->brand ?: 'N/A' }}</div>
                    </div>
                    
                    <div class="py-2 border-bottom">
                        <small class="text-muted d-block">Model:</small>
                        <div>{{ $inventory->product->model ?: 'N/A' }}</div>
                    </div>
                    
                    <div class="py-2 border-bottom">
                        <small class="text-muted d-block">Category:</small>
                        <div>{{ $inventory->product->category->name }}</div>
                    </div>
                    
                    <div class="py-2 border-bottom">
                        <small class="text-muted d-block">Price:</small>
                        <div>${{ number_format($inventory->product->price, 2) }}</div>
                    </div>
                    
                    <div class="py-2">
                        <small class="text-muted d-block">Current Stock:</small>
                        <h4>
                            <span class="badge {{ $inventory->stock_status_badge }}">
                                {{ $inventory->quantity }} units
                            </span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Edit Form Card -->
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Inventory Details</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.inventory.update', $inventory->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i> To adjust the stock quantity, use the 
                            <a href="{{ route('admin.inventory.adjust', $inventory->id) }}" class="font-weight-bold">Adjust Stock</a> 
                            function instead.
                        </div>
                        
                        <div class="row">
                            <!-- SKU -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sku">SKU <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                           id="sku" name="sku" value="{{ old('sku', $inventory->sku) }}" required>
                                    <small class="form-text text-muted">Unique identifier for this product</small>
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="active" {{ old('status', $inventory->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $inventory->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Reorder Level -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reorder_level">Reorder Level <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('reorder_level') is-invalid @enderror" 
                                           id="reorder_level" name="reorder_level" 
                                           value="{{ old('reorder_level', $inventory->reorder_level) }}" min="0" required>
                                    <small class="form-text text-muted">Stock level at which to reorder</small>
                                    @error('reorder_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Current Quantity (Read Only) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Current Quantity</label>
                                    <input type="text" class="form-control bg-light" 
                                           value="{{ $inventory->quantity }}" readonly>
                                    <small class="form-text text-muted">
                                        Use the <a href="{{ route('admin.inventory.adjust', $inventory->id) }}">Adjust Stock</a> 
                                        function to change this value
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Location -->
                        <div class="form-group">
                            <label for="location">Storage Location</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                   id="location" name="location" value="{{ old('location', $inventory->location) }}" maxlength="100">
                            <small class="form-text text-muted">Where this item is stored (e.g., Warehouse A, Shelf B3)</small>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <hr>
                        
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Update Inventory
                            </button>
                            <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 