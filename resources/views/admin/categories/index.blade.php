@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Categories</h5>
        <button type="button" class="btn btn-primary" data-open-modal="createCategoryModal">
            <i class="fas fa-plus me-1"></i>Add New Category
        </button>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form id="filterForm" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by name or description...">
                </div>
                
                <div class="col-md-4">
                    <label for="sort" class="form-label">Sort By</label>
                    <div class="input-group">
                        <select class="form-select" id="sort" name="sort">
                            <option value="name" {{ request('sort', 'name') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date Created</option>
                            <option value="brand_id" {{ request('sort') == 'brand_id' ? 'selected' : '' }}>Brand</option>
                        </select>
                        <select class="form-select" id="direction" name="direction">
                            <option value="asc" {{ request('direction', 'asc') == 'asc' ? 'selected' : '' }}>Ascending</option>
                            <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
        
        <!-- Active Filters Display -->
        @if(request()->anyFilled(['search', 'sort', 'direction']))
            <div class="d-flex flex-wrap gap-2 mb-3 active-filters">
                <span class="fw-bold me-2">Active Filters:</span>
                
                @if(request('search'))
                    <span class="badge bg-info">
                        Search: "{{ request('search') }}"
                        <a href="{{ request()->fullUrlWithoutQuery(['search']) }}" class="text-white ms-1 text-decoration-none">×</a>
                    </span>
                @endif
                
                @if(request('sort') && request('sort') != 'name')
                    <span class="badge bg-secondary">
                        Sort: {{ ucfirst(str_replace('_', ' ', request('sort'))) }} 
                        ({{ request('direction', 'asc') == 'asc' ? '↑' : '↓' }})
                        <a href="{{ request()->fullUrlWithoutQuery(['sort', 'direction']) }}" class="text-white ms-1 text-decoration-none">×</a>
                    </span>
                @endif
            </div>
        @endif
        
        <!-- Filter Status -->
        <div class="filter-status mb-3">
            Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }} categories
            @if(request()->anyFilled(['search']))
                (filtered from {{ \App\Models\Category::count() }} total)
            @endif
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Brand</th>
                        <th>Products</th>
                        <th>Specs</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>
                                @if($category->icon)
                                    <i class="{{ $category->icon }} me-1"></i>
                                @endif
                                {{ $category->name }}
                                @if($category->is_featured)
                                    <span class="badge bg-success ms-1">Featured</span>
                                @endif
                                @if($category->brand_featured)
                                    <span class="badge bg-info ms-1">Brand Featured</span>
                                @endif
                            </td>
                            <td>{{ $category->slug }}</td>
                            <td>
                                @if($category->brand)
                                    <a href="{{ route('admin.brands.edit', $category->brand_id) }}" class="text-decoration-none">
                                        {{ $category->brand->name }}
                                    </a>
                                @else
                                    <span class="text-muted">General</span>
                                @endif
                            </td>
                            <td>{{ $category->products->count() }}</td>
                            <td>{{ $category->specificationTypes->count() }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="{{ route('admin.specifications', $category->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-list"></i> Specs
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" data-delete-confirm="Are you sure you want to delete this category?">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <nav aria-label="Category pagination">
                {{ $categories->withQueryString()->links('pagination::bootstrap-5') }}
            </nav>
        </div>
    </div>
</div>

<!-- Create Category Modal -->
<x-admin-form-modal 
    id="createCategoryModal" 
    title="Create Category" 
    formId="createCategoryForm" 
    formAction="{{ route('admin.categories.store') }}" 
    formMethod="POST"
    submitButtonText="Save Category">

    <div class="mb-3">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-3">
        <label for="slug" class="form-label">Slug</label>
        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}">
        <small class="form-text text-muted">Leave blank to auto-generate from name</small>
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-3">
        <label for="brand_id" class="form-label">Associated Brand</label>
        <select class="form-select @error('brand_id') is-invalid @enderror" id="brand_id" name="brand_id">
            <option value="">None (General Category)</option>
            @foreach(\App\Models\Brand::orderBy('name')->get() as $brand)
                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
            @endforeach
        </select>
        <small class="form-text text-muted">Select a brand if this category is brand-specific</small>
        @error('brand_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-3">
        <label for="icon" class="form-label">Icon Class (FontAwesome)</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-icons"></i></span>
            <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon') }}" placeholder="e.g. fas fa-laptop">
        </div>
        <small class="form-text text-muted">Visit <a href="https://fontawesome.com/icons" target="_blank">FontAwesome</a> to find icons</small>
        @error('icon')
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
    
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
        <label class="form-check-label" for="is_featured">Featured Category</label>
        <small class="form-text text-muted d-block">Display this category prominently on the homepage</small>
    </div>
    
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="brand_featured" name="brand_featured" value="1" {{ old('brand_featured') ? 'checked' : '' }}>
        <label class="form-check-label" for="brand_featured">Featured for Brand</label>
        <small class="form-text text-muted d-block">Display this category prominently on the brand page</small>
    </div>
</x-admin-form-modal>

<!-- Edit Category Modal -->
<x-admin-form-modal 
    id="editCategoryModal" 
    title="Edit Category" 
    formId="editCategoryForm" 
    formMethod="POST"
    submitButtonText="Update Category">
    <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading category data...</p>
    </div>
</x-admin-form-modal>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // When Edit button is clicked, update the form action
        const editButtons = document.querySelectorAll('[data-edit-url]');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const url = this.getAttribute('data-edit-url');
                if (url) {
                    // This will be processed by the admin-modals.js script
                    // Form will be loaded via AJAX
                    const formAction = url.replace('/edit', '');
                    const editForm = document.getElementById('editCategoryForm');
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
                }
            });
        });
    });
</script>
@endsection 