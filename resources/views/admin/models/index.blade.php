@extends('layouts.admin')

@section('title', 'Models Management')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h1>Models Management</h1>
    </div>
    <div class="col-md-6 text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModelModal">
            <i class="fas fa-plus me-1"></i> Add New Model
        </button>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Filter Models</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.models.index') }}" method="GET">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="brand_id" class="form-label">Brand</label>
                    <select name="brand_id" id="brand_id" class="form-select">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select name="category_id" id="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="is_active" class="form-label">Status</label>
                    <select name="is_active" id="is_active" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="true" {{ request('is_active') == 'true' ? 'selected' : '' }}>Active</option>
                        <option value="false" {{ request('is_active') == 'false' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-9 mb-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Search by name...">
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">All Models</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($models as $model)
                        <tr>
                            <td>{{ $model->id }}</td>
                            <td>{{ $model->name }}</td>
                            <td>{{ $model->brand->name ?? 'N/A' }}</td>
                            <td>{{ $model->category->name ?? 'N/A' }}</td>
                            <td>
                                @if($model->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.models.show', $model->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary edit-model-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModelModal" 
                                            data-id="{{ $model->id }}"
                                            data-name="{{ $model->name }}"
                                            data-description="{{ $model->description }}"
                                            data-brand="{{ $model->brand_id }}"
                                            data-category="{{ $model->category_id }}"
                                            data-active="{{ $model->is_active }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.models.destroy', $model->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this model?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No models found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $models->links() }}
        </div>
    </div>
</div>

<!-- Create Model Modal -->
<div class="modal fade" id="createModelModal" tabindex="-1" aria-labelledby="createModelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModelModalLabel">Add New Model</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.models.store') }}" method="POST" id="createModelForm">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="create_name" class="form-label">Model Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="create_name" class="form-control" required>
                            <div class="invalid-feedback name-error"></div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <!-- Brand Dropdown -->
                        <div class="col-md-6">
                            <label for="create_brand_id" class="form-label">Brand <span class="text-danger">*</span></label>
                            <select name="brand_id" id="create_brand_id" class="form-select" required>
                                <option value="">Select Brand</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback brand-error"></div>
                        </div>
                        
                        <!-- Category Dropdown -->
                        <div class="col-md-6">
                            <label for="create_category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="create_category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback category-error"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="create_description" class="form-label">Description</label>
                        <textarea name="description" id="create_description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Features</label>
                        <div id="create_featuresContainer">
                            <div class="input-group mb-2">
                                <input type="text" name="features[]" class="form-control">
                                <button type="button" class="btn btn-danger remove-create-feature"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" id="add_create_feature">
                            <i class="fas fa-plus"></i> Add Feature
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Specifications</label>
                        <div id="create_specificationsContainer">
                            <div class="input-group mb-2">
                                <input type="text" name="specifications[]" class="form-control">
                                <button type="button" class="btn btn-danger remove-create-spec"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" id="add_create_spec">
                            <i class="fas fa-plus"></i> Add Specification
                        </button>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="create_is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="create_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="createModelBtn">Save Model</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Model Modal -->
<div class="modal fade" id="editModelModal" tabindex="-1" aria-labelledby="editModelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModelModalLabel">Edit Model</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editModelForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_model_id">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="edit_name" class="form-label">Model Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                            <div class="invalid-feedback name-error"></div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <!-- Brand Dropdown -->
                        <div class="col-md-6">
                            <label for="edit_brand_id" class="form-label">Brand <span class="text-danger">*</span></label>
                            <select name="brand_id" id="edit_brand_id" class="form-select" required>
                                <option value="">Select Brand</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback brand-error"></div>
                        </div>
                        
                        <!-- Category Dropdown -->
                        <div class="col-md-6">
                            <label for="edit_category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="edit_category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback category-error"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Features</label>
                        <div id="edit_featuresContainer">
                            <!-- Features will be loaded here -->
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" id="add_edit_feature">
                            <i class="fas fa-plus"></i> Add Feature
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Specifications</label>
                        <div id="edit_specificationsContainer">
                            <!-- Specifications will be loaded here -->
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" id="add_edit_spec">
                            <i class="fas fa-plus"></i> Add Specification
                        </button>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_is_active" name="is_active" value="1">
                        <label class="form-check-label" for="edit_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateModelBtn">Update Model</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Filter form brand and category relationships
    const filterBrand = document.getElementById('brand_id');
    const filterCategory = document.getElementById('category_id');
    
    if (filterBrand && filterCategory) {
        filterBrand.addEventListener('change', function() {
            const brandId = this.value;
            
            // Reset category dropdown
            filterCategory.disabled = true;
            filterCategory.innerHTML = '<option value="">Loading categories...</option>';
            
            if (brandId) {
                // Fetch categories for selected brand
                fetch(`{{ route('admin.api.categories-by-brand') }}?brand_id=${brandId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                    filterCategory.innerHTML = '<option value="">All Categories</option>';
                    
                    if (data.success && data.categories.length > 0) {
                        data.categories.forEach(category => {
                                const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            filterCategory.appendChild(option);
                        });
                } else {
                        const option = document.createElement('option');
                        option.disabled = true;
                        option.textContent = 'No categories found for this brand';
                        filterCategory.appendChild(option);
                    }
                    
                    filterCategory.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    filterCategory.innerHTML = '<option value="">Error loading categories</option>';
                    filterCategory.disabled = false;
                });
            } else {
                // No brand selected, show all categories
                fetch(`{{ route('admin.categories.index') }}?format=json`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                    filterCategory.innerHTML = '<option value="">All Categories</option>';
                    
                    if (data.categories && data.categories.length > 0) {
                        data.categories.forEach(category => {
                                const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            filterCategory.appendChild(option);
                        });
                    }
                    
                    filterCategory.disabled = false;
                    })
                    .catch(error => {
                    console.error('Error:', error);
                    filterCategory.innerHTML = '<option value="">All Categories</option>';
                    filterCategory.disabled = false;
                    });
                }
            });
        }
        
    // Create modal dynamic fields
    const addCreateFeatureBtn = document.getElementById('add_create_feature');
    const createFeaturesContainer = document.getElementById('create_featuresContainer');
    
    if (addCreateFeatureBtn && createFeaturesContainer) {
        addCreateFeatureBtn.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.classList.add('input-group', 'mb-2');
            newRow.innerHTML = `
                <input type="text" name="features[]" class="form-control">
                <button type="button" class="btn btn-danger remove-create-feature"><i class="fas fa-times"></i></button>
            `;
            createFeaturesContainer.appendChild(newRow);
            
            // Add event listener to remove button
            newRow.querySelector('.remove-create-feature').addEventListener('click', function() {
                createFeaturesContainer.removeChild(newRow);
            });
        });
        
        // Add event listeners to existing remove buttons
        document.querySelectorAll('.remove-create-feature').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('.input-group');
                row.parentNode.removeChild(row);
            });
        });
    }
    
    const addCreateSpecBtn = document.getElementById('add_create_spec');
    const createSpecsContainer = document.getElementById('create_specificationsContainer');
        
    if (addCreateSpecBtn && createSpecsContainer) {
        addCreateSpecBtn.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.classList.add('input-group', 'mb-2');
            newRow.innerHTML = `
                <input type="text" name="specifications[]" class="form-control">
                <button type="button" class="btn btn-danger remove-create-spec"><i class="fas fa-times"></i></button>
            `;
            createSpecsContainer.appendChild(newRow);
            
            // Add event listener to remove button
            newRow.querySelector('.remove-create-spec').addEventListener('click', function() {
                createSpecsContainer.removeChild(newRow);
            });
        });
        
        // Add event listeners to existing remove buttons
        document.querySelectorAll('.remove-create-spec').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('.input-group');
                row.parentNode.removeChild(row);
            });
        });
    }
    
    // Create form brand and category relationship
    const createBrandSelect = document.getElementById('create_brand_id');
    const createCategorySelect = document.getElementById('create_category_id');
    
    if (createBrandSelect && createCategorySelect) {
        createBrandSelect.addEventListener('change', function() {
            const brandId = this.value;
            
            // Reset category dropdown
            createCategorySelect.disabled = true;
            createCategorySelect.innerHTML = '<option value="">Loading categories...</option>';
            
            if (brandId) {
                // Fetch categories for selected brand
                fetch(`{{ route('admin.api.categories-by-brand') }}?brand_id=${brandId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    createCategorySelect.innerHTML = '<option value="">Select Category</option>';
                    
                    if (data.success && data.categories.length > 0) {
                        data.categories.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            createCategorySelect.appendChild(option);
                        });
                    } else {
                        const option = document.createElement('option');
                        option.disabled = true;
                        option.textContent = 'No categories found for this brand';
                        createCategorySelect.appendChild(option);
                    }
                    
                    createCategorySelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    createCategorySelect.innerHTML = '<option value="">Error loading categories</option>';
                    createCategorySelect.disabled = false;
                });
            } else {
                createCategorySelect.innerHTML = '<option value="">Select Brand First</option>';
                createCategorySelect.disabled = true;
            }
        });
    }
    
    // Edit modal
    const editButtons = document.querySelectorAll('.edit-model-btn');
    const editForm = document.getElementById('editModelForm');
    const editNameInput = document.getElementById('edit_name');
        const editBrandSelect = document.getElementById('edit_brand_id');
        const editCategorySelect = document.getElementById('edit_category_id');
    const editDescriptionInput = document.getElementById('edit_description');
    const editIsActiveCheckbox = document.getElementById('edit_is_active');
    
    if (editButtons.length > 0) {
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const modelId = this.getAttribute('data-id');
                const modelName = this.getAttribute('data-name');
                const description = this.getAttribute('data-description');
                const brandId = this.getAttribute('data-brand');
                const categoryId = this.getAttribute('data-category');
                const isActive = this.getAttribute('data-active') === '1' || this.getAttribute('data-active') === 'true';
                
                // Set form action URL with the model ID
                editForm.action = `/admin/models/${modelId}`;
                
                // Set values in the form
                editNameInput.value = modelName;
                editDescriptionInput.value = description || '';
                editIsActiveCheckbox.checked = isActive;
                
                // Set brand
                for (let i = 0; i < editBrandSelect.options.length; i++) {
                    if (editBrandSelect.options[i].value == brandId) {
                        editBrandSelect.options[i].selected = true;
                        break;
                    }
                }
                
                // Trigger change event to load categories
                editBrandSelect.dispatchEvent(new Event('change'));
                
                // We'll set category after categories are loaded
                const setCategory = setInterval(() => {
                    if (!editCategorySelect.disabled && editCategorySelect.options.length > 1) {
                        clearInterval(setCategory);
                        
                        for (let i = 0; i < editCategorySelect.options.length; i++) {
                            if (editCategorySelect.options[i].value == categoryId) {
                                editCategorySelect.options[i].selected = true;
                                break;
                            }
                        }
                    }
                }, 100);
            });
        });
    }
    
    // Edit form brand and category relationship
    if (editBrandSelect && editCategorySelect) {
        editBrandSelect.addEventListener('change', function() {
            const brandId = this.value;
            
            // Reset category dropdown
            editCategorySelect.disabled = true;
            editCategorySelect.innerHTML = '<option value="">Loading categories...</option>';
            
            if (brandId) {
                // Fetch categories for selected brand
                fetch(`{{ route('admin.api.categories-by-brand') }}?brand_id=${brandId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                    editCategorySelect.innerHTML = '<option value="">Select Category</option>';
                    
                    if (data.success && data.categories.length > 0) {
                        data.categories.forEach(category => {
                                const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            editCategorySelect.appendChild(option);
                        });
                        } else {
                        const option = document.createElement('option');
                        option.disabled = true;
                        option.textContent = 'No categories found for this brand';
                        editCategorySelect.appendChild(option);
                        }
                    
                    editCategorySelect.disabled = false;
                    })
                    .catch(error => {
                    console.error('Error:', error);
                    editCategorySelect.innerHTML = '<option value="">Error loading categories</option>';
                    editCategorySelect.disabled = false;
                    });
                } else {
                editCategorySelect.innerHTML = '<option value="">Select Brand First</option>';
                editCategorySelect.disabled = true;
                }
            });
        }
        
    // Edit modal dynamic fields
    const addEditFeatureBtn = document.getElementById('add_edit_feature');
    const editFeaturesContainer = document.getElementById('edit_featuresContainer');
    
    if (addEditFeatureBtn && editFeaturesContainer) {
        addEditFeatureBtn.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.classList.add('input-group', 'mb-2');
            newRow.innerHTML = `
                <input type="text" name="features[]" class="form-control">
                <button type="button" class="btn btn-danger remove-edit-feature"><i class="fas fa-times"></i></button>
            `;
            editFeaturesContainer.appendChild(newRow);
            
            // Add event listener to remove button
            newRow.querySelector('.remove-edit-feature').addEventListener('click', function() {
                editFeaturesContainer.removeChild(newRow);
            });
        });
        
        // Add event listeners to existing remove buttons
        document.querySelectorAll('.remove-edit-feature').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('.input-group');
                row.parentNode.removeChild(row);
            });
        });
    }
    
    const addEditSpecBtn = document.getElementById('add_edit_spec');
    const editSpecsContainer = document.getElementById('edit_specificationsContainer');
    
    if (addEditSpecBtn && editSpecsContainer) {
        addEditSpecBtn.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.classList.add('input-group', 'mb-2');
            newRow.innerHTML = `
                <input type="text" name="specifications[]" class="form-control">
                <button type="button" class="btn btn-danger remove-edit-spec"><i class="fas fa-times"></i></button>
            `;
            editSpecsContainer.appendChild(newRow);
            
            // Add event listener to remove button
            newRow.querySelector('.remove-edit-spec').addEventListener('click', function() {
                editSpecsContainer.removeChild(newRow);
            });
        });
        
        // Add event listeners to existing remove buttons
        document.querySelectorAll('.remove-edit-spec').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('.input-group');
                row.parentNode.removeChild(row);
            });
        });
    }
    });
</script>
@endsection 