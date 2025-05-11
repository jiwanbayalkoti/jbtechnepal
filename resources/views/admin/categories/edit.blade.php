@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit Category</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="slug" class="form-label">Slug</label>
                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $category->slug) }}">
                <small class="form-text text-muted">Leave blank to auto-generate from name</small>
                @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="brand_id" class="form-label">Associated Brand</label>
                <select class="form-select @error('brand_id') is-invalid @enderror" id="brand_id" name="brand_id">
                    <option value="">None (General Category)</option>
                    @if(isset($brands) && $brands->count() > 0)
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ (old('brand_id', $category->brand_id) == $brand->id) ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    @else
                        @php
                            $brandModel = \App\Models\Brand::find($category->brand_id);
                        @endphp
                        @if($brandModel)
                            <option value="{{ $brandModel->id }}" selected>{{ $brandModel->name }}</option>
                        @endif
                    @endif
                </select>
                <small class="form-text text-muted">Select a brand if this category is brand-specific</small>
                @error('brand_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="icon" class="form-label">Icon Class (FontAwesome)</label>
                <div class="input-group">
                    <span class="input-group-text">
                        @if($category->icon)
                            <i class="{{ $category->icon }}"></i>
                        @else
                            <i class="fas fa-icons"></i>
                        @endif
                    </span>
                    <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon', $category->icon) }}" placeholder="e.g. fas fa-laptop">
                    @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <small class="form-text text-muted">Visit <a href="https://fontawesome.com/icons" target="_blank">FontAwesome</a> to find icons</small>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $category->is_featured) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_featured">Featured Category</label>
                <small class="form-text text-muted d-block">Display this category prominently on the homepage</small>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="brand_featured" name="brand_featured" value="1" {{ old('brand_featured', $category->brand_featured) ? 'checked' : '' }}>
                <label class="form-check-label" for="brand_featured">Featured for Brand</label>
                <small class="form-text text-muted d-block">Display this category prominently on the brand page (only applies if a brand is selected)</small>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Update Category
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Specification Types</h5>
        <a href="{{ route('admin.specifications.create', $category->id) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Add Specification Type
        </a>
    </div>
    <div class="card-body">
        <p>Manage specification types for this category.</p>
        <a href="{{ route('admin.specifications', ['categoryId' => $category->id]) }}" class="btn btn-info">
            <i class="fas fa-list me-1"></i>View Specification Types
        </a>
    </div>
</div>
@endsection 