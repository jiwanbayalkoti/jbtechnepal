@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total Products</h5>
                        <h2 class="mb-0">{{ $totalProducts }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-laptop fa-3x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a href="{{ route('admin.products.index') }}" class="text-white">View Details</a>
                <i class="fas fa-angle-right text-white"></i>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total Categories</h5>
                        <h2 class="mb-0">{{ $totalCategories }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-tags fa-3x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a href="{{ route('admin.categories.index') }}" class="text-white">View Details</a>
                <i class="fas fa-angle-right text-white"></i>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Compare System</h5>
                        <p class="mb-0">Product Comparison</p>
                    </div>
                    <div>
                        <i class="fas fa-balance-scale fa-3x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a href="{{ route('home') }}" class="text-white" target="_blank">View Website</a>
                <i class="fas fa-angle-right text-white"></i>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total Customers</h5>
                        <h2 class="mb-0">{{ $totalCustomers }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a href="{{ route('admin.customers.index') }}" class="text-white">View Details</a>
                <i class="fas fa-angle-right text-white"></i>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Recent Products</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentProducts as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name }}</td>
                            <td>${{ number_format($product->price, 2) }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" 
                                       data-edit-url="{{ route('admin.products.edit', ['product' => $product->id]) }}"
                                       data-open-modal="editProductModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No products added yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-primary" data-open-modal="createProductModal">
            <i class="fas fa-plus me-1"></i>Add New Product
        </button>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Categories Management</h5>
                <button type="button" class="btn btn-sm btn-primary" data-open-modal="createCategoryModal">
                    <i class="fas fa-plus me-1"></i>Add New
                </button>
            </div>
            <div class="card-body">
                <p>Manage product categories and their specification types.</p>
                <ul>
                    <li>Create, edit, and delete categories</li>
                    <li>Manage specification types for each category</li>
                    <li>Organize products by categories</li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-primary">
                    Manage Categories
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Products Management</h5>
                <button type="button" class="btn btn-sm btn-primary" data-open-modal="createProductModal">
                    <i class="fas fa-plus me-1"></i>Add New
                </button>
            </div>
            <div class="card-body">
                <p>Manage products and their specifications.</p>
                <ul>
                    <li>Add new products with detailed specifications</li>
                    <li>Upload product images</li>
                    <li>Edit and delete existing products</li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary">
                    Manage Products
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Menu Management</h5>
                <button type="button" class="btn btn-sm btn-primary" data-open-modal="createMenuModal">
                    <i class="fas fa-plus me-1"></i>Add New
                </button>
            </div>
            <div class="card-body">
                <p>Manage website navigation menus.</p>
                <ul>
                    <li>Create and organize menu items</li>
                    <li>Configure menu locations (main, footer)</li>
                    <li>Set up parent-child relationships for dropdown menus</li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-primary">
                    Manage Menus
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Settings</h5>
            </div>
            <div class="card-body">
                <p>Configure website settings.</p>
                <ul>
                    <li>Update site title and description</li>
                    <li>Configure contact information</li>
                    <li>Adjust system preferences</li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-primary">
                    Manage Settings
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">SEO Settings</h5>
                <button type="button" class="btn btn-sm btn-primary" data-open-modal="seoSettingsModal">
                    <i class="fas fa-cog me-1"></i>Configure
                </button>
            </div>
            <div class="card-body">
                <p>Configure website SEO settings.</p>
                <ul>
                    <li>Update site meta title and description</li>
                    <li>Configure meta keywords and robots</li>
                    <li>Set social media sharing information</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Contact Information</h5>
                <button type="button" class="btn btn-sm btn-primary" data-open-modal="contactSettingsModal">
                    <i class="fas fa-cog me-1"></i>Configure
                </button>
            </div>
            <div class="card-body">
                <p>Configure website contact information.</p>
                <ul>
                    <li>Update company contact details</li>
                    <li>Configure email addresses and phone numbers</li>
                    <li>Set social media links</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Documentation Row -->
<div class="row mt-4">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-purple text-white d-flex align-items-center">
                <div class="me-2">
                    <i class="fas fa-file-alt fa-lg"></i>
                </div>
                <h5 class="mb-0">System Documentation</h5>
            </div>
            <div class="card-body">
                <p>Access comprehensive system documentation for the Product Comparison System.</p>
                <ul>
                    <li>Detailed user role descriptions</li>
                    <li>User and admin use cases</li>
                    <li>System workflow diagrams</li>
                    <li>Component relationship explanations</li>
                </ul>
                <div class="mt-3">
                    <a href="{{ asset('docs/use_cases.html') }}" class="btn documentation-btn text-white" target="_blank">
                        <i class="fas fa-download me-2"></i>View Use Cases Documentation
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Product Categories Distribution</h5>
            </div>
            <div class="card-body">
                <div id="categoryChart" class="chart-container"></div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Products Price Range</h5>
            </div>
            <div class="card-body">
                <div id="priceRangeChart" class="chart-container"></div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Monthly Product Additions</h5>
            </div>
            <div class="card-body">
                <div id="monthlyCategoryProductChart" class="chart-container"></div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">System Health & Activity</h5>
            </div>
            <div class="card-body">
                <div id="systemHealthChart" class="chart-container"></div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Growth Chart -->
<div class="row mt-4">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Customer Growth (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="customerGrowthChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Customer Status Distribution -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Customer Status Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="customerStatusChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Customers Card -->
<div class="card mt-4 mb-4">
    <div class="card-header">
        <h5 class="mb-0">Recent Customers</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCustomers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>{{ $customer->full_name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>
                                <span class="badge {{ $customer->status_badge }} text-white">
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </td>
                            <td>{{ $customer->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No customers registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <a href="{{ route('admin.customers.index') }}" class="btn btn-primary">
            <i class="fas fa-users me-1"></i>Manage Customers
        </a>
    </div>
</div>

<!-- Product Modals -->
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
    
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="brand" class="form-label">Brand</label>
            <input type="text" class="form-control @error('brand') is-invalid @enderror" id="brand" name="brand" value="{{ old('brand') }}">
            @error('brand')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-md-6">
            <label for="model" class="form-label">Model</label>
            <input type="text" class="form-control @error('model') is-invalid @enderror" id="model" name="model" value="{{ old('model') }}">
            @error('model')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
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
        <label for="image" class="form-label">Product Image</label>
        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
        <small class="form-text text-muted">Upload a product image (JPEG, PNG, GIF, max 2MB)</small>
        @error('image')
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

<x-admin-form-modal 
    id="editProductModal" 
    title="Edit Product" 
    formId="editProductForm" 
    formMethod="POST"
    hasFiles="true"
    submitButtonText="Update Product">
    <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading product data...</p>
    </div>
</x-admin-form-modal>

<!-- Category Modals -->
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
</x-admin-form-modal>

<!-- Menu Modals -->
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

<!-- SEO Settings Modal -->
<x-admin-form-modal 
    id="seoSettingsModal" 
    title="SEO Settings" 
    formId="seoSettingsForm" 
    formAction="{{ route('admin.settings.seo.update') }}" 
    formMethod="POST"
    submitButtonText="Save SEO Settings">
    
    @csrf
    @method('PUT')
    
    <div class="mb-3">
        <label for="site_title" class="form-label">Site Title <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="site_title" name="site_title" value="{{ $seoSettings->site_title ?? old('site_title') }}" required>
        <small class="form-text text-muted">The title that appears in search engine results</small>
    </div>
    
    <div class="mb-3">
        <label for="meta_description" class="form-label">Meta Description</label>
        <textarea class="form-control" id="meta_description" name="meta_description" rows="3">{{ $seoSettings->meta_description ?? old('meta_description') }}</textarea>
        <small class="form-text text-muted">The description that appears in search engine results (150-160 characters recommended)</small>
    </div>
    
    <div class="mb-3">
        <label for="meta_keywords" class="form-label">Meta Keywords</label>
        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="{{ $seoSettings->meta_keywords ?? old('meta_keywords') }}">
        <small class="form-text text-muted">Comma-separated list of keywords (less important for modern SEO)</small>
    </div>
    
    <div class="mb-3">
        <label for="og_image" class="form-label">Social Media Image</label>
        <input type="file" class="form-control" id="og_image" name="og_image">
        <small class="form-text text-muted">Image displayed when shared on social media (min 1200×630 pixels recommended)</small>
    </div>
    
    @if(isset($seoSettings->og_image) && $seoSettings->og_image)
    <div class="mb-3">
        <label class="form-label">Current Social Media Image</label>
        <div>
            <img src="{{ asset('storage/' . $seoSettings->og_image) }}" alt="OG Image" class="img-thumbnail" style="max-height: 150px">
        </div>
    </div>
    @endif
    
    <div class="mb-3">
        <label for="google_analytics" class="form-label">Google Analytics Code</label>
        <textarea class="form-control" id="google_analytics" name="google_analytics" rows="3">{{ $seoSettings->google_analytics ?? old('google_analytics') }}</textarea>
        <small class="form-text text-muted">Paste your Google Analytics tracking code here</small>
    </div>
    
    <div class="mb-3">
        <label for="robots_txt" class="form-label">Robots.txt Content</label>
        <textarea class="form-control" id="robots_txt" name="robots_txt" rows="4">{{ $seoSettings->robots_txt ?? old('robots_txt', "User-agent: *\nAllow: /") }}</textarea>
        <small class="form-text text-muted">Content for your robots.txt file</small>
    </div>
    
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="robots_index" name="robots_index" value="1" {{ ($seoSettings->robots_index ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="robots_index">Allow search engines to index this site</label>
    </div>
</x-admin-form-modal>

<!-- Contact Settings Modal -->
<x-admin-form-modal 
    id="contactSettingsModal" 
    title="Contact Information" 
    formId="contactSettingsForm" 
    formAction="{{ route('admin.settings.contact.update') }}" 
    formMethod="POST"
    submitButtonText="Save Contact Settings">
    
    @csrf
    @method('PUT')
    
    <div class="mb-3">
        <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="company_name" name="company_name" value="{{ $contactSettings->company_name ?? old('company_name') }}" required>
    </div>
    
    <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <textarea class="form-control" id="address" name="address" rows="3">{{ $contactSettings->address ?? old('address') }}</textarea>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ $contactSettings->phone ?? old('phone') }}">
        </div>
        
        <div class="col-md-6">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $contactSettings->email ?? old('email') }}">
        </div>
    </div>
    
    <h5 class="mt-4 mb-3">Social Media Links</h5>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="facebook" class="form-label">
                <i class="fab fa-facebook text-primary"></i> Facebook
            </label>
            <input type="url" class="form-control" id="facebook" name="facebook" value="{{ $contactSettings->facebook ?? old('facebook') }}" placeholder="https://facebook.com/yourpage">
        </div>
        
        <div class="col-md-6">
            <label for="twitter" class="form-label">
                <i class="fab fa-twitter text-info"></i> Twitter
            </label>
            <input type="url" class="form-control" id="twitter" name="twitter" value="{{ $contactSettings->twitter ?? old('twitter') }}" placeholder="https://twitter.com/yourhandle">
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="instagram" class="form-label">
                <i class="fab fa-instagram text-danger"></i> Instagram
            </label>
            <input type="url" class="form-control" id="instagram" name="instagram" value="{{ $contactSettings->instagram ?? old('instagram') }}" placeholder="https://instagram.com/yourprofile">
        </div>
        
        <div class="col-md-6">
            <label for="youtube" class="form-label">
                <i class="fab fa-youtube text-danger"></i> YouTube
            </label>
            <input type="url" class="form-control" id="youtube" name="youtube" value="{{ $contactSettings->youtube ?? old('youtube') }}" placeholder="https://youtube.com/c/yourchannel">
        </div>
    </div>
    
    <div class="mb-3">
        <label for="google_maps_embed" class="form-label">Google Maps Embed Code</label>
        <textarea class="form-control" id="google_maps_embed" name="google_maps_embed" rows="3">{{ $contactSettings->google_maps_embed ?? old('google_maps_embed') }}</textarea>
        <small class="form-text text-muted">Paste your Google Maps embed code here</small>
    </div>
    
    <div class="mb-3">
        <label for="contact_form_recipients" class="form-label">Contact Form Recipients</label>
        <input type="text" class="form-control" id="contact_form_recipients" name="contact_form_recipients" value="{{ $contactSettings->contact_form_recipients ?? old('contact_form_recipients') }}" placeholder="email@example.com, another@example.com">
        <small class="form-text text-muted">Comma-separated list of email addresses to receive contact form submissions</small>
    </div>
</x-admin-form-modal>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Customer Growth Chart
        var customerGrowthCtx = document.getElementById('customerGrowthChart').getContext('2d');
        var customerGrowthChart = new Chart(customerGrowthCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($customerGrowth['labels']) !!},
                datasets: [{
                    label: 'New Customers',
                    data: {!! json_encode($customerGrowth['data']) !!},
                    backgroundColor: 'rgba(23, 162, 184, 0.2)',
                    borderColor: 'rgba(23, 162, 184, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(23, 162, 184, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(23, 162, 184, 1)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Customer Status Distribution Chart
        var customerStatusCtx = document.getElementById('customerStatusChart').getContext('2d');
        var customerStatusChart = new Chart(customerStatusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($customerStatusData['labels']) !!},
                datasets: [{
                    data: {!! json_encode($customerStatusData['data']) !!},
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(220, 53, 69, 0.7)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                },
                cutout: '70%'
            }
        });

        // Existing chart code...
    });
</script>
@endsection