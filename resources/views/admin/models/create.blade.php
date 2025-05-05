@extends('layouts.admin')

@section('title', 'Add New Model')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h1>Add New Model</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('admin.models.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Models
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Model Details</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.models.store') }}" method="POST" id="modelForm">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="name" class="form-label">Model Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <!-- Brand Dropdown (First Level) -->
                <div class="col-md-4">
                    <label for="brand_id" class="form-label">Brand <span class="text-danger">*</span></label>
                    <select name="brand_id" id="brand_id" class="form-select @error('brand_id') is-invalid @enderror" required>
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('brand_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Category Dropdown (Second Level - Depends on Brand) -->
                <div class="col-md-4">
                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                    <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        @if(old('brand_id') && $categories->count() > 0)
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Subcategory Dropdown (Third Level - Depends on Category) -->
                <div class="col-md-4">
                    <label for="subcategory_id" class="form-label">Subcategory <span class="text-danger">*</span></label>
                    <select name="subcategory_id" id="subcategory_id" class="form-select @error('subcategory_id') is-invalid @enderror" required disabled>
                        <option value="">Select Subcategory</option>
                        @if(old('category_id') && $subcategories->count() > 0)
                            @foreach($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}" {{ old('subcategory_id') == $subcategory->id ? 'selected' : '' }}>
                                    {{ $subcategory->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('subcategory_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Features</label>
                <div id="featuresContainer">
                    @if(old('features'))
                        @foreach(old('features') as $index => $feature)
                            <div class="input-group mb-2">
                                <input type="text" name="features[]" class="form-control" value="{{ $feature }}">
                                <button type="button" class="btn btn-danger remove-feature"><i class="fas fa-times"></i></button>
                            </div>
                        @endforeach
                    @else
                        <div class="input-group mb-2">
                            <input type="text" name="features[]" class="form-control">
                            <button type="button" class="btn btn-danger remove-feature"><i class="fas fa-times"></i></button>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn btn-secondary btn-sm" id="addFeature">
                    <i class="fas fa-plus"></i> Add Feature
                </button>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Specifications</label>
                <div id="specificationsContainer">
                    @if(old('specifications'))
                        @foreach(old('specifications') as $index => $spec)
                            <div class="input-group mb-2">
                                <input type="text" name="specifications[]" class="form-control" value="{{ $spec }}">
                                <button type="button" class="btn btn-danger remove-spec"><i class="fas fa-times"></i></button>
                            </div>
                        @endforeach
                    @else
                        <div class="input-group mb-2">
                            <input type="text" name="specifications[]" class="form-control">
                            <button type="button" class="btn btn-danger remove-spec"><i class="fas fa-times"></i></button>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn btn-secondary btn-sm" id="addSpec">
                    <i class="fas fa-plus"></i> Add Specification
                </button>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Model
                </button>
                <a href="{{ route('admin.models.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const brandSelect = document.getElementById('brand_id');
    const categorySelect = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('subcategory_id');
    
    // Event listener for brand select
    brandSelect.addEventListener('change', function() {
        const brandId = this.value;
        categorySelect.disabled = true;
        categorySelect.innerHTML = '<option value="">Loading categories...</option>';
        subcategorySelect.disabled = true;
        subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
        
        if (brandId) {
            // Fetch categories based on selected brand
            fetch(`{{ route('admin.api.categories-by-brand') }}?brand_id=${brandId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    categorySelect.innerHTML = '<option value="">Select Category</option>';
                    if (data.success && data.categories.length > 0) {
                        data.categories.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            categorySelect.appendChild(option);
                        });
                        categorySelect.disabled = false;
                    } else {
                        categorySelect.innerHTML = '<option value="">No categories found</option>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching categories:', error);
                    categorySelect.innerHTML = '<option value="">Error loading categories</option>';
                });
        } else {
            categorySelect.innerHTML = '<option value="">Select Category</option>';
        }
    });
    
    // Event listener for category select
    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;
        subcategorySelect.disabled = true;
        subcategorySelect.innerHTML = '<option value="">Loading subcategories...</option>';
        
        if (categoryId) {
            // Fetch subcategories based on selected category
            fetch(`{{ route('admin.api.subcategories-by-category') }}?category_id=${categoryId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
                    if (data.success && data.subcategories.length > 0) {
                        data.subcategories.forEach(subcategory => {
                            const option = document.createElement('option');
                            option.value = subcategory.id;
                            option.textContent = subcategory.name;
                            subcategorySelect.appendChild(option);
                        });
                        subcategorySelect.disabled = false;
                    } else {
                        subcategorySelect.innerHTML = '<option value="">No subcategories found</option>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching subcategories:', error);
                    subcategorySelect.innerHTML = '<option value="">Error loading subcategories</option>';
                });
        } else {
            subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
        }
    });
    
    // Initialize dropdowns if there are old values
    if (brandSelect.value) {
        brandSelect.dispatchEvent(new Event('change'));
        
        // We need to wait for the categories to load before triggering category change
        setTimeout(() => {
            if ('{{ old('category_id') }}') {
                categorySelect.value = '{{ old('category_id') }}';
                categorySelect.dispatchEvent(new Event('change'));
            }
        }, 500);
    }
    
    // Features and Specifications dynamic fields
    document.getElementById('addFeature').addEventListener('click', function() {
        const container = document.getElementById('featuresContainer');
        const newRow = document.createElement('div');
        newRow.classList.add('input-group', 'mb-2');
        newRow.innerHTML = `
            <input type="text" name="features[]" class="form-control">
            <button type="button" class="btn btn-danger remove-feature"><i class="fas fa-times"></i></button>
        `;
        container.appendChild(newRow);
        
        // Add event listener to the new remove button
        newRow.querySelector('.remove-feature').addEventListener('click', function() {
            container.removeChild(newRow);
        });
    });
    
    document.getElementById('addSpec').addEventListener('click', function() {
        const container = document.getElementById('specificationsContainer');
        const newRow = document.createElement('div');
        newRow.classList.add('input-group', 'mb-2');
        newRow.innerHTML = `
            <input type="text" name="specifications[]" class="form-control">
            <button type="button" class="btn btn-danger remove-spec"><i class="fas fa-times"></i></button>
        `;
        container.appendChild(newRow);
        
        // Add event listener to the new remove button
        newRow.querySelector('.remove-spec').addEventListener('click', function() {
            container.removeChild(newRow);
        });
    });
    
    // Add event listeners to existing remove buttons
    document.querySelectorAll('.remove-feature').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('.input-group');
            row.parentNode.removeChild(row);
        });
    });
    
    document.querySelectorAll('.remove-spec').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('.input-group');
            row.parentNode.removeChild(row);
        });
    });
});
</script>
@endsection 