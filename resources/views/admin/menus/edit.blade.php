@extends('layouts.admin')

@section('title', 'Edit Menu Item')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit Menu Item</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $menu->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="url" class="form-label">URL</label>
                    <input type="text" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $menu->url) }}">
                    <small class="form-text text-muted">External or internal URL (e.g., /contact, https://example.com)</small>
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="route_name" class="form-label">Route Name</label>
                    <input type="text" class="form-control @error('route_name') is-invalid @enderror" id="route_name" name="route_name" value="{{ old('route_name', $menu->route_name) }}">
                    <small class="form-text text-muted">Laravel route name (e.g., home, contact.index)</small>
                    @error('route_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-text text-danger mt-2">Either URL or Route Name must be provided unless this is a dynamic page.</div>
            </div>
            
            <div class="mb-3">
                <label for="icon" class="form-label">Icon Class (FontAwesome)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-icons"></i></span>
                    <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon', $menu->icon) }}" placeholder="e.g. fas fa-home">
                    @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <small class="form-text text-muted">Visit <a href="https://fontawesome.com/icons" target="_blank">FontAwesome</a> to find icons</small>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                    <select class="form-select @error('location') is-invalid @enderror" id="location" name="location" required>
                        @foreach($locations as $location)
                            <option value="{{ $location }}" {{ old('location', $menu->location) == $location ? 'selected' : '' }}>
                                {{ ucfirst($location) }}
                            </option>
                        @endforeach
                    </select>
                    @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label for="order" class="form-label">Order <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $menu->order) }}" min="0" required>
                    @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label for="parent_id" class="form-label">Parent Menu Item</label>
                    <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                        <option value="">None (Top Level)</option>
                        @foreach($menuItems as $item)
                            <option value="{{ $item->id }}" {{ old('parent_id', $menu->parent_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Associated Category</label>
                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                    <option value="">None</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $menu->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Link this menu item to a category (optional)</small>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="active" name="active" value="1" {{ old('active', $menu->active) ? 'checked' : '' }}>
                <label class="form-check-label" for="active">Active</label>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_dynamic_page" name="is_dynamic_page" value="1" {{ old('is_dynamic_page', $menu->is_dynamic_page) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_dynamic_page">Is Dynamic Page</label>
                <small class="form-text text-muted d-block">Check this if you want to create a custom page with content</small>
            </div>
            
            <div id="dynamic-page-fields" class="mb-3" style="display: none;">
                <div class="mb-3">
                    <label for="slug" class="form-label">Page Slug</label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $menu->slug) }}">
                    <small class="form-text text-muted">URL-friendly name for the page. Leave blank to generate from page name.</small>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">Page Content</label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="10">{{ old('content', $menu->content) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Menu Items
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Update Menu Item
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize TinyMCE
        tinymce.init({
            selector: '#content',
            height: 400,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            menubar: 'file edit view insert format tools table help'
        });
        
        // Show/hide dynamic page fields
        const isDynamicCheckbox = document.getElementById('is_dynamic_page');
        const dynamicFields = document.getElementById('dynamic-page-fields');
        
        function toggleDynamicFields() {
            if (isDynamicCheckbox && isDynamicCheckbox.checked) {
                dynamicFields.style.display = 'block';
            } else {
                dynamicFields.style.display = 'none';
            }
        }
        
        // Set initial state
        toggleDynamicFields();
        
        // Add event listener
        if (isDynamicCheckbox) {
            isDynamicCheckbox.addEventListener('change', toggleDynamicFields);
        }
        
        // Add hidden input to handle unchecked checkbox issues
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            // Handle is_dynamic_page checkbox
            if (!isDynamicCheckbox.checked) {
                const hiddenDynamicInput = document.createElement('input');
                hiddenDynamicInput.type = 'hidden';
                hiddenDynamicInput.name = 'is_dynamic_page';
                hiddenDynamicInput.value = '0';
                form.appendChild(hiddenDynamicInput);
            }
            
            // Handle active checkbox
            const activeCheckbox = document.getElementById('active');
            if (!activeCheckbox.checked) {
                const hiddenActiveInput = document.createElement('input');
                hiddenActiveInput.type = 'hidden';
                hiddenActiveInput.name = 'active';
                hiddenActiveInput.value = '0';
                form.appendChild(hiddenActiveInput);
            }
        });
        
        // Generate slug from name
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        
        if (nameInput && slugInput) {
            nameInput.addEventListener('blur', function() {
                if (slugInput.value === '' && isDynamicCheckbox && isDynamicCheckbox.checked) {
                    slugInput.value = nameInput.value
                        .toLowerCase()
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '');
                }
            });
        }
    });
</script>
@endsection 