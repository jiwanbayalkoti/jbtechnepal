@extends('layouts.admin')

@section('title', 'Products')

@section('styles')
<style>
    .active-filters .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5em 0.75em;
        font-size: 0.85rem;
    }
    
    .active-filters .badge a {
        margin-left: 5px;
        font-weight: bold;
    }
    
    .filter-status {
        font-size: 0.9rem;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
{{-- Debug information --}}
@if(isset($brands))
    <div class="alert alert-info">
        Brands count: {{ $brands->count() }}
    </div>
@else
    <div class="alert alert-danger">
        No brands variable available
    </div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Products</h5>
        <button type="button" class="btn btn-primary" data-open-modal="createProductModal">
            <i class="fas fa-plus me-1"></i>Add New Product
        </button>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form id="filterForm" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="filter_category" class="form-label">Category</label>
                    <select class="form-select" id="filter_category" name="category">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="filter_brand" class="form-label">Brand</label>
                    <select class="form-select" id="filter_brand" name="brand">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                                {{ $brand }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="filter_price_min" class="form-label">Min Price</label>
                    <input type="number" class="form-control" id="filter_price_min" name="price_min" value="{{ request('price_min') }}" min="0" step="0.01">
                </div>
                
                <div class="col-md-3">
                    <label for="filter_price_max" class="form-label">Max Price</label>
                    <input type="number" class="form-control" id="filter_price_max" name="price_max" value="{{ request('price_max') }}" min="0" step="0.01">
                </div>
                
                <div class="col-md-6">
                    <label for="filter_search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="filter_search" name="search" value="{{ request('search') }}" placeholder="Search by name or model...">
                </div>
                
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Apply Filters
                    </button>
                    <button type="button" class="btn btn-secondary" id="resetFilters">
                        <i class="fas fa-times me-1"></i>Reset
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Active Filters Display -->
        @if(request()->anyFilled(['category', 'subcategory', 'brand', 'price_min', 'price_max', 'search']))
            <div class="d-flex flex-wrap gap-2 mb-3 active-filters">
                <span class="fw-bold me-2">Active Filters:</span>
                
                @if(request('search'))
                    <span class="badge bg-info">
                        Search: "{{ request('search') }}"
                        <a href="{{ request()->fullUrlWithoutQuery(['search']) }}" class="text-white ms-1 text-decoration-none">×</a>
                    </span>
                @endif
                
                @if(request('category'))
                    <span class="badge bg-primary">
                        Category: {{ $categories->where('id', request('category'))->first()->name ?? 'Unknown' }}
                        <a href="{{ request()->fullUrlWithoutQuery(['category', 'subcategory']) }}" class="text-white ms-1 text-decoration-none">×</a>
                    </span>
                @endif
                
                @if(request('brand'))
                    <span class="badge bg-success">
                        Brand: {{ request('brand') }}
                        <a href="{{ request()->fullUrlWithoutQuery(['brand']) }}" class="text-white ms-1 text-decoration-none">×</a>
                    </span>
                @endif
                
                @if(request('price_min') || request('price_max'))
                    <span class="badge bg-warning text-dark">
                        Price: 
                        @if(request('price_min') && request('price_max'))
                            ${{ request('price_min') }} - ${{ request('price_max') }}
                        @elseif(request('price_min'))
                            ≥ ${{ request('price_min') }}
                        @elseif(request('price_max'))
                            ≤ ${{ request('price_max') }}
                        @endif
                        <a href="{{ request()->fullUrlWithoutQuery(['price_min', 'price_max']) }}" class="text-dark ms-1 text-decoration-none">×</a>
                    </span>
                @endif
                
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">
                    Clear All Filters
                </a>
            </div>
        @endif
        
        <!-- Filter Status -->
        <div class="filter-status mb-3">
            Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
            @if(request()->anyFilled(['category', 'subcategory', 'brand', 'price_min', 'price_max', 'search']))
                (filtered from {{ \App\Models\Product::count() }} total)
            @endif
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td class="text-center" style="width: 100px;">
                                @if($product->primary_image)
                                    <img src="{{ $product->primary_image->url }}" class="img-thumbnail" style="max-height: 60px;" alt="{{ $product->name }}">
                                @elseif($product->images->isNotEmpty())
                                    <img src="{{ $product->images->first()->url }}" class="img-thumbnail" style="max-height: 60px;" alt="{{ $product->name }}">
                                @else
                                    <i class="fas fa-image text-muted fa-2x"></i>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                @if($product->brand)
                                    <br><small>Brand: {{ $product->brand }}</small>
                                @endif
                                @if($product->model)
                                    <br><small>Model: {{ $product->model }}</small>
                                @endif
                            </td>
                            <td>{{ $product->category->name }}</td>
                            <td>${{ number_format($product->price, 2) }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            data-edit-url="{{ route('admin.products.edit', $product->id) }}"
                                            data-open-modal="editProductModal">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" data-delete-confirm="Are you sure you want to delete this product?">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="mt-4">
                <nav aria-label="Product pagination">
                    {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Create Product Modal -->
<x-admin-form-modal 
    id="createProductModal" 
    title="Create Product" 
    formId="createProductForm" 
    formAction="{{ route('admin.products.store') }}" 
    formMethod="POST"
    hasFiles="true"
    submitButtonText="Save Product">
    
    <div class="mb-3">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
            <label for="brand" class="form-label">Brand <span class="text-danger">*</span></label>
            <select class="form-select @error('brand') is-invalid @enderror" id="brand" name="brand" required>
                <option value="">Select Brand</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand }}" {{ old('brand') == $brand ? 'selected' : '' }}>
                        {{ $brand }}
                    </option>
                @endforeach
            </select>
            @error('brand')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    
    <div class="mb-3">
        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
            <option value="">Please select a brand first</option>
        </select>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-3">
        <label for="model" class="form-label">Model <span class="text-danger">*</span></label>
        <select class="form-select @error('model') is-invalid @enderror" id="model" name="model" required>
            <option value="">Please select a category first</option>
        </select>
        @error('model')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    
    <div class="mb-3">
        <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required>
            @error('price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="mb-3">
        <label for="images" class="form-label">Product Images</label>
        <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images[]" multiple accept="image/*">
        <small class="form-text text-muted">Upload product images (JPEG, PNG, GIF, max 2MB each)</small>
        @error('images')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</x-admin-form-modal>

<!-- Edit Product Modal -->
<x-admin-form-modal 
    id="editProductModal" 
    title="Edit Product" 
    formId="editProductForm" 
    formMethod="PUT"
    hasFiles="true"
    submitButtonText="Update Product">
    <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading product data...</p>
    </div>
</x-admin-form-modal>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter form handling
        const filterForm = document.getElementById('filterForm');
        const resetButton = document.getElementById('resetFilters');
        const filterCategory = document.getElementById('filter_category');

        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const params = new URLSearchParams();

                for (const [key, value] of formData.entries()) {
                    if (value) { // Only add non-empty values
                        params.append(key, value);
                    }
                }

                window.location.href = `${window.location.pathname}?${params.toString()}`;
            });
        }

        if (resetButton) {
            resetButton.addEventListener('click', function() {
                window.location.href = window.location.pathname;
            });
        }

        // Helper function to add event handlers to a form
        function addCascadingDropdowns(form) {
            if (!form) return;
            
            const brandSelect = form.querySelector('select[name="brand"]');
            const categorySelect = form.querySelector('select[name="category_id"]');
            const modelSelect = form.querySelector('select[name="model"]');
            
            // Initialize dropdowns - disable category and model initially
            if (categorySelect) {
                categorySelect.innerHTML = '<option value="">Please select a brand first</option>';
                categorySelect.disabled = true;
            }
            
                if (modelSelect) {
                modelSelect.innerHTML = '<option value="">Please select a category first</option>';
                modelSelect.disabled = true;
            }
            
            // Handle brand changes
            if (brandSelect) {
                brandSelect.addEventListener('change', function() {
                    const brandValue = this.value;
                    
                    // Clear and disable category dropdown while loading
                    if (categorySelect) {
                        if (brandValue) {
                            categorySelect.innerHTML = '<option value="">Loading categories...</option>';
                            categorySelect.disabled = true;
                            
                            // Fetch categories for the selected brand
                            fetch(`/admin/api/categories-by-brand?brand=${encodeURIComponent(brandValue)}`)
                    .then(response => response.json())
                    .then(data => {
                                    // Enable and populate category dropdown
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
                                        categorySelect.innerHTML = '<option value="">No categories found for this brand</option>';
                                        categorySelect.disabled = true;
                            }
                        })
                    .catch(error => {
                                    console.error('Error fetching categories:', error);
                                    categorySelect.innerHTML = '<option value="">Error loading categories</option>';
                                    categorySelect.disabled = true;
                                });
                        } else {
                            // If no brand selected, reset category dropdown
                            categorySelect.innerHTML = '<option value="">Please select a brand first</option>';
                            categorySelect.disabled = true;
                        }
                    }
                    
                    // Also reset model dropdown
                    if (modelSelect) {
                        modelSelect.innerHTML = '<option value="">Please select a category first</option>';
                        modelSelect.disabled = true;
                    }
                });
            }
            
            // Handle category changes
            if (categorySelect) {
                categorySelect.addEventListener('change', function() {
                    const categoryId = this.value;
                    
                    // Clear model dropdown
                    if (modelSelect) {
                        if (categoryId) {
                            modelSelect.innerHTML = '<option value="">Loading models...</option>';
                            modelSelect.disabled = true;
                            
                            // Fetch models for the selected category
                            fetch(`/admin/api/models-by-category/${categoryId}`)
                                .then(response => response.json())
                                .then(data => {
                                    // Enable and populate model dropdown
                                    modelSelect.innerHTML = '<option value="">Select Model</option>';
                                    
                                    if (data.success && data.models.length > 0) {
                                data.models.forEach(model => {
                                    const option = document.createElement('option');
                                    option.value = model.name;
                                    option.textContent = model.name;
                                    modelSelect.appendChild(option);
                                });
                                        modelSelect.disabled = false;
                            } else {
                                        modelSelect.innerHTML = '<option value="">No models available for this category</option>';
                                        modelSelect.disabled = true;
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching models:', error);
                                    modelSelect.innerHTML = '<option value="">Error loading models</option>';
                                    modelSelect.disabled = true;
                                });
                        } else {
                            // If no category selected, reset model dropdown
                            modelSelect.innerHTML = '<option value="">Please select a category first</option>';
                            modelSelect.disabled = true;
                }
            }
        });
            }
        }

        // Initialize cascading dropdowns for create form
        const createProductForm = document.getElementById('createProductForm');
        addCascadingDropdowns(createProductForm);
        
        // Event delegation for edit modal
        document.addEventListener('click', function(e) {
            // When edit modal is opened and loaded
            if (e.target && e.target.hasAttribute('data-edit-url')) {
                const editUrl = e.target.getAttribute('data-edit-url');
                if (editUrl) {
                    // Wait for the modal content to be loaded
                    const observer = new MutationObserver(function(mutations, observer) {
                        const editForm = document.getElementById('editProductForm');
                        if (editForm && !editForm.querySelector('.spinner-border')) {
                            // Add cascading dropdowns to the edit form
                            addCascadingDropdowns(editForm);
                            observer.disconnect();
                        }
                    });
                    
                    // Observe the edit form container
                    const editFormContainer = document.querySelector('#editProductModal .modal-body');
                    if (editFormContainer) {
                        observer.observe(editFormContainer, { childList: true, subtree: true });
                    }
                }
            }
        });
    });
</script>
@endsection 