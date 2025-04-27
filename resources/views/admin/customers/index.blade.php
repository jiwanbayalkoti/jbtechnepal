@extends('layouts.admin')

@section('title', 'Customer Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Customer Management</h1>
        <a href="{{ route('admin.customers.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Add New Customer
        </a>
    </div>

    <!-- Customer Stats Row -->
    <div class="row mb-4">
        <!-- Total Customers Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Customers Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['active'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inactive Customers Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Inactive Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['inactive'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blocked Customers Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Blocked Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['blocked'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-slash fa-2x text-gray-300"></i>
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
            <form action="{{ route('admin.customers.index') }}" method="GET" id="filter-form">
                <div class="row">
                    <!-- Search Field -->
                    <div class="col-md-4 mb-3">
                        <label for="search">Search</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Name, Email, Phone..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Search by name, email, or phone</small>
                    </div>
                    
                    <!-- Status Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" onchange="document.getElementById('filter-form').submit()">
                            <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                        </select>
                    </div>
                    
                    <!-- Country Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="country">Country</label>
                        <select class="form-control" id="country" name="country" onchange="document.getElementById('filter-form').submit()">
                            <option value="all" {{ request('country') == 'all' || !request('country') ? 'selected' : '' }}>All Countries</option>
                            @foreach($countries as $country)
                                <option value="{{ $country }}" {{ request('country') == $country ? 'selected' : '' }}>
                                    {{ $country }}
                                </option>
                            @endforeach
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
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-sync-alt mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Customers Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Customers List</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Export Options:</div>
                    <a class="dropdown-item" href="{{ route('admin.customers.export', request()->all()) }}"><i class="fas fa-file-csv mr-2"></i>Export CSV</a>
                    <a class="dropdown-item" href="#" onclick="printCustomers()"><i class="fas fa-print mr-2"></i>Print</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="customers-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="20%">Name</th>
                            <th width="15%">Email</th>
                            <th width="15%">Phone</th>
                            <th width="15%">Location</th>
                            <th width="10%">Status</th>
                            <th width="10%">Created</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($customers) > 0)
                            @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $customer->id }}</td>
                                    <td>{{ $customer->full_name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone ?? 'N/A' }}</td>
                                    <td>
                                        @if($customer->city || $customer->country)
                                            {{ $customer->city ? $customer->city . ', ' : '' }}
                                            {{ $customer->country ?? '' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $customer->status_badge }} text-white status-badge" 
                                              data-customer-id="{{ $customer->id }}" 
                                              data-status="{{ $customer->status }}"
                                              style="cursor: pointer;" 
                                              title="Click to change status">
                                            {{ ucfirst($customer->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $customer->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-info btn-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center">No customers found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $customers->links() }}
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
        $('#customers-table tbody tr').hover(
            function() { $(this).addClass('bg-light'); },
            function() { $(this).removeClass('bg-light'); }
        );
        
        // Quick status change functionality
        $('.status-badge').click(function() {
            const customerId = $(this).data('customer-id');
            const currentStatus = $(this).data('status');
            
            // Show status change options in a popover
            $(this).popover({
                html: true,
                title: 'Change Status',
                content: `
                    <div class="status-options">
                        <a href="#" class="change-status d-block mb-2" data-status="active" data-customer-id="${customerId}">
                            <span class="badge bg-success text-white">Active</span>
                        </a>
                        <a href="#" class="change-status d-block mb-2" data-status="inactive" data-customer-id="${customerId}">
                            <span class="badge bg-warning text-white">Inactive</span>
                        </a>
                        <a href="#" class="change-status d-block" data-status="blocked" data-customer-id="${customerId}">
                            <span class="badge bg-danger text-white">Blocked</span>
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
            
            const customerId = $(this).data('customer-id');
            const newStatus = $(this).data('status');
            
            if (confirm(`Are you sure you want to change this customer's status to ${newStatus}?`)) {
                // Submit form via AJAX to update status
                $.ajax({
                    url: `{{ route('admin.customers.update-status', ['customer' => '__id__']) }}`.replace('__id__', customerId),
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
    function printCustomers() {
        window.print();
        return false;
    }
</script>
@endsection 