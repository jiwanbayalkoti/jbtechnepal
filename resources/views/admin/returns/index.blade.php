@extends('layouts.admin')

@section('title', 'Return Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Return Management</h1>
        <a href="{{ route('admin.returns.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Create New Return
        </a>
    </div>

    <!-- Return Stats Row -->
    <div class="row mb-4">
        <!-- Total Returns Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Returns</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['total'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-undo-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requested Returns Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Requested</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['requested'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Processing Returns Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Approved/Processing</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['approved'] + $statusCounts['processed'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Returns Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['completed'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.returns.index') }}" method="GET" id="filter-form">
                <div class="row">
                    <!-- Search Field -->
                    <div class="col-md-4 mb-3">
                        <label for="search">Search</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Return number, order #, customer..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Search by return #, order #, customer name, or email</small>
                    </div>
                    
                    <!-- Status Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" onchange="document.getElementById('filter-form').submit()">
                            <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Status</option>
                            <option value="requested" {{ request('status') == 'requested' ? 'selected' : '' }}>Requested</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                            <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    
                    <!-- Date Range Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="date_from">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label for="date_to">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>
                
                <div class="mt-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter mr-1"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.returns.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-sync-alt mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Returns Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Returns List</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Export Options:</div>
                    {{-- <a class="dropdown-item" href="{{ route('admin.returns.export', request()->all()) }}"><i class="fas fa-file-csv mr-2"></i>Export CSV</a> --}}
                    <a class="dropdown-item" href="#" onclick="printReturns()"><i class="fas fa-print mr-2"></i>Print</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="returns-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Return #</th>
                            <th>Date</th>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Refund Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($returns) > 0)
                            @foreach($returns as $return)
                                <tr>
                                    <td>{{ $return->return_number }}</td>
                                    <td>{{ $return->created_at->format('M d, Y') }}</td>
                                    <td>{{ $return->order->order_number }}</td>
                                    <td>{{ $return->customer->full_name }}</td>
                                    <td>{{ $return->items->sum('quantity') }}</td>
                                    <td>
                                        <span class="badge {{ $return->status_badge }} text-white status-badge" 
                                              data-return-id="{{ $return->id }}" 
                                              data-status="{{ $return->status }}"
                                              style="cursor: pointer;" 
                                              title="Click to change status">
                                            {{ ucfirst($return->status) }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($return->refund_amount ?? 0, 2) }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.returns.show', $return) }}" class="btn btn-info btn-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.returns.edit', $return) }}" class="btn btn-primary btn-sm" title="Process">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center">No returns found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $returns->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Date picker initialization
    $(document).ready(function() {
        // If date filters are applied, update form on changes
        $('#date_from, #date_to').change(function() {
            if ($('#date_from').val() && $('#date_to').val()) {
                $('#filter-form').submit();
            }
        });
        
        // Highlight table row on hover
        $('#returns-table tbody tr').hover(
            function() { $(this).addClass('bg-light'); },
            function() { $(this).removeClass('bg-light'); }
        );
        
        // Quick status change functionality
        $('.status-badge').click(function() {
            const returnId = $(this).data('return-id');
            const currentStatus = $(this).data('status');
            
            // Show status change options in a popover
            $(this).popover({
                html: true,
                title: 'Change Status',
                content: `
                    <div class="status-options">
                        <a href="#" class="change-status d-block mb-2" data-status="requested" data-return-id="${returnId}">
                            <span class="badge bg-warning text-white">Requested</span>
                        </a>
                        <a href="#" class="change-status d-block mb-2" data-status="approved" data-return-id="${returnId}">
                            <span class="badge bg-info text-white">Approved</span>
                        </a>
                        <a href="#" class="change-status d-block mb-2" data-status="received" data-return-id="${returnId}">
                            <span class="badge bg-primary text-white">Received</span>
                        </a>
                        <a href="#" class="change-status d-block mb-2" data-status="processed" data-return-id="${returnId}">
                            <span class="badge bg-info text-white">Processed</span>
                        </a>
                        <a href="#" class="change-status d-block mb-2" data-status="completed" data-return-id="${returnId}">
                            <span class="badge bg-success text-white">Completed</span>
                        </a>
                        <a href="#" class="change-status d-block" data-status="rejected" data-return-id="${returnId}">
                            <span class="badge bg-danger text-white">Rejected</span>
                        </a>
                    </div>
                `,
                placement: 'bottom',
                trigger: 'click'
            }).popover('show');
        });
        
        // Handle status change click
        $(document).on('click', '.change-status', function(e) {
            e.preventDefault();
            
            const returnId = $(this).data('return-id');
            const newStatus = $(this).data('status');
            
            if (confirm(`Are you sure you want to change this return's status to ${newStatus}?`)) {
                // Submit form via AJAX to update status
                $.ajax({
                    url: `{{ url('admin/returns') }}/${returnId}/status`,
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    success: function(response) {
                        // Reload page to show updated status
                        window.location.reload();
                    },
                    error: function(xhr) {
                        alert('Error updating status. Please try again.');
                    }
                });
            }
            
            // Hide the popover
            $('.status-badge').popover('hide');
        });
    });
    
    // Print function
    function printReturns() {
        window.print();
        return false;
    }
</script>
@endsection