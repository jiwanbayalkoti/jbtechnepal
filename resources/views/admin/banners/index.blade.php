@extends('layouts.admin')

@section('title', 'Manage Banners')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Manage Banners</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Banners</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-image me-1"></i>
                Banner List
            </div>
            <div>
                <a href="{{ route('admin.banners.demo') }}" class="btn btn-info btn-sm me-2">
                    <i class="fas fa-lightbulb me-1"></i> Demo & Usage Guide
                </a>
                <a href="{{ route('admin.banners.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Add New Banner
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="{{ route('admin.banners.index') }}" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search banners..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="btn-group">
                        <a href="{{ route('admin.banners.index') }}" class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
                        <a href="{{ route('admin.banners.index', ['status' => 'active']) }}" class="btn {{ request('status') == 'active' ? 'btn-primary' : 'btn-outline-primary' }}">Active</a>
                        <a href="{{ route('admin.banners.index', ['status' => 'inactive']) }}" class="btn {{ request('status') == 'inactive' ? 'btn-primary' : 'btn-outline-primary' }}">Inactive</a>
                    </div>
                </div>
            </div>
            
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
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 15%;">Image</th>
                            <th style="width: 20%;">Title</th>
                            <th style="width: 15%;">Link</th>
                            <th style="width: 10%;">Order</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 15%;">Created At</th>
                            <th style="width: 10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="banner-list">
                        @forelse($banners as $banner)
                            <tr data-id="{{ $banner->id }}">
                                <td>{{ $banner->id }}</td>
                                <td>
                                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" 
                                         class="img-thumbnail" style="max-height: 80px;">
                                </td>
                                <td>
                                    <strong>{{ $banner->title }}</strong>
                                    @if($banner->subtitle)
                                        <br><small class="text-muted">{{ $banner->subtitle }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($banner->link)
                                        <a href="{{ $banner->link }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 150px;">
                                            {{ $banner->link }}
                                        </a>
                                    @else
                                        <span class="text-muted">No link</span>
                                    @endif
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm banner-order" 
                                           data-id="{{ $banner->id }}" value="{{ $banner->order }}" min="0">
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input banner-status" type="checkbox" 
                                               data-id="{{ $banner->id }}"
                                               {{ $banner->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            {!! $banner->status_badge !!}
                                        </label>
                                    </div>
                                </td>
                                <td>{{ $banner->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.banners.show', $banner->id) }}" class="btn btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger delete-banner" data-id="{{ $banner->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                        <h5>No banners found</h5>
                                        @if(request('search') || request('status'))
                                            <p>Try clearing your filters or creating a new banner</p>
                                            <a href="{{ route('admin.banners.index') }}" class="btn btn-sm btn-outline-secondary mt-2">Clear Filters</a>
                                        @else
                                            <p>Start by creating your first banner</p>
                                            <a href="{{ route('admin.banners.create') }}" class="btn btn-sm btn-primary mt-2">
                                                <i class="fas fa-plus me-1"></i> Create Banner
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    Showing {{ $banners->firstItem() ?? 0 }} to {{ $banners->lastItem() ?? 0 }} of {{ $banners->total() }} banners
                </div>
                <div>
                    {{ $banners->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteBannerModal" tabindex="-1" aria-labelledby="deleteBannerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteBannerModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this banner? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteBannerForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle delete banner click
        const deleteButtons = document.querySelectorAll('.delete-banner');
        const deleteBannerForm = document.getElementById('deleteBannerForm');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const bannerId = this.getAttribute('data-id');
                deleteBannerForm.action = `{{ route('admin.banners.destroy', '') }}/${bannerId}`;
                
                // Show the modal
                let deleteModal = new bootstrap.Modal(document.getElementById('deleteBannerModal'));
                deleteModal.show();
            });
        });
        
        // Handle banner status toggle
        const statusToggles = document.querySelectorAll('.banner-status');
        
        statusToggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const bannerId = this.getAttribute('data-id');
                const isActive = this.checked;
                
                // Send AJAX request to update status
                fetch(`{{ route('admin.banners.toggle-status', '') }}/${bannerId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ is_active: isActive })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update status badge
                        const label = this.nextElementSibling;
                        label.innerHTML = data.status_badge;
                        
                        // Show success message
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                        alertDiv.setAttribute('role', 'alert');
                        alertDiv.innerHTML = `
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        document.body.appendChild(alertDiv);
                        
                        // Auto-dismiss after 3 seconds
                        setTimeout(() => {
                            alertDiv.remove();
                        }, 3000);
                    } else {
                        console.error('Error updating banner status');
                        this.checked = !isActive; // Revert the toggle
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.checked = !isActive; // Revert the toggle
                });
            });
        });
        
        // Handle banner order update
        let updateOrderTimeout;
        const orderInputs = document.querySelectorAll('.banner-order');
        
        orderInputs.forEach(input => {
            input.addEventListener('change', function() {
                const bannerId = this.getAttribute('data-id');
                const newOrder = this.value;
                
                clearTimeout(updateOrderTimeout);
                
                updateOrderTimeout = setTimeout(() => {
                    // Collect all banner IDs and their order values
                    const bannerOrders = {};
                    document.querySelectorAll('.banner-order').forEach(inp => {
                        bannerOrders[inp.getAttribute('data-id')] = inp.value;
                    });
                    
                    // Send AJAX request to update orders
                    fetch(`{{ route('admin.banners.update-order') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ orders: bannerOrders })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                            alertDiv.setAttribute('role', 'alert');
                            alertDiv.innerHTML = `
                                ${data.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            document.body.appendChild(alertDiv);
                            
                            // Auto-dismiss after 3 seconds
                            setTimeout(() => {
                                alertDiv.remove();
                            }, 3000);
                        } else {
                            console.error('Error updating banner order');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }, 500);
            });
        });
    });
</script>
@endpush 