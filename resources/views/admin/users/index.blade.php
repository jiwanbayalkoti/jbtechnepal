@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">User Management</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle mr-1"></i> Add New User
        </a>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Users</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="usersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if ($user->profile_image)
                                    <img src="{{ asset('storage/' . $user->profile_image) }}" 
                                         alt="{{ $user->name }}" 
                                         class="img-profile rounded-circle mr-2"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                    <div class="rounded-circle bg-gray-300 text-center mr-2" 
                                         style="width: 40px; height: 40px; line-height: 40px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    @endif
                                    {{ $user->name }}
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if (auth()->id() !== $user->id && auth()->user()->isAdmin())
                                <select class="form-control role-select" 
                                        data-user-id="{{ $user->id }}"
                                        {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                                    <option value="{{ \App\Models\User::ROLE_CUSTOMER }}" 
                                            {{ $user->role === \App\Models\User::ROLE_CUSTOMER ? 'selected' : '' }}>
                                        Customer
                                    </option>
                                    <option value="{{ \App\Models\User::ROLE_EDITOR }}" 
                                            {{ $user->role === \App\Models\User::ROLE_EDITOR ? 'selected' : '' }}>
                                        Editor
                                    </option>
                                    <option value="{{ \App\Models\User::ROLE_MANAGER }}" 
                                            {{ $user->role === \App\Models\User::ROLE_MANAGER ? 'selected' : '' }}>
                                        Manager
                                    </option>
                                    <option value="{{ \App\Models\User::ROLE_ADMIN }}" 
                                            {{ $user->role === \App\Models\User::ROLE_ADMIN ? 'selected' : '' }}>
                                        Administrator
                                    </option>
                                </select>
                                @else
                                <span class="badge badge-{{ $user->role === \App\Models\User::ROLE_ADMIN ? 'danger' : 
                                        ($user->role === \App\Models\User::ROLE_MANAGER ? 'primary' : 
                                        ($user->role === \App\Models\User::ROLE_EDITOR ? 'success' : 'secondary')) }}">
                                    {{ $user->role === \App\Models\User::ROLE_ADMIN ? 'Administrator' : 
                                        ($user->role === \App\Models\User::ROLE_MANAGER ? 'Manager' : 
                                        ($user->role === \App\Models\User::ROLE_EDITOR ? 'Editor' : 'Customer')) }}
                                </span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if (auth()->id() !== $user->id && auth()->user()->isAdmin())
                                    <button type="button" class="btn btn-sm btn-danger delete-user" 
                                            data-user-id="{{ $user->id }}" 
                                            data-user-name="{{ $user->name }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the user <span id="delete-user-name" class="font-weight-bold"></span>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="delete-user-form" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#usersTable').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 25
        });
        
        // Handle role changes
        $('.role-select').change(function() {
            const userId = $(this).data('user-id');
            const newRole = $(this).val();
            
            $.ajax({
                url: "{{ route('admin.users.update-role') }}",
                method: 'POST',
                data: {
                    user_id: userId,
                    role: newRole,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message || 'An error occurred');
                    // Reset the select to previous value
                    $(this).prop('selectedIndex', $(this).data('original-index'));
                }
            });
        });
        
        // Store original select index to revert if needed
        $('.role-select').each(function() {
            $(this).data('original-index', this.selectedIndex);
        });
        
        // Setup delete user modal
        $('.delete-user').click(function() {
            const userId = $(this).data('user-id');
            const userName = $(this).data('user-name');
            
            $('#delete-user-name').text(userName);
            $('#delete-user-form').attr('action', `/admin/users/${userId}`);
            $('#deleteUserModal').modal('show');
        });
    });
</script>
@endsection 