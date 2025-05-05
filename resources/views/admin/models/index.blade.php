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
                <div class="col-md-3 mb-3">
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
                <div class="col-md-3 mb-3">
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
                <div class="col-md-3 mb-3">
                    <label for="subcategory_id" class="form-label">Subcategory</label>
                    <select name="subcategory_id" id="subcategory_id" class="form-select">
                        <option value="">All Subcategories</option>
                        @foreach($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}" {{ request('subcategory_id') == $subcategory->id ? 'selected' : '' }}>
                                {{ $subcategory->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
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
                        <th>Subcategory</th>
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
                            <td>{{ $model->subcategory->name ?? 'N/A' }}</td>
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
                                            data-subcategory="{{ $model->subcategory_id }}"
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
                            <td colspan="7" class="text-center">No models found</td>
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
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <label for="create_category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="create_category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback category-error"></div>
                        </div>
                        
                        <!-- Subcategory Dropdown -->
                        <div class="col-md-4">
                            <label for="create_subcategory_id" class="form-label">Subcategory <span class="text-danger">*</span></label>
                            <select name="subcategory_id" id="create_subcategory_id" class="form-select" required>
                                <option value="">Select Category First</option>
                            </select>
                            <div class="invalid-feedback subcategory-error"></div>
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
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <label for="edit_category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="edit_category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback category-error"></div>
                        </div>
                        
                        <!-- Subcategory Dropdown -->
                        <div class="col-md-4">
                            <label for="edit_subcategory_id" class="form-label">Subcategory <span class="text-danger">*</span></label>
                            <select name="subcategory_id" id="edit_subcategory_id" class="form-select" required>
                                <option value="">Loading subcategories...</option>
                            </select>
                            <div class="invalid-feedback subcategory-error"></div>
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
        // Filter form functionality - Update subcategories when category changes
        const filterCategorySelect = document.getElementById('category_id');
        const filterSubcategorySelect = document.getElementById('subcategory_id');
        
        if (filterCategorySelect && filterSubcategorySelect) {
            filterCategorySelect.addEventListener('change', function() {
                const categoryId = this.value;
                if (categoryId) {
                    fetch(`/admin/models/get-subcategories-by-category?category_id=${categoryId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            filterSubcategorySelect.innerHTML = '<option value="">All Subcategories</option>';
                            data.subcategories.forEach(subcategory => {
                                const option = document.createElement('option');
                                option.value = subcategory.id;
                                option.textContent = subcategory.name;
                                filterSubcategorySelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => console.error('Error fetching subcategories:', error));
                } else {
                    filterSubcategorySelect.innerHTML = '<option value="">All Subcategories</option>';
                }
            });
        }

        // Create Modal functionality
        const createBrandSelect = document.getElementById('create_brand_id');
        const createCategorySelect = document.getElementById('create_category_id');
        const createSubcategorySelect = document.getElementById('create_subcategory_id');
        
        // Update subcategories when category changes in create form
        if (createCategorySelect && createSubcategorySelect) {
            createCategorySelect.addEventListener('change', function() {
                const categoryId = this.value;
                createSubcategorySelect.disabled = true;
                createSubcategorySelect.innerHTML = '<option value="">Loading subcategories...</option>';
                
                if (categoryId) {
                    fetch(`/admin/api/subcategories-by-category?category_id=${categoryId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        createSubcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
                        if (data.success && data.subcategories.length > 0) {
                            data.subcategories.forEach(subcategory => {
                                const option = document.createElement('option');
                                option.value = subcategory.id;
                                option.textContent = subcategory.name;
                                createSubcategorySelect.appendChild(option);
                            });
                            createSubcategorySelect.disabled = false;
                        } else {
                            createSubcategorySelect.innerHTML = '<option value="">No subcategories found</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching subcategories:', error);
                        createSubcategorySelect.innerHTML = '<option value="">Error loading subcategories</option>';
                    });
                } else {
                    createSubcategorySelect.innerHTML = '<option value="">Select Category First</option>';
                    createSubcategorySelect.disabled = true;
                }
            });
        }
        
        // Add feature in create form
        document.getElementById('add_create_feature').addEventListener('click', function() {
            const container = document.getElementById('create_featuresContainer');
            const newRow = document.createElement('div');
            newRow.classList.add('input-group', 'mb-2');
            newRow.innerHTML = `
                <input type="text" name="features[]" class="form-control">
                <button type="button" class="btn btn-danger remove-create-feature"><i class="fas fa-times"></i></button>
            `;
            container.appendChild(newRow);
            
            // Add event listener to the new remove button
            newRow.querySelector('.remove-create-feature').addEventListener('click', function() {
                container.removeChild(newRow);
            });
        });
        
        // Remove feature in create form
        document.querySelectorAll('.remove-create-feature').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('.input-group');
                row.parentNode.removeChild(row);
            });
        });
        
        // Add specification in create form
        document.getElementById('add_create_spec').addEventListener('click', function() {
            const container = document.getElementById('create_specificationsContainer');
            const newRow = document.createElement('div');
            newRow.classList.add('input-group', 'mb-2');
            newRow.innerHTML = `
                <input type="text" name="specifications[]" class="form-control">
                <button type="button" class="btn btn-danger remove-create-spec"><i class="fas fa-times"></i></button>
            `;
            container.appendChild(newRow);
            
            // Add event listener to the new remove button
            newRow.querySelector('.remove-create-spec').addEventListener('click', function() {
                container.removeChild(newRow);
            });
        });
        
        // Remove specification in create form
        document.querySelectorAll('.remove-create-spec').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('.input-group');
                row.parentNode.removeChild(row);
            });
        });
        
        // Edit Modal functionality
        const editBrandSelect = document.getElementById('edit_brand_id');
        const editCategorySelect = document.getElementById('edit_category_id');
        const editSubcategorySelect = document.getElementById('edit_subcategory_id');
        
        // Update subcategories when category changes in edit form
        if (editCategorySelect && editSubcategorySelect) {
            editCategorySelect.addEventListener('change', function() {
                const categoryId = this.value;
                editSubcategorySelect.disabled = true;
                editSubcategorySelect.innerHTML = '<option value="">Loading subcategories...</option>';
                
                if (categoryId) {
                    fetch(`/admin/api/subcategories-by-category?category_id=${categoryId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        editSubcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
                        if (data.success && data.subcategories.length > 0) {
                            data.subcategories.forEach(subcategory => {
                                const option = document.createElement('option');
                                option.value = subcategory.id;
                                option.textContent = subcategory.name;
                                editSubcategorySelect.appendChild(option);
                            });
                            editSubcategorySelect.disabled = false;
                        } else {
                            editSubcategorySelect.innerHTML = '<option value="">No subcategories found</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching subcategories:', error);
                        editSubcategorySelect.innerHTML = '<option value="">Error loading subcategories</option>';
                    });
                } else {
                    editSubcategorySelect.innerHTML = '<option value="">Select Category First</option>';
                    editSubcategorySelect.disabled = true;
                }
            });
        }
        
        // Add feature in edit form
        document.getElementById('add_edit_feature').addEventListener('click', function() {
            const container = document.getElementById('edit_featuresContainer');
            const newRow = document.createElement('div');
            newRow.classList.add('input-group', 'mb-2');
            newRow.innerHTML = `
                <input type="text" name="features[]" class="form-control">
                <button type="button" class="btn btn-danger remove-edit-feature"><i class="fas fa-times"></i></button>
            `;
            container.appendChild(newRow);
            
            // Add event listener to the new remove button
            newRow.querySelector('.remove-edit-feature').addEventListener('click', function() {
                container.removeChild(newRow);
            });
        });
        
        // Add specification in edit form
        document.getElementById('add_edit_spec').addEventListener('click', function() {
            const container = document.getElementById('edit_specificationsContainer');
            const newRow = document.createElement('div');
            newRow.classList.add('input-group', 'mb-2');
            newRow.innerHTML = `
                <input type="text" name="specifications[]" class="form-control">
                <button type="button" class="btn btn-danger remove-edit-spec"><i class="fas fa-times"></i></button>
            `;
            container.appendChild(newRow);
            
            // Add event listener to the new remove button
            newRow.querySelector('.remove-edit-spec').addEventListener('click', function() {
                container.removeChild(newRow);
            });
        });
        
        // Load model data into edit form
        const editModelButtons = document.querySelectorAll('.edit-model-btn');
        editModelButtons.forEach(button => {
            button.addEventListener('click', function() {
                const modelId = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const description = this.getAttribute('data-description');
                const brandId = this.getAttribute('data-brand');
                const categoryId = this.getAttribute('data-category');
                const subcategoryId = this.getAttribute('data-subcategory');
                const isActive = this.getAttribute('data-active') === '1' || this.getAttribute('data-active') === 'true';
                
                document.getElementById('edit_model_id').value = modelId;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_description').value = description || '';
                document.getElementById('edit_brand_id').value = brandId;
                document.getElementById('edit_category_id').value = categoryId;
                document.getElementById('edit_is_active').checked = isActive;
                
                // Update form action
                document.getElementById('editModelForm').action = `/admin/models/${modelId}`;
                
                // Load subcategories based on selected category
                if (categoryId) {
                    fetch(`/admin/api/subcategories-by-category?category_id=${categoryId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        editSubcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
                        if (data.success && data.subcategories.length > 0) {
                            data.subcategories.forEach(subcategory => {
                                const option = document.createElement('option');
                                option.value = subcategory.id;
                                option.textContent = subcategory.name;
                                option.selected = subcategory.id == subcategoryId;
                                editSubcategorySelect.appendChild(option);
                            });
                            editSubcategorySelect.disabled = false;
                        }
                    });
                }
                
                // Load features and specifications via AJAX
                fetch(`/admin/models/${modelId}/edit`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Load features
                        const featuresContainer = document.getElementById('edit_featuresContainer');
                        featuresContainer.innerHTML = '';
                        
                        if (data.model.features && data.model.features.length > 0) {
                            data.model.features.forEach(feature => {
                                const featureRow = document.createElement('div');
                                featureRow.classList.add('input-group', 'mb-2');
                                featureRow.innerHTML = `
                                    <input type="text" name="features[]" class="form-control" value="${feature}">
                                    <button type="button" class="btn btn-danger remove-edit-feature"><i class="fas fa-times"></i></button>
                                `;
                                featuresContainer.appendChild(featureRow);
                                
                                // Add event listener to remove button
                                featureRow.querySelector('.remove-edit-feature').addEventListener('click', function() {
                                    featuresContainer.removeChild(featureRow);
                                });
                            });
                        } else {
                            // Add one empty row
                            const featureRow = document.createElement('div');
                            featureRow.classList.add('input-group', 'mb-2');
                            featureRow.innerHTML = `
                                <input type="text" name="features[]" class="form-control">
                                <button type="button" class="btn btn-danger remove-edit-feature"><i class="fas fa-times"></i></button>
                            `;
                            featuresContainer.appendChild(featureRow);
                            
                            featureRow.querySelector('.remove-edit-feature').addEventListener('click', function() {
                                featuresContainer.removeChild(featureRow);
                            });
                        }
                        
                        // Load specifications
                        const specificationsContainer = document.getElementById('edit_specificationsContainer');
                        specificationsContainer.innerHTML = '';
                        
                        if (data.model.specifications && data.model.specifications.length > 0) {
                            data.model.specifications.forEach(spec => {
                                const specRow = document.createElement('div');
                                specRow.classList.add('input-group', 'mb-2');
                                specRow.innerHTML = `
                                    <input type="text" name="specifications[]" class="form-control" value="${spec}">
                                    <button type="button" class="btn btn-danger remove-edit-spec"><i class="fas fa-times"></i></button>
                                `;
                                specificationsContainer.appendChild(specRow);
                                
                                // Add event listener to remove button
                                specRow.querySelector('.remove-edit-spec').addEventListener('click', function() {
                                    specificationsContainer.removeChild(specRow);
                                });
                            });
                        } else {
                            // Add one empty row
                            const specRow = document.createElement('div');
                            specRow.classList.add('input-group', 'mb-2');
                            specRow.innerHTML = `
                                <input type="text" name="specifications[]" class="form-control">
                                <button type="button" class="btn btn-danger remove-edit-spec"><i class="fas fa-times"></i></button>
                            `;
                            specificationsContainer.appendChild(specRow);
                            
                            specRow.querySelector('.remove-edit-spec').addEventListener('click', function() {
                                specificationsContainer.removeChild(specRow);
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading model data:', error);
                });
            });
        });
    });
</script>
@endsection 