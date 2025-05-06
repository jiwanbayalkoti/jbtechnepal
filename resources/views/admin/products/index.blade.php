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
                    <label for="filter_subcategory" class="form-label">Subcategory</label>
                    <select class="form-select" id="filter_subcategory" name="subcategory">
                        <option value="">All Subcategories</option>
                        @if(request('category') && isset($subcategories))
                            @foreach($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}" {{ request('subcategory') == $subcategory->id ? 'selected' : '' }}>
                                    {{ $subcategory->name }}
                                </option>
                            @endforeach
                        @endif
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
                
                @if(request('subcategory'))
                    <span class="badge bg-secondary">
                        Subcategory: {{ $subcategories->where('id', request('subcategory'))->first()->name ?? 'Unknown' }}
                        <a href="{{ request()->fullUrlWithoutQuery(['subcategory']) }}" class="text-white ms-1 text-decoration-none">×</a>
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
            <option value="">Select Category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
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
            <!-- Subcategories will be loaded dynamically based on selected category -->
        </select>
        @error('subcategory_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
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
        const filterSubcategory = document.getElementById('filter_subcategory');

        // Category change handler for filter form
        if (filterCategory && filterSubcategory) {
            filterCategory.addEventListener('change', function() {
                const categoryId = this.value;
                
                // Clear existing options except the first one
                while (filterSubcategory.options.length > 1) {
                    filterSubcategory.remove(1);
                }
                
                if (categoryId) {
                    // Show loading indicator
                    const loadingOption = document.createElement('option');
                    loadingOption.textContent = 'Loading...';
                    loadingOption.disabled = true;
                    filterSubcategory.appendChild(loadingOption);
                    
                    // Fetch subcategories for the selected category
                    fetch(`{{ url('admin/subcategories') }}/${categoryId}`)
                        .then(response => response.json())
                        .then(data => {
                            // Remove loading option
                            filterSubcategory.remove(filterSubcategory.options.length - 1);
                            
                            if (data.success && data.subcategories) {
                                // Add new options
                                data.subcategories.forEach(subcategory => {
                                    const option = document.createElement('option');
                                    option.value = subcategory.id;
                                    option.textContent = subcategory.name;
                                    filterSubcategory.appendChild(option);
                                });
                                
                                if (data.subcategories.length === 0) {
                                    const noOption = document.createElement('option');
                                    noOption.textContent = 'No subcategories available';
                                    noOption.disabled = true;
                                    filterSubcategory.appendChild(noOption);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching subcategories:', error);
                            // Remove loading option on error
                            filterSubcategory.remove(filterSubcategory.options.length - 1);
                            
                            const errorOption = document.createElement('option');
                            errorOption.textContent = 'Error loading subcategories';
                            errorOption.disabled = true;
                            filterSubcategory.appendChild(errorOption);
                        });
                }
            });
        }

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

        // Category change handler for the create form
        const createForm = document.getElementById('createProductForm');
        if (createForm) {
            const categorySelect = createForm.querySelector('select[name="category_id"]');
            const subcategorySelect = createForm.querySelector('select[name="subcategory_id"]');
            
            if (categorySelect && subcategorySelect) {
                categorySelect.addEventListener('change', function() {
                    loadSubcategories(this, subcategorySelect);
                });
            }
        }
        
        // Function to load subcategories
        function loadSubcategories(categorySelect, subcategorySelect) {
            const categoryId = categorySelect.value;
            
            // Clear existing options except the first one
            while (subcategorySelect.options.length > 1) {
                subcategorySelect.remove(1);
            }
            
            // Clear model dropdown when category changes
            const createModalForm = document.getElementById('createProductForm');
            if (createModalForm) {
                const modelSelect = createModalForm.querySelector('select[name="model"]');
                if (modelSelect) {
                    while (modelSelect.options.length > 1) {
                        modelSelect.remove(1);
                    }
                }
            }
            
            if (categoryId) {
                console.log(categoryId);
                // Show loading indicator in the subcategory dropdown
                const loadingOption = document.createElement('option');
                loadingOption.textContent = 'Loading...';
                loadingOption.disabled = true;
                subcategorySelect.appendChild(loadingOption);
                
                // Fetch subcategories for the selected category
                fetch(`{{ url('admin/subcategories') }}/${categoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Remove loading option
                        subcategorySelect.remove(subcategorySelect.options.length - 1);
                        
                        if (data && data.subcategories) {
                            // Add new options
                            data.subcategories.forEach(subcategory => {
                                const option = document.createElement('option');
                                option.value = subcategory.id;
                                option.textContent = subcategory.name;
                                subcategorySelect.appendChild(option);
                            });
                            
                            if (data.subcategories.length === 0) {
                                const noOption = document.createElement('option');
                                noOption.textContent = 'No subcategories available';
                                noOption.disabled = true;
                                subcategorySelect.appendChild(noOption);
                                }
                            } else {
                                const errorOption = document.createElement('option');
                                errorOption.textContent = 'Error: Invalid data received';
                                errorOption.disabled = true;
                                subcategorySelect.appendChild(errorOption);
                            }
                        })
                    .catch(error => {
                        console.error('Error fetching subcategories:', error);
                        // Remove loading option on error
                        subcategorySelect.remove(subcategorySelect.options.length - 1);
                        
                        const errorOption = document.createElement('option');
                        errorOption.textContent = 'Error loading subcategories';
                        errorOption.disabled = true;
                        subcategorySelect.appendChild(errorOption);
                    });
            }
        }

        // Add event listeners for subcategory changes (for loading models)
        document.addEventListener('change', function(e) {
            if (e.target && e.target.matches('select[name="subcategory_id"]')) {
                const subcategoryId = e.target.value;
                // Find the closest form to this subcategory select
                const form = e.target.closest('form');
                if (!form) return;
                
                const modelSelect = form.querySelector('select[name="model"]');
                if (!modelSelect) return;
                
                // Clear existing model options except the first one
                while (modelSelect.options.length > 1) {
                    modelSelect.remove(1);
                }
                
                if (subcategoryId) {
                    // Add loading option
                    const loadingOption = document.createElement('option');
                    loadingOption.textContent = 'Loading models...';
                    loadingOption.disabled = true;
                    modelSelect.appendChild(loadingOption);
                    
                    // Fetch models for the selected subcategory
                    fetch(`{{ url('admin/models-by-subcategory') }}/${subcategoryId}`)
                        .then(response => response.json())
                        .then(data => {
                            // Remove loading option
                            modelSelect.remove(modelSelect.options.length - 1);
                            
                            if (data.success && data.models) {
                                // Add new options
                                data.models.forEach(model => {
                                    const option = document.createElement('option');
                                    option.value = model.name;
                                    option.textContent = model.name;
                                    modelSelect.appendChild(option);
                                });
                                
                                if (data.models.length === 0) {
                                    const noOption = document.createElement('option');
                                    noOption.textContent = 'No models available for this subcategory';
                                    noOption.disabled = true;
                                    modelSelect.appendChild(noOption);
                                }
                            } else {
                                const errorOption = document.createElement('option');
                                errorOption.textContent = 'Error: Invalid data received';
                                errorOption.disabled = true;
                                modelSelect.appendChild(errorOption);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching models:', error);
                            // Remove loading option on error
                            modelSelect.remove(modelSelect.options.length - 1);
                            
                            const errorOption = document.createElement('option');
                            errorOption.textContent = 'Error loading models';
                            errorOption.disabled = true;
                            modelSelect.appendChild(errorOption);
                        });
                }
            }
        });

        // When Edit button is clicked, update the form action
        const editButtons = document.querySelectorAll('[data-edit-url]');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const url = this.getAttribute('data-edit-url');
                const modalId = this.getAttribute('data-open-modal');
                
                if (url) {
                    // This will be processed by the admin-modals.js script
                    console.log('Edit URL clicked:', url);
                    
                    // Form action will be without the /edit suffix
                    const formAction = url.replace('/edit', '');
                    
                    // Define a function to find and setup the form
                    const setupEditForm = () => {
                        // First, try to find the form by ID
                        let editForm = document.getElementById('editProductForm');
                        
                        // If not found, look for any form in the modal
                        if (!editForm) {
                            const modalElement = document.getElementById(modalId);
                            if (modalElement) {
                                const modalBody = modalElement.querySelector('.modal-body');
                                if (modalBody) {
                                    // Find first form in the modal or in the formContent div
                                    const formContent = modalBody.querySelector('#formContent');
                                    editForm = modalBody.querySelector('form') || 
                                             (formContent ? formContent.querySelector('form') : null);
                                    
                                    // If found a form but it has no ID, assign the expected ID
                                    if (editForm && !editForm.id) {
                                        console.log('Found form without ID, assigning ID: editProductForm');
                                        editForm.id = 'editProductForm';
                                    }
                                }
                            }
                        }
                        
                        // Now setup the form if found
                        if (editForm) {
                            console.log('Found and setting up form:', editForm.id);
                            editForm.action = formAction;
                            
                            // Add method override for PUT
                            let methodInput = editForm.querySelector('input[name="_method"]');
                            if (!methodInput) {
                                methodInput = document.createElement('input');
                                methodInput.type = 'hidden';
                                methodInput.name = '_method';
                                editForm.appendChild(methodInput);
                            }
                            methodInput.value = 'PUT';
                            return true;
                        }
                        
                        console.warn('Edit form not found');
                        return false;
                    };
                    
                    // Try immediately (in case form is already in the page)
                    if (!setupEditForm()) {
                        // If not successful, try again after a short delay (for AJAX loading)
                        setTimeout(() => {
                            if (!setupEditForm()) {
                                // Try one more time after a longer delay
                                setTimeout(setupEditForm, 500);
                            }
                        }, 100);
                    }
                }
            });
        });
    });
</script>
@endsection 