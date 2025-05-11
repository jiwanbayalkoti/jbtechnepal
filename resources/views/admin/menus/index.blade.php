@extends('layouts.admin')

@section('title', 'Menu Management')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <form action="{{ route('admin.menus.index') }}" method="GET" class="d-flex">
            <select name="location" class="form-select me-2" onchange="this.form.submit()">
                <option value="">All Locations</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc }}" {{ $location == $loc ? 'selected' : '' }}>{{ ucfirst($loc) }}</option>
                @endforeach
            </select>
            
            @if($location)
                <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary">Clear Filter</a>
            @endif
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Main Menu</h5>
        <button type="button" class="btn btn-primary" data-open-modal="createMenuModal">
            <i class="fas fa-plus me-1"></i>Add New Menu Item
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>URL/Route</th>
                        <th>Icon</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mainMenu as $item)
                        <tr class="parent-menu" data-menu-id="{{ $item->id }}">
                            <td>{{ $item->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($item->children->count() > 0)
                                        <button class="btn btn-sm btn-outline-secondary me-2 toggle-children" data-menu-id="{{ $item->id }}">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    @else
                                        <span class="ms-4"></span>
                                    @endif
                                    <span>{{ $item->name }}</span>
                                    @if($item->children->count() > 0)
                                        <span class="badge bg-secondary ms-2">{{ $item->children->count() }} children</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($item->url)
                                    <span class="badge bg-info">URL: {{ $item->url }}</span>
                                @else
                                    <span class="badge bg-primary">Route: {{ $item->route_name }}</span>
                                @endif
                            </td>
                            <td>
                                @if($item->icon)
                                    <i class="{{ $item->icon }}"></i> {{ $item->icon }}
                                @else
                                    <span class="text-muted">No icon</span>
                                @endif
                            </td>
                            <td>{{ $item->order }}</td>
                            <td>
                                @if($item->active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            data-edit-url="{{ route('admin.menus.edit', ['menuItem' => $item->id]) }}"
                                            data-open-modal="editMenuModal">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.menus.destroy', ['menuItem' => $item->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" data-delete-confirm="Are you sure you want to delete this menu item?">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-success" 
                                            data-open-modal="createMenuModal" 
                                            onclick="document.getElementById('parent_id').value = '{{ $item->id }}'; document.getElementById('location').value = '{{ $item->location }}';">
                                        <i class="fas fa-plus"></i> Add Child
                                    </button>
                                </div>
                            </td>
                        </tr>
                        
                        @foreach($item->children as $child)
                            <tr class="child-menu child-of-{{ $item->id }} bg-light" style="display: none;">
                                <td>{{ $child->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center ps-4 ms-4 border-start border-2 border-secondary">
                                        @if($child->children && $child->children->count() > 0)
                                            <button class="btn btn-sm btn-outline-secondary me-2 toggle-children" data-menu-id="{{ $child->id }}">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        @else
                                        <i class="fas fa-level-down-alt text-secondary me-2"></i>
                                        @endif
                                        {{ $child->name }}
                                        @if($child->children && $child->children->count() > 0)
                                            <span class="badge bg-secondary ms-2">{{ $child->children->count() }} children</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($child->url)
                                        <span class="badge bg-info">URL: {{ $child->url }}</span>
                                    @else
                                        <span class="badge bg-primary">Route: {{ $child->route_name }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($child->icon)
                                        <i class="{{ $child->icon }}"></i> {{ $child->icon }}
                                    @else
                                        <span class="text-muted">No icon</span>
                                    @endif
                                </td>
                                <td>{{ $child->order }}</td>
                                <td>
                                    @if($child->active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                data-edit-url="{{ route('admin.menus.edit', ['menuItem' => $child->id]) }}"
                                                data-open-modal="editMenuModal">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form action="{{ route('admin.menus.destroy', ['menuItem' => $child->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" data-delete-confirm="Are you sure you want to delete this menu item?">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-success" 
                                                data-open-modal="createMenuModal" 
                                                onclick="document.getElementById('parent_id').value = '{{ $child->id }}'; document.getElementById('location').value = '{{ $child->location }}';">
                                            <i class="fas fa-plus"></i> Add Child
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Render grandchildren if any -->
                            @if($child->children && $child->children->count() > 0)
                                @foreach($child->children as $grandchild)
                                    <tr class="child-menu child-of-{{ $child->id }} grandchild-menu bg-light-subtle" style="display: none;">
                                        <td>{{ $grandchild->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center ps-5 ms-5 border-start border-2 border-info">
                                                <i class="fas fa-long-arrow-alt-right text-info me-2"></i>
                                                {{ $grandchild->name }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($grandchild->url)
                                                <span class="badge bg-info">URL: {{ $grandchild->url }}</span>
                                            @else
                                                <span class="badge bg-primary">Route: {{ $grandchild->route_name }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($grandchild->icon)
                                                <i class="{{ $grandchild->icon }}"></i> {{ $grandchild->icon }}
                                            @else
                                                <span class="text-muted">No icon</span>
                                            @endif
                                        </td>
                                        <td>{{ $grandchild->order }}</td>
                                        <td>
                                            @if($grandchild->active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-primary" 
                                                        data-edit-url="{{ route('admin.menus.edit', ['menuItem' => $grandchild->id]) }}"
                                                        data-open-modal="editMenuModal">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <form action="{{ route('admin.menus.destroy', ['menuItem' => $grandchild->id]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" data-delete-confirm="Are you sure you want to delete this menu item?">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No main menu items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Footer Menu</h5>
        <button type="button" class="btn btn-primary" data-open-modal="createMenuModal" 
                onclick="document.getElementById('location').value = 'footer';">
            <i class="fas fa-plus me-1"></i>Add Footer Item
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>URL/Route</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($footerMenu as $item)
                        <tr class="parent-menu" data-menu-id="{{ $item->id }}">
                            <td>{{ $item->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($item->children->count() > 0)
                                        <button class="btn btn-sm btn-outline-secondary me-2 toggle-children" data-menu-id="{{ $item->id }}">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    @else
                                        <span class="ms-4"></span>
                                    @endif
                                    <span>{{ $item->name }}</span>
                                    @if($item->children->count() > 0)
                                        <span class="badge bg-secondary ms-2">{{ $item->children->count() }} children</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($item->url)
                                    <span class="badge bg-info">URL: {{ $item->url }}</span>
                                @else
                                    <span class="badge bg-primary">Route: {{ $item->route_name }}</span>
                                @endif
                            </td>
                            <td>{{ $item->order }}</td>
                            <td>
                                @if($item->active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            data-edit-url="{{ route('admin.menus.edit', ['menuItem' => $item->id]) }}"
                                            data-open-modal="editMenuModal">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.menus.destroy', ['menuItem' => $item->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" data-delete-confirm="Are you sure you want to delete this menu item?">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-success" 
                                            data-open-modal="createMenuModal" 
                                            onclick="document.getElementById('parent_id').value = '{{ $item->id }}'; document.getElementById('location').value = '{{ $item->location }}';">
                                        <i class="fas fa-plus"></i> Add Child
                                    </button>
                                </div>
                            </td>
                        </tr>
                        
                        @foreach($item->children as $child)
                            <tr class="child-menu child-of-{{ $item->id }} bg-light" style="display: none;">
                                <td>{{ $child->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center ps-4 ms-4 border-start border-2 border-secondary">
                                        @if($child->children && $child->children->count() > 0)
                                            <button class="btn btn-sm btn-outline-secondary me-2 toggle-children" data-menu-id="{{ $child->id }}">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        @else
                                        <i class="fas fa-level-down-alt text-secondary me-2"></i>
                                        @endif
                                        {{ $child->name }}
                                        @if($child->children && $child->children->count() > 0)
                                            <span class="badge bg-secondary ms-2">{{ $child->children->count() }} children</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($child->url)
                                        <span class="badge bg-info">URL: {{ $child->url }}</span>
                                    @else
                                        <span class="badge bg-primary">Route: {{ $child->route_name }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($child->icon)
                                        <i class="{{ $child->icon }}"></i> {{ $child->icon }}
                                    @else
                                        <span class="text-muted">No icon</span>
                                    @endif
                                </td>
                                <td>{{ $child->order }}</td>
                                <td>
                                    @if($child->active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                data-edit-url="{{ route('admin.menus.edit', ['menuItem' => $child->id]) }}"
                                                data-open-modal="editMenuModal">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form action="{{ route('admin.menus.destroy', ['menuItem' => $child->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" data-delete-confirm="Are you sure you want to delete this menu item?">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-success" 
                                                data-open-modal="createMenuModal" 
                                                onclick="document.getElementById('parent_id').value = '{{ $child->id }}'; document.getElementById('location').value = '{{ $child->location }}';">
                                            <i class="fas fa-plus"></i> Add Child
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Render grandchildren if any -->
                            @if($child->children && $child->children->count() > 0)
                                @foreach($child->children as $grandchild)
                                    <tr class="child-menu child-of-{{ $child->id }} grandchild-menu bg-light-subtle" style="display: none;">
                                        <td>{{ $grandchild->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center ps-5 ms-5 border-start border-2 border-info">
                                                <i class="fas fa-long-arrow-alt-right text-info me-2"></i>
                                                {{ $grandchild->name }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($grandchild->url)
                                                <span class="badge bg-info">URL: {{ $grandchild->url }}</span>
                                            @else
                                                <span class="badge bg-primary">Route: {{ $grandchild->route_name }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($grandchild->icon)
                                                <i class="{{ $grandchild->icon }}"></i> {{ $grandchild->icon }}
                                            @else
                                                <span class="text-muted">No icon</span>
                                            @endif
                                        </td>
                                        <td>{{ $grandchild->order }}</td>
                                        <td>
                                            @if($grandchild->active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-primary" 
                                                        data-edit-url="{{ route('admin.menus.edit', ['menuItem' => $grandchild->id]) }}"
                                                        data-open-modal="editMenuModal">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <form action="{{ route('admin.menus.destroy', ['menuItem' => $grandchild->id]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" data-delete-confirm="Are you sure you want to delete this menu item?">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No footer menu items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Menu Modal -->
<x-admin-form-modal 
    id="createMenuModal" 
    title="Create Menu Item" 
    formId="createMenuForm" 
    formAction="{{ route('admin.menus.store') }}" 
    formMethod="POST"
    submitButtonText="Save Menu Item">

    <input type="hidden" id="parent_id" name="parent_id" value="{{ old('parent_id') }}">

    <div class="mb-3">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="url" class="form-label">URL</label>
            <input type="text" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url') }}">
            <small class="form-text text-muted">External or internal URL (e.g., /contact, https://example.com)</small>
            @error('url')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-md-6">
            <label for="route_name" class="form-label">Route Name</label>
            <input type="text" class="form-control @error('route_name') is-invalid @enderror" id="route_name" name="route_name" value="{{ old('route_name') }}">
            <small class="form-text text-muted">Laravel route name (e.g., home, contact.index)</small>
            @error('route_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-text text-danger mt-2">Either URL or Route Name must be provided.</div>
    </div>
    
    <div class="mb-3">
        <label for="icon" class="form-label">Icon Class (FontAwesome)</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-icons"></i></span>
            <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon') }}" placeholder="e.g. fas fa-home">
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
                <option value="main" {{ old('location') == 'main' ? 'selected' : '' }}>Main Navigation</option>
                <option value="footer" {{ old('location') == 'footer' ? 'selected' : '' }}>Footer</option>
                <option value="footer_admin" {{ old('location') == 'footer_admin' ? 'selected' : '' }}>Footer Admin</option>
            </select>
            @error('location')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-md-4">
            <label for="order" class="form-label">Order <span class="text-danger">*</span></label>
            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', 0) }}" min="0" required>
            @error('order')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-md-4">
            <label for="parent_id" class="form-label">Parent Menu Item</label>
            <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                <option value="">None (Top Level)</option>
                @foreach($parentMenuItems as $menuItem)
                    <option value="{{ $menuItem->id }}" {{ old('parent_id') == $menuItem->id ? 'selected' : '' }}>
                        {{ $menuItem->name }} ({{ $menuItem->location }})
                    </option>
                @endforeach
            </select>
            @error('parent_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="form-check">
        <input type="checkbox" class="form-check-input" id="active" name="active" value="1" {{ old('active', '1') ? 'checked' : '' }}>
        <label class="form-check-label" for="active">Active</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="is_mega_menu" name="is_mega_menu" value="1" {{ old('is_mega_menu') ? 'checked' : '' }}>
                <label class="form-check-label" for="is_mega_menu">Mega Menu</label>
            </div>
        </div>
    </div>
    
    <div class="mb-3">
        <label for="category_id" class="form-label">Related Category (Optional)</label>
        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
            <option value="">None</option>
            @foreach(\App\Models\Category::orderBy('name')->get() as $category)
                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <small class="form-text text-muted">For mega menu "View All" links - associates this menu with a specific category</small>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</x-admin-form-modal>

<!-- Edit Menu Modal -->
<div class="modal fade" id="editMenuModal" tabindex="-1" aria-labelledby="editMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMenuModalLabel">Edit Menu Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <div class="modal-body">
                <!-- Content will be loaded dynamically -->
                <div class="text-center">
                    <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading menu data...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" form="editMenuForm">Update Menu Item</button>
                </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle menu children toggling
    const toggleButtons = document.querySelectorAll('.toggle-children');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const menuId = this.getAttribute('data-menu-id');
            const childRows = document.querySelectorAll('.child-of-' + menuId);
            const icon = this.querySelector('i');
            
            let isVisible = false;
            
            childRows.forEach(row => {
                if (row.style.display === 'none' || row.style.display === '') {
                    row.style.display = 'table-row';
                    isVisible = true;
                } else {
                    row.style.display = 'none';
                    isVisible = false;
                    
                    // Also hide any grandchildren
                    if (row.classList.contains('child-menu') && !row.classList.contains('grandchild-menu')) {
                        const childId = row.querySelector('td:first-child')?.textContent?.trim();
                        if (childId) {
                            const grandchildRows = document.querySelectorAll('.child-of-' + childId);
                            
                            grandchildRows.forEach(grandchildRow => {
                                grandchildRow.style.display = 'none';
                            });
                            
                            // Reset the child row's toggle button icon
                            const childToggleBtn = row.querySelector('.toggle-children i');
                            if (childToggleBtn) {
                                childToggleBtn.className = 'fas fa-chevron-down';
                            }
                        }
                    }
                }
            });
            
            // Update the icon
            if (icon) {
                icon.className = isVisible ? 'fas fa-chevron-up' : 'fas fa-chevron-down';
            }
        });
    });
    
    // Handle delete confirmations
    const deleteButtons = document.querySelectorAll('[data-delete-confirm]');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const confirmMessage = this.getAttribute('data-delete-confirm');
            
            if (confirm(confirmMessage)) {
                const form = this.closest('form');
                if (form) {
                form.submit();
                }
            }
        });
    });
    
    // Handle modal opening
    document.querySelectorAll('[data-open-modal]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-open-modal');
            const modal = document.getElementById(modalId);
            
            if (!modal) {
                console.error(`Modal with ID "${modalId}" not found`);
                return;
            }
            
            if (modalId === 'editMenuModal') {
                const editUrl = this.getAttribute('data-edit-url');
                if (editUrl) {
                    // Set the form action with the menu ID
                    const menuId = editUrl.split('/').pop();
                    
                    // Extract numeric ID from the URL pattern
                    const numericId = editUrl.match(/\/edit\/(\d+)/)?.[1] || editUrl.match(/\/(\d+)\/edit/)?.[1] || menuId;
                    
                    // Store the menu ID for later use
                    window.currentEditMenuId = numericId;
                    
                    // Show loading state
                    const formContent = modal.querySelector('.modal-body');
                    if (formContent) {
                        formContent.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading menu data...</p></div>';
                    
                        // Fetch menu data
                        fetch(editUrl, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Response data:', data);
                            if (data.success && data.html) {
                                // Update form content
                                if (formContent) {
                                    formContent.innerHTML = data.html;
                                    
                                    // Make sure the form action is set correctly
                const editForm = document.getElementById('editMenuForm');
                                    if (editForm) {
                                        editForm.action = editForm.action.replace(':id', numericId);
                                    }
                                    
                                    // Initialize form behaviors after it's loaded
                                    initializeEditFormBehaviors();
                                }
                            } else {
                                throw new Error(data.message || 'Error loading menu data');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            if (formContent) {
                                formContent.innerHTML = `<div class="alert alert-danger">Error loading menu data: ${error.message}</div>`;
                            }
                        });
                    }
                }
            }
            
            // Show the modal
            try {
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            } catch (error) {
                console.error('Error showing modal:', error);
            }
        });
    });

    // Initialize behaviors for the edit form after it's loaded dynamically
    function initializeEditFormBehaviors() {
        try {
            // Handle dynamic page fields toggle
            const isDynamicCheckbox = document.getElementById('is_dynamic_page');
            const dynamicFields = document.getElementById('dynamic-page-fields');
            const brandSection = document.getElementById('brand_section');
            const autoGenerateSection = document.getElementById('auto_generate_section');
            
            if (isDynamicCheckbox && dynamicFields) {
                isDynamicCheckbox.addEventListener('change', function() {
                    dynamicFields.style.display = this.checked ? 'block' : 'none';
                    if (brandSection) brandSection.style.display = this.checked ? 'none' : 'block';
                    if (autoGenerateSection) autoGenerateSection.style.display = this.checked ? 'none' : 'block';
                });
                
                // Initialize visibility on load
                if (isDynamicCheckbox.checked) {
                    dynamicFields.style.display = 'block';
                    if (brandSection) brandSection.style.display = 'none';
                    if (autoGenerateSection) autoGenerateSection.style.display = 'none';
                } else {
                    dynamicFields.style.display = 'none';
                    if (brandSection) brandSection.style.display = 'block';
                    if (autoGenerateSection) autoGenerateSection.style.display = 'block';
                }
            }
            
            // Handle parent menu selection affecting location
            const parentSelect = document.getElementById('parent_id');
            if (parentSelect) {
                parentSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.value) {
                        const locationText = selectedOption.textContent.match(/\((.*?)\)/);
                        if (locationText && locationText[1]) {
                            const location = locationText[1];
                            const locationSelect = document.getElementById('location');
                            if (locationSelect) {
                                locationSelect.value = location;
                            }
                        }
                    }
                });
            }
        } catch (error) {
            console.error('Error initializing form behaviors:', error);
        }
    }

    // Handle form submission - using event delegation for dynamically loaded content
    document.addEventListener('submit', function(e) {
        if (e.target && e.target.id === 'editMenuForm') {
            e.preventDefault();
            
            try {
                // Show processing state
                const form = e.target;
                const submitButton = form.querySelector('button[type="submit"]') || 
                                    document.querySelector('button[form="editMenuForm"]');
                
                let originalButtonText = 'Update Menu Item';
                if (submitButton) {
                    originalButtonText = submitButton.innerHTML;
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
                }
                
                // Get the menu ID
                const menuId = window.currentEditMenuId || form.action.split('/').pop();
                if (!menuId) {
                    throw new Error('Menu ID not found');
                }
                
                // Capture all form data before any DOM manipulation
                const formData = new FormData(form);
                
                // Make sure boolean values are properly represented
                // If checkboxes aren't checked, they won't be included in the FormData
                if (!formData.has('active')) {
                    formData.append('active', '0');
                }
                
                if (!formData.has('is_mega_menu')) {
                    formData.append('is_mega_menu', '0');
                }
                
                if (!formData.has('is_dynamic_page')) {
                    formData.append('is_dynamic_page', '0');
                }
                
                if (!formData.has('auto_generate_models')) {
                    formData.append('auto_generate_models', '0');
                }
                
                // Log form data for debugging
                const formDataObj = {};
                formData.forEach((value, key) => {
                    formDataObj[key] = value;
                    // Add console logging for specific fields of interest
                    if (key === 'url' || key === 'route_name') {
                        console.log(`Form data - ${key}: "${value}"`);
                    }
                });
                console.log('Form data being submitted:', formDataObj);
                console.log('Menu ID for update:', menuId);
                
                // Get base URL from the current page
                const baseUrl = window.location.pathname.split('/menus')[0];
                
                // Submit form via fetch API using menuItem parameter
                const updateUrl = `${baseUrl}/menus/${menuId}/debug-update`;
                console.log('Submitting to URL:', updateUrl);
                
                // Make sure form data has the method set correctly
                formData.append('_method', 'POST'); // Ensure we're using POST method
                
                // Explicitly set URL field even if it's empty to ensure it's included in the request
                if (!formData.has('url')) {
                    formData.append('url', '');
                    console.log('Added empty URL field to ensure it\'s included');
                }
                
                // Explicitly set route_name field even if it's empty to ensure it's included
                if (!formData.has('route_name')) {
                    formData.append('route_name', '');
                    console.log('Added empty route_name field to ensure it\'s included');
                }
                
                fetch(updateUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || `HTTP error: ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Server response:', data);
                    
                    // Reset button state
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonText;
                    }
                    
                    if (data.success) {
                        // Show success message
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success alert-dismissible fade show mt-3';
                        alertDiv.innerHTML = `
                            <strong>Success!</strong> ${data.message || 'Menu item updated successfully.'}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        form.insertAdjacentElement('beforebegin', alertDiv);
                        
                        // Reload the page after a short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        throw new Error(data.message || 'Unknown error occurred');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    // Reset button state
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonText;
                    }
                    
                    // Show error message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger alert-dismissible fade show mt-3';
                    alertDiv.innerHTML = `
                        <strong>Error!</strong> ${error.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    form.insertAdjacentElement('beforebegin', alertDiv);
                });
            } catch (error) {
                console.error('Error handling form submission:', error);
                alert('Error submitting form: ' + error.message);
            }
        }
    });
});
</script>
@endpush 