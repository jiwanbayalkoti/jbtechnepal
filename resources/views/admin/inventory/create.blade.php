@extends('layouts.admin')

@section('title', 'Add Inventory Item')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add Inventory Item</h1>
        <a href="{{ route('admin.inventory.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Back to Inventory
        </a>
    </div>

    <!-- Create Inventory Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">New Inventory Item</h6>
        </div>
        <div class="card-body">
            @if($products->isEmpty())
                <div class="alert alert-info">
                    All products already have inventory records. 
                    <a href="{{ route('admin.products.create') }}" class="alert-link">Create a new product</a> to add inventory.
                </div>
            @else
                <form action="{{ route('admin.inventory.store') }}" method="POST">
                    @csrf
                    
                    <!-- Product Selection -->
                    <div class="form-group">
                        <label for="product_id">Product <span class="text-danger">*</span></label>
                        <select class="form-control @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->brand }} {{ $product->model }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <!-- SKU -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sku">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                       id="sku" name="sku" value="{{ old('sku') }}" required>
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
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Quantity -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantity">Initial Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                       id="quantity" name="quantity" value="{{ old('quantity', 0) }}" min="0" required>
                                <small class="form-text text-muted">Initial stock quantity</small>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Reorder Level -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reorder_level">Reorder Level <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('reorder_level') is-invalid @enderror" 
                                       id="reorder_level" name="reorder_level" value="{{ old('reorder_level', 10) }}" min="0" required>
                                <small class="form-text text-muted">Stock level at which to reorder</small>
                                @error('reorder_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Location -->
                    <div class="form-group">
                        <label for="location">Storage Location</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                               id="location" name="location" value="{{ old('location') }}" maxlength="100">
                        <small class="form-text text-muted">Where this item is stored (e.g., Warehouse A, Shelf B3)</small>
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr>
                    
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Create Inventory Item
                        </button>
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection 