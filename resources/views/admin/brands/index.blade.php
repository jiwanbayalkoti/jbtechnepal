@extends('layouts.admin')

@section('title', 'Brands')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Brands</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="addBrandBtn">
                            <i class="fas fa-plus"></i> Add Brand
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Filter Form -->
                    <form id="filterForm" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by name or description...">
                            </div>
                            
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="sort" class="form-label">Sort By</label>
                                <div class="input-group">
                                    <select class="form-select" id="sort" name="sort">
                                        <option value="name" {{ request('sort', 'name') == 'name' ? 'selected' : '' }}>Name</option>
                                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date Created</option>
                                        <option value="updated_at" {{ request('sort') == 'updated_at' ? 'selected' : '' }}>Date Updated</option>
                                    </select>
                                    <select class="form-select" id="direction" name="direction">
                                        <option value="asc" {{ request('direction', 'asc') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-12 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter me-1"></i>Apply Filters
                                </button>
                                <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Active Filters Display -->
                    @if(request()->anyFilled(['search', 'status', 'sort', 'direction']))
                        <div class="d-flex flex-wrap gap-2 mb-3 active-filters">
                            <span class="fw-bold me-2">Active Filters:</span>
                            
                            @if(request('search'))
                                <span class="badge bg-info">
                                    Search: "{{ request('search') }}"
                                    <a href="{{ request()->fullUrlWithoutQuery(['search']) }}" class="text-white ms-1 text-decoration-none">×</a>
                                </span>
                            @endif
                            
                            @if(request('status'))
                                <span class="badge bg-primary">
                                    Status: {{ ucfirst(request('status')) }}
                                    <a href="{{ request()->fullUrlWithoutQuery(['status']) }}" class="text-white ms-1 text-decoration-none">×</a>
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
                        Showing {{ $brands->firstItem() ?? 0 }} to {{ $brands->lastItem() ?? 0 }} of {{ $brands->total() }} brands
                        @if(request()->anyFilled(['search', 'status']))
                            (filtered)
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Logo</th>
                                    <th>Name</th>
                                    <th>Website</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brands as $brand)
                                    <tr>
                                        <td>
                                            @if($brand->logo)
                                                <img src="{{ Storage::url($brand->logo) }}" alt="{{ $brand->name }} logo" class="img-thumbnail" style="max-height: 50px;">
                                            @else
                                                <span class="text-muted">No logo</span>
                                            @endif
                                        </td>
                                        <td>{{ $brand->name }}</td>
                                        <td>
                                            @if($brand->website)
                                                <a href="{{ $brand->website }}" target="_blank">{{ $brand->website }}</a>
                                            @else
                                                <span class="text-muted">No website</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $brand->is_active ? 'success' : 'danger' }}">
                                                {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-sm btn-info edit-brand" 
                                                    data-id="{{ $brand->id }}"
                                                    data-name="{{ $brand->name }}"
                                                    data-description="{{ $brand->description }}"
                                                    data-website="{{ $brand->website }}"
                                                    data-is-active="{{ $brand->is_active }}"
                                                    data-logo="{{ $brand->logo }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger delete-brand" 
                                                    data-id="{{ $brand->id }}"
                                                    data-name="{{ $brand->name }}">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No brands found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <nav aria-label="Brand pagination">
                            {{ $brands->withQueryString()->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Brand Form Modal -->
<div class="modal fade" id="brandModal" tabindex="-1" aria-labelledby="brandModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandModalLabel">Add Brand</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="brandForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="methodField"></div>

                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               required>
                        <div class="invalid-feedback" id="nameError"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3"></textarea>
                        <div class="invalid-feedback" id="descriptionError"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="website" class="form-label">Website</label>
                        <input type="url" 
                               class="form-control @error('website') is-invalid @enderror" 
                               id="website" 
                               name="website">
                        <div class="invalid-feedback" id="websiteError"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="logo" class="form-label">Logo</label>
                        <div id="currentLogo" class="mb-2 d-none">
                            <img src="" alt="Current logo" class="img-thumbnail" style="max-height: 100px;">
                        </div>
                        <input type="file" 
                               class="form-control @error('logo') is-invalid @enderror" 
                               id="logo" 
                               name="logo" 
                               accept="image/*">
                        <small class="form-text text-muted">Recommended size: 200x200px. Max file size: 2MB.</small>
                        <div class="invalid-feedback" id="logoError"></div>
                    </div>

                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1">
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveBrandBtn">Save Brand</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the brand "<span id="brandName"></span>"?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize modals
        const brandModal = new bootstrap.Modal(document.getElementById('brandModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        
        // Form elements
        const brandForm = document.getElementById('brandForm');
        const methodField = document.getElementById('methodField');
        const saveBrandBtn = document.getElementById('saveBrandBtn');
        const currentLogo = document.getElementById('currentLogo');
        const currentLogoImg = currentLogo.querySelector('img');
        
        // Add Brand button
        document.getElementById('addBrandBtn').addEventListener('click', function() {
            // Reset form
            brandForm.reset();
            methodField.innerHTML = '';
            currentLogo.classList.add('d-none');
            document.getElementById('brandModalLabel').textContent = 'Add Brand';
            brandModal.show();
        });
        
        // Edit Brand buttons
        document.querySelectorAll('.edit-brand').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const description = this.dataset.description;
                const website = this.dataset.website;
                const isActive = this.dataset.isActive === '1';
                const logo = this.dataset.logo;
                
                // Set form values
                document.getElementById('name').value = name;
                document.getElementById('description').value = description;
                document.getElementById('website').value = website;
                document.getElementById('is_active').checked = isActive;
                
                // Set method field for PUT request
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                
                // Set form action
                brandForm.action = `/admin/brands/${id}`;
                
                // Show current logo if exists
                if (logo) {
                    currentLogoImg.src = `/storage/${logo}`;
                    currentLogo.classList.remove('d-none');
                } else {
                    currentLogo.classList.add('d-none');
                }
                
                document.getElementById('brandModalLabel').textContent = 'Edit Brand';
                brandModal.show();
            });
        });
        
        // Delete Brand buttons
        document.querySelectorAll('.delete-brand').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                
                document.getElementById('brandName').textContent = name;
                document.getElementById('deleteForm').action = `/admin/brands/${id}`;
                
                deleteModal.show();
            });
        });
        
        // Save Brand button
        saveBrandBtn.addEventListener('click', function() {
            // Clear previous errors
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
            
            // Create FormData object
            const formData = new FormData(brandForm);
            
            // Determine if it's a create or update
            const isUpdate = formData.has('_method');
            const url = isUpdate ? brandForm.action : '{{ route("admin.brands.store") }}';
            
            // Send AJAX request
            fetch(url, {
                method: isUpdate ? 'POST' : 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to show updated data
                    window.location.reload();
                } else {
                    // Show validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const input = document.getElementById(key);
                            const errorDiv = document.getElementById(`${key}Error`);
                            
                            if (input && errorDiv) {
                                input.classList.add('is-invalid');
                                errorDiv.textContent = data.errors[key][0];
                            }
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });
</script>
@endsection 