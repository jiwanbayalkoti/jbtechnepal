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
                                            data-edit-url="{{ route('admin.menus.edit', $item->id) }}"
                                            data-open-modal="editMenuModal">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.menus.destroy', $item->id) }}" method="POST" class="d-inline">
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
                                                data-edit-url="{{ route('admin.menus.edit', $child->id) }}"
                                                data-open-modal="editMenuModal">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form action="{{ route('admin.menus.destroy', $child->id) }}" method="POST" class="d-inline">
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
                                                        data-edit-url="{{ route('admin.menus.edit', $grandchild->id) }}"
                                                        data-open-modal="editMenuModal">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <form action="{{ route('admin.menus.destroy', $grandchild->id) }}" method="POST" class="d-inline">
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
                                            data-edit-url="{{ route('admin.menus.edit', $item->id) }}"
                                            data-open-modal="editMenuModal">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.menus.destroy', $item->id) }}" method="POST" class="d-inline">
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
                                                data-edit-url="{{ route('admin.menus.edit', $child->id) }}"
                                                data-open-modal="editMenuModal">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form action="{{ route('admin.menus.destroy', $child->id) }}" method="POST" class="d-inline">
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
                                                        data-edit-url="{{ route('admin.menus.edit', $grandchild->id) }}"
                                                        data-open-modal="editMenuModal">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <form action="{{ route('admin.menus.destroy', $grandchild->id) }}" method="POST" class="d-inline">
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
    
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="active" name="active" value="1" {{ old('active', '1') ? 'checked' : '' }}>
        <label class="form-check-label" for="active">Active</label>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMenuModalLabel">Edit Menu Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form content will be loaded here via AJAX -->
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading form...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="saveEditButton" class="btn btn-primary">Save Changes</button>
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
                            const childId = row.querySelector('td:first-child').textContent.trim();
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
                });
                
                // Update the icon
                if (isVisible) {
                    icon.className = 'fas fa-chevron-up';
                } else {
                    icon.className = 'fas fa-chevron-down';
                }
            });
        });
        
        // Handle delete confirmations
        const deleteButtons = document.querySelectorAll('[data-delete-confirm]');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const confirmMessage = this.getAttribute('data-delete-confirm');
                
                if (confirm(confirmMessage)) {
                    this.closest('form').submit();
                }
            });
        });
        
        // Handle modal opening
        const modalTriggers = document.querySelectorAll('[data-open-modal]');
        let currentMenuId = null;
        
        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', function() {
                const modalId = this.getAttribute('data-open-modal');
                const modal = document.getElementById(modalId);
                
                if (modal) {
                    const editUrl = this.getAttribute('data-edit-url');
                    
                    if (editUrl && modalId === 'editMenuModal') {
                        // Extract the menu ID from the URL
                        const matches = editUrl.match(/\/(\d+)\/edit$/);
                        if (matches && matches[1]) {
                            currentMenuId = matches[1];
                        }
                        
                        // Load edit form via AJAX
                        fetch(editUrl)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    const formContainer = modal.querySelector('.modal-body');
                                    formContainer.innerHTML = data.html;
                                    
                                    // Initialize form scripts
                                    const scripts = formContainer.querySelectorAll('script');
                                    scripts.forEach(script => {
                                        eval(script.innerText);
                                    });
                                    
                                    // Show the modal
                                    const bsModal = new bootstrap.Modal(modal);
                                    bsModal.show();
                                } else {
                                    alert('Error loading form: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Error loading form. Please try again.');
                            });
                    } else {
                        // Just show the modal
                        const bsModal = new bootstrap.Modal(modal);
                        bsModal.show();
                    }
                }
            });
        });
        
        // Handle save edit button click
        document.getElementById('saveEditButton').addEventListener('click', function() {
            console.log("Save button clicked");
            
            if (!currentMenuId) {
                console.error("Error: No menu ID found", currentMenuId);
                alert('Error: No menu ID found');
                return;
            }
            
            console.log("Current menu ID:", currentMenuId);
            
            // Debug info
            if (!document.getElementById('debug-info')) {
                const debugDiv = document.createElement('div');
                debugDiv.id = 'debug-info';
                debugDiv.style.padding = '10px';
                debugDiv.style.backgroundColor = '#f8f9fa';
                debugDiv.style.border = '1px solid #dee2e6';
                debugDiv.style.marginBottom = '10px';
                debugDiv.innerHTML = `
                    <h5>Debug Information</h5>
                    <p>Menu ID: ${currentMenuId}</p>
                    <p>Form URL: {{ url('/admin/menus') }}/${currentMenuId}</p>
                `;
                document.querySelector('#editMenuModal .modal-body').prepend(debugDiv);
            }
            
            // Get the form from the modal body
            const formElement = document.querySelector('#formContent');
            if (!formElement) {
                console.error("Error: Form content not found");
                alert('Error: Form content not found');
                return;
            }
            
            // Create a FormData object from all form inputs in the modal
            const formData = new FormData();
            const inputs = document.querySelectorAll('#editMenuModal input, #editMenuModal select, #editMenuModal textarea');
            
            console.log("Found inputs:", inputs.length);
            
            inputs.forEach(input => {
                // Skip hidden inputs that might cause issues
                if (input.type === 'hidden' && (input.name === '_method' || input.name === '_token')) {
                    console.log("Skipping existing method or token field:", input.name);
                    return;
                }
                
                // Handle checkboxes
                if (input.type === 'checkbox') {
                    if (input.checked) {
                        formData.append(input.name, input.value);
                        console.log("Adding checkbox:", input.name, input.value);
                    } else {
                        // Send unchecked checkboxes as 0
                        if (input.name) {
                            formData.append(input.name, '0');
                            console.log("Adding unchecked checkbox:", input.name, '0');
                        }
                    }
                } else if (input.name) {
                    formData.append(input.name, input.value);
                    console.log("Adding field:", input.name, input.value);
                }
            });
            
            // Add method-spoofing field for PUT
            formData.append('_method', 'PUT');
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            formData.append('_token', csrfToken);
            
            console.log("CSRF token:", csrfToken);
            console.log("Submitting to URL:", `{{ url('/admin/menus') }}/${currentMenuId}`);
            
            // Create a temporary form and submit it instead of using fetch
            const tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.action = `{{ url('/admin/menus') }}/${currentMenuId}`;
            tempForm.style.display = 'none';
            
            // Add method-spoofing field
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            tempForm.appendChild(methodInput);
            
            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            tempForm.appendChild(csrfInput);
            
            // Add all other form data
            inputs.forEach(input => {
                if (input.type === 'checkbox') {
                    if (input.checked) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = input.name;
                        hiddenInput.value = input.value;
                        tempForm.appendChild(hiddenInput);
                    } else {
                        // Send unchecked checkboxes as 0
                        if (input.name) {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = input.name;
                            hiddenInput.value = '0';
                            tempForm.appendChild(hiddenInput);
                        }
                    }
                } else if (input.name && input.name !== '_method' && input.name !== '_token') {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = input.name;
                    hiddenInput.value = input.value;
                    tempForm.appendChild(hiddenInput);
                }
            });
            
            // Append the form to the body and submit it
            document.body.appendChild(tempForm);
            tempForm.submit();
        });
    });
</script>
@endpush 