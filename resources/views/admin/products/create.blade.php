@extends('layouts.admin')

@section('title', 'Add Product')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Add New Product</h5>
    </div>
    <div class="card-body">
        <form id="createProductForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="subcategory_id" class="form-label">Subcategory</label>
                        <select class="form-select @error('subcategory_id') is-invalid @enderror" id="subcategory_id" name="subcategory_id">
                            <option value="">Select Subcategory</option>
                            <!-- Subcategories will be loaded dynamically -->
                        </select>
                        @error('subcategory_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', 0) }}" step="0.01" min="0" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="images" class="form-label">Product Images</label>
                                <input type="file" class="form-control @error('images.*') is-invalid @enderror" id="images" name="images[]" multiple accept="image/*">
                                <small class="form-text text-muted">You can select multiple images. First image will be the primary image.</small>
                                @error('images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="brand" class="form-label">Brand</label>
                                <select class="form-select @error('brand') is-invalid @enderror" id="brand" name="brand">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->name }}" {{ old('brand') == $brand->name ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="model" class="form-label">Model <span class="text-danger">*</span></label>
                                <select class="form-select @error('model') is-invalid @enderror" id="model" name="model" required>
                                    <option value="">Select Model</option>
                                    @foreach(\App\Models\Model::orderBy('name')->get() as $modelOption)
                                        <option value="{{ $modelOption->name }}" {{ old('model') == $modelOption->name ? 'selected' : '' }}>
                                            {{ $modelOption->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Specifications</h6>
                        </div>
                        <div class="card-body" id="specifications-container">
                            <div class="alert alert-info mb-3">
                                First select a category to load specification types.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Save Product
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Form submission handling
        $('#createProductForm').on('submit', function(e) {
            console.log('Form submitting...');
            
            // Check for validation errors
            let hasErrors = false;
            const requiredFields = ['name', 'category_id', 'model', 'price', 'description'];
            
            requiredFields.forEach(field => {
                const $field = $(`#${field}`);
                if (!$field.val()) {
                    console.error(`Field ${field} is empty!`);
                    hasErrors = true;
                    
                    // Highlight the field
                    $field.addClass('is-invalid');
                } else {
                    console.log(`Field ${field} value: ${$field.val()}`);
                }
            });
            
            if (hasErrors) {
                console.error('Form has validation errors!');
                alert('Please fill in all required fields marked with *');
                e.preventDefault();
                return false;
            }
            
            // Log form data
            console.log('Form is valid, submitting...');
            
            return true; // Allow form submission to continue
        });
    
        $('#category_id').change(function() {
            const categoryId = $(this).val();
            const subcategorySelect = $('#subcategory_id');
            
            // Clear subcategory dropdown
            subcategorySelect.empty().append('<option value="">Select Subcategory</option>');
            
            // Clear model dropdown when category changes
            $('#model').empty().append('<option value="">Select Model</option>');
            
            if (categoryId) {
                // Load subcategories for this category
                $.ajax({
                    url: `/admin/subcategories/${categoryId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        if (response.success && response.subcategories.length > 0) {
                            // Add subcategories to dropdown
                            $.each(response.subcategories, function(index, subcategory) {
                                subcategorySelect.append(`<option value="${subcategory.id}">${subcategory.name}</option>`);
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading subcategories:', xhr.responseText);
                    }
                });
                
                // Load specification types for this category
                $.ajax({
                    url: `/admin/specification-types/${categoryId}`,
                    type: 'GET',
                    success: function(data) {
                        let html = '';
                        
                        if (data.length === 0) {
                            html = `<div class="alert alert-warning">
                                No specification types defined for this category.
                                <a href="{{ route('admin.specifications.create', '') }}/${categoryId}" target="_blank">
                                    Add specification types
                                </a>
                            </div>`;
                        } else {
                            html += '<div class="mb-3"><p class="text-muted">Enter values for the specifications below:</p></div>';
                            
                            data.forEach(function(spec) {
                                html += `
                                <div class="mb-3">
                                    <label class="form-label">${spec.name}${spec.unit ? ' (' + spec.unit + ')' : ''}</label>
                                    <input type="text" class="form-control" name="specifications[${spec.id}]" placeholder="Enter value">
                                </div>
                                `;
                            });
                        }
                        
                        $('#specifications-container').html(html);
                    }
                });
            } else {
                $('#specifications-container').html('<div class="alert alert-info mb-3">First select a category to load specification types.</div>');
            }
        });
        
        // Load models when subcategory is selected
        $('#subcategory_id').change(function() {
            const subcategoryId = $(this).val();
            const modelSelect = $('#model');
            
            // Clear model dropdown
            modelSelect.empty().append('<option value="">Select Model</option>');
            
            if (subcategoryId) {
                // Load models for this subcategory
                $.ajax({
                    url: `/admin/models-by-subcategory/${subcategoryId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        if (response.success && response.models.length > 0) {
                            // Add models to dropdown
                            $.each(response.models, function(index, model) {
                                modelSelect.append(`<option value="${model.name}">${model.name}</option>`);
                            });
                        } else {
                            modelSelect.append('<option value="" disabled>No models available for this subcategory</option>');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading models:', xhr.responseText);
                        modelSelect.append('<option value="" disabled>Error loading models</option>');
                    }
                });
            }
        });
    });
</script>
@endsection 