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
                        <tr class="@if($item->parent_id) bg-light @endif">
                            <td>{{ $item->id }}</td>
                            <td>
                                @if($item->parent_id)
                                    <span class="ms-3">↳</span>
                                @endif
                                {{ $item->name }}
                                @if($item->children->count() > 0)
                                    <span class="badge bg-secondary">{{ $item->children->count() }} children</span>
                                @endif
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
                                </div>
                            </td>
                        </tr>
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
                        <tr class="@if($item->parent_id) bg-light @endif">
                            <td>{{ $item->id }}</td>
                            <td>
                                @if($item->parent_id)
                                    <span class="ms-3">↳</span>
                                @endif
                                {{ $item->name }}
                                @if($item->children->count() > 0)
                                    <span class="badge bg-secondary">{{ $item->children->count() }} children</span>
                                @endif
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
                                </div>
                            </td>
                        </tr>
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
</x-admin-form-modal>

<!-- Edit Menu Modal -->
<x-admin-form-modal 
    id="editMenuModal" 
    title="Edit Menu Item" 
    formId="editMenuForm" 
    formMethod="POST"
    submitButtonText="Update Menu Item">
    <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading menu item data...</p>
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
                    const editForm = document.getElementById('editMenuForm');
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