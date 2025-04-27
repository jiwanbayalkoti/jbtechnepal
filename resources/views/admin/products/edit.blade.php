@extends('layouts.admin')

@section('title', 'Edit Product')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Edit Product</h2>
    <div>
        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-info me-2">
            <i class="fas fa-eye me-1"></i>View Product
        </a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Products
        </a>
    </div>
</div>

<form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name*</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $product->slug) }}">
                        <small class="text-muted">Leave empty to auto-generate from name</small>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category*</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="price" class="form-label">Price*</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price) }}" required>
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="brand" class="form-label">Brand</label>
                                <input type="text" class="form-control @error('brand') is-invalid @enderror" id="brand" name="brand" value="{{ old('brand', $product->brand) }}">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="model" class="form-label">Model</label>
                                <input type="text" class="form-control @error('model') is-invalid @enderror" id="model" name="model" value="{{ old('model', $product->model) }}">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="card mb-4" id="specifications">
                <div class="card-header">
                    <h5 class="mb-0">Product Specifications</h5>
                </div>
                <div class="card-body">
                    <div id="specifications-container">
                        <div class="alert alert-info" id="loading-specs">
                            <i class="fas fa-spinner fa-spin me-2"></i> Loading specifications for this category...
                        </div>
                        
                        <div id="spec-fields" class="d-none">
                            <!-- Specification fields will be loaded dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Product Image</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="image" class="form-label">Upload Image</label>
                        <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    @if($product->image)
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded mb-2" style="max-height: 200px;">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="delete_image" name="delete_image">
                            <label class="form-check-label text-danger" for="delete_image">
                                Delete current image
                            </label>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Product
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#category_id').select2({
            theme: 'bootstrap-5'
        });
        
        // Handle category change
        $('#category_id').on('change', function() {
            loadSpecifications();
        });
        
        // Auto-generate slug from name
        $('#name').on('blur', function() {
            if ($('#slug').val() === '') {
                const name = $(this).val();
                const slug = name.toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                $('#slug').val(slug);
            }
        });
        
        // Initial load of specifications
        loadSpecifications();
        
        function loadSpecifications() {
            const categoryId = $('#category_id').val();
            
            if (!categoryId) {
                $('#loading-specs').addClass('d-none');
                $('#spec-fields').addClass('d-none').html('');
                $('#no-specs').removeClass('d-none');
                return;
            }
            
            $('#loading-specs').removeClass('d-none');
            $('#spec-fields').addClass('d-none');
            $('#no-specs').addClass('d-none');
            
            // Get specification types for selected category
            $.ajax({
                url: "{{ url('admin/specification-types') }}/" + categoryId,
                type: 'GET',
                success: function(data) {
                    if (data.length > 0) {
                        let html = '';
                        
                        // Get existing specifications for this product
                        const existingSpecs = @json($product->specifications->keyBy('specification_type_id'));
                        
                        data.forEach(function(spec) {
                            const existingValue = existingSpecs[spec.id] ? existingSpecs[spec.id].value : '';
                            
                            html += `
                                <div class="mb-3">
                                    <label for="spec_${spec.id}" class="form-label">${spec.name} ${spec.unit ? '(' + spec.unit + ')' : ''}</label>
                                    <input type="text" class="form-control" id="spec_${spec.id}" 
                                        name="specifications[${spec.id}]" value="${existingValue}">
                                </div>
                            `;
                        });
                        
                        $('#spec-fields').html(html).removeClass('d-none');
                    } else {
                        $('#spec-fields').html('<div class="alert alert-warning">No specifications defined for this category.</div>').removeClass('d-none');
                    }
                    
                    $('#loading-specs').addClass('d-none');
                },
                error: function() {
                    $('#loading-specs').addClass('d-none');
                    $('#spec-fields').html('<div class="alert alert-danger">Failed to load specifications. Please try again.</div>').removeClass('d-none');
                }
            });
        }
    });
</script>
@endsection 