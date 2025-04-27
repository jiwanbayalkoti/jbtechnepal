@extends('layouts.admin')

@section('title', 'Subcategories')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Subcategories</h5>
        <button type="button" class="btn btn-primary" data-open-modal="createSubcategoryModal">
            <i class="fas fa-plus me-1"></i>Add New Subcategory
        </button>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form id="filterForm" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="category" class="form-label">Parent Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by name or description...">
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.subcategories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
        
        <!-- Active Filters Display -->
        @if(request()->anyFilled(['category', 'status', 'search']))
            <div class="d-flex flex-wrap gap-2 mb-3 active-filters">
                <span class="fw-bold me-2">Active Filters:</span>
                
                @if(request('category'))
                    <span class="badge bg-primary">
                        Category: {{ $categories->where('id', request('category'))->first()->name ?? 'Unknown' }}
                        <a href="{{ request()->fullUrlWithoutQuery(['category']) }}" class="text-white ms-1 text-decoration-none">×</a>
                    </span>
                @endif
                
                @if(request('status'))
                    <span class="badge bg-secondary">
                        Status: {{ ucfirst(request('status')) }}
                        <a href="{{ request()->fullUrlWithoutQuery(['status']) }}" class="text-white ms-1 text-decoration-none">×</a>
                    </span>
                @endif
                
                @if(request('search'))
                    <span class="badge bg-info">
                        Search: "{{ request('search') }}"
                        <a href="{{ request()->fullUrlWithoutQuery(['search']) }}" class="text-white ms-1 text-decoration-none">×</a>
                    </span>
                @endif
            </div>
        @endif
        
        <!-- Filter Status -->
        <div class="filter-status mb-3">
            Showing {{ $subcategories->firstItem() ?? 0 }} to {{ $subcategories->lastItem() ?? 0 }} of {{ $subcategories->total() }} subcategories
            @if(request()->anyFilled(['category', 'status', 'search']))
                (filtered)
            @endif
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Parent Category</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subcategories as $subcategory)
                        <tr>
                            <td>{{ $subcategory->id }}</td>
                            <td>
                                @if($subcategory->icon)
                                    <i class="{{ $subcategory->icon }} me-1"></i>
                                @endif
                                {{ $subcategory->name }}
                            </td>
                            <td>{{ $subcategory->category->name }}</td>
                            <td>{{ $subcategory->slug }}</td>
                            <td>
                                @if($subcategory->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            data-edit-url="{{ route('admin.subcategories.edit', $subcategory->id) }}"
                                            data-open-modal="editSubcategoryModal">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.subcategories.destroy', $subcategory->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" data-delete-confirm="Are you sure you want to delete this subcategory?">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No subcategories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <nav aria-label="Subcategory pagination">
                {{ $subcategories->withQueryString()->links('pagination::bootstrap-5') }}
            </nav>
        </div>
    </div>
</div>

<!-- Create Subcategory Modal -->
<x-admin-form-modal 
    id="createSubcategoryModal" 
    title="Create Subcategory" 
    formId="createSubcategoryForm" 
    formAction="{{ route('admin.subcategories.store') }}" 
    formMethod="POST"
    submitButtonText="Save Subcategory">
    
    <div class="mb-3">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-3">
        <label for="category_id" class="form-label">Parent Category <span class="text-danger">*</span></label>
        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
            <option value="">Select Parent Category</option>
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
        <label for="icon" class="form-label">Icon Class (FontAwesome)</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-icons"></i>
            </span>
            <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon') }}" placeholder="e.g. fas fa-laptop">
            @error('icon')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="form-text text-muted">Visit <a href="https://fontawesome.com/icons" target="_blank">FontAwesome</a> to find icons</small>
    </div>
    
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
        <label class="form-check-label" for="is_active">Active</label>
    </div>
</x-admin-form-modal>

<!-- Edit Subcategory Modal -->
<x-admin-form-modal 
    id="editSubcategoryModal" 
    title="Edit Subcategory" 
    formId="editSubcategoryForm" 
    formMethod="POST"
    submitButtonText="Update Subcategory">
    <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading subcategory data...</p>
    </div>
</x-admin-form-modal>
@endsection 