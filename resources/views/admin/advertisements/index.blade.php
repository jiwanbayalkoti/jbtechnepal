@extends('layouts.admin')

@section('title', 'Advertisement Management')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Advertisement Management</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#createAdModal">
            <i class="fas fa-plus fa-sm text-white-50 mr-2"></i>Add New Advertisement
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Ads</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAds ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ad fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Ads</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeAds ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Expiring Soon</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expiringSoonAds ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Clicks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalClicks ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mouse-pointer fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Advertisements</h6>
        </div>
        <div class="card-body">
            <form id="adFilterForm" action="{{ route('admin.advertisements.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Search by title..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="position">Position</label>
                            <select class="form-control" id="position" name="position">
                                <option value="">All Positions</option>
                                <option value="homepage_top" {{ request('position') == 'homepage_top' ? 'selected' : '' }}>Homepage Top</option>
                                <option value="homepage_sidebar" {{ request('position') == 'homepage_sidebar' ? 'selected' : '' }}>Homepage Sidebar</option>
                                <option value="category_page" {{ request('position') == 'category_page' ? 'selected' : '' }}>Category Page</option>
                                <option value="product_page" {{ request('position') == 'product_page' ? 'selected' : '' }}>Product Page</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-search fa-sm"></i> Filter
                                </button>
                                <a href="{{ route('admin.advertisements.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-sync-alt fa-sm"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Advertisements Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Advertisements</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Export Options:</div>
                    <a class="dropdown-item" href="{{ route('admin.advertisements.export') }}">
                        <i class="fas fa-file-csv fa-sm fa-fw mr-2 text-gray-400"></i>Export to CSV
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th width="80">Image</th>
                            <th>Title</th>
                            <th width="120">Position</th>
                            <th width="100">Starts</th>
                            <th width="100">Ends</th>
                            <th width="80">Views</th>
                            <th width="80">Clicks</th>
                            <th width="80">Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($advertisements ?? [] as $ad)
                        <tr>
                            <td>{{ $ad->id }}</td>
                            <td>
                                @if($ad->image)
                                <img src="{{ asset('storage/' . $ad->image) }}" alt="{{ $ad->title }}" class="img-thumbnail" style="max-height: 50px;">
                                @else
                                <span class="text-muted">No image</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $ad->title }}</strong>
                                <div class="small text-truncate" style="max-width: 250px;">{{ $ad->url }}</div>
                            </td>
                            <td>
                                @php
                                    $positionLabels = [
                                        'homepage_top' => '<span class="badge badge-primary">Homepage Top</span>',
                                        'homepage_sidebar' => '<span class="badge badge-info">Homepage Sidebar</span>',
                                        'category_page' => '<span class="badge badge-success">Category Page</span>',
                                        'product_page' => '<span class="badge badge-warning">Product Page</span>'
                                    ];
                                @endphp
                                {!! $positionLabels[$ad->position] ?? '<span class="badge badge-secondary">Unknown</span>' !!}
                            </td>
                            <td>{{ $ad->start_date ? date('M d, Y', strtotime($ad->start_date)) : 'N/A' }}</td>
                            <td>{{ $ad->end_date ? date('M d, Y', strtotime($ad->end_date)) : 'Indefinite' }}</td>
                            <td>{{ number_format($ad->views ?? 0) }}</td>
                            <td>{{ number_format($ad->clicks ?? 0) }}</td>
                            <td>
                                @if(!$ad->is_active)
                                <span class="badge badge-danger">Inactive</span>
                                @elseif($ad->end_date && $ad->end_date < date('Y-m-d'))
                                <span class="badge badge-warning">Expired</span>
                                @elseif($ad->start_date > date('Y-m-d'))
                                <span class="badge badge-info">Scheduled</span>
                                @else
                                <span class="badge badge-success">Active</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info edit-ad-btn" data-id="{{ $ad->id }}" data-toggle="modal" data-target="#editAdModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="{{ route('admin.advertisements.show', $ad->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-danger delete-ad-btn" data-id="{{ $ad->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">No advertisements found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(isset($advertisements) && $advertisements->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $advertisements->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Advertisement Modal -->
<div class="modal fade" id="createAdModal" tabindex="-1" role="dialog" aria-labelledby="createAdModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAdModalLabel">Add New Advertisement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createAdForm" action="{{ route('admin.advertisements.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div id="createFormContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="mt-2">Loading form...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Advertisement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Advertisement Modal -->
<div class="modal fade" id="editAdModal" tabindex="-1" role="dialog" aria-labelledby="editAdModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAdModalLabel">Edit Advertisement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editAdForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div id="editFormContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="mt-2">Loading form...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Advertisement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Advertisement Modal -->
<div class="modal fade" id="deleteAdModal" tabindex="-1" role="dialog" aria-labelledby="deleteAdModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAdModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this advertisement? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteAdForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Advertisement</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize datepickers
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
        
        // Custom file input
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
        });
        
        // Load create form via AJAX
        $('#createAdModal').on('show.bs.modal', function() {
            $.ajax({
                url: "{{ route('admin.advertisements.create') }}",
                type: 'GET',
                success: function(data) {
                    $('#createFormContent').html(data);
                    // Reinitialize plugins after loading content
                    $('.datepicker').datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true
                    });
                    $('.custom-file-input').on('change', function() {
                        var fileName = $(this).val().split('\\').pop();
                        $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
                    });
                },
                error: function() {
                    $('#createFormContent').html('<div class="alert alert-danger">Error loading form. Please try again.</div>');
                }
            });
        });
        
        // Load edit form via AJAX
        $('.edit-ad-btn').on('click', function() {
            var adId = $(this).data('id');
            $('#editAdForm').attr('action', '/admin/advertisements/' + adId);
            
            $.ajax({
                url: "/admin/advertisements/" + adId + "/edit",
                type: 'GET',
                success: function(data) {
                    $('#editFormContent').html(data);
                    // Reinitialize plugins after loading content
                    $('.datepicker').datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true
                    });
                    $('.custom-file-input').on('change', function() {
                        var fileName = $(this).val().split('\\').pop();
                        $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
                    });
                },
                error: function() {
                    $('#editFormContent').html('<div class="alert alert-danger">Error loading form. Please try again.</div>');
                }
            });
        });
        
        // Delete advertisement
        $('.delete-ad-btn').on('click', function() {
            var adId = $(this).data('id');
            $('#deleteAdForm').attr('action', '/admin/advertisements/' + adId);
            $('#deleteAdModal').modal('show');
        });
    });
</script>
@endsection 