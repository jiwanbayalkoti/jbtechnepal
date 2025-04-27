@extends('layouts.admin')

@section('title', 'Add New User')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New User</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Users
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="role">Role <span class="text-danger">*</span></label>
                            <select class="form-control @error('role') is-invalid @enderror" 
                                    id="role" name="role" required>
                                <option value="{{ \App\Models\User::ROLE_CUSTOMER }}" 
                                        {{ old('role') === \App\Models\User::ROLE_CUSTOMER ? 'selected' : '' }}>
                                    Customer
                                </option>
                                <option value="{{ \App\Models\User::ROLE_EDITOR }}" 
                                        {{ old('role') === \App\Models\User::ROLE_EDITOR ? 'selected' : '' }}>
                                    Editor
                                </option>
                                <option value="{{ \App\Models\User::ROLE_MANAGER }}" 
                                        {{ old('role') === \App\Models\User::ROLE_MANAGER ? 'selected' : '' }}>
                                    Manager
                                </option>
                                <option value="{{ \App\Models\User::ROLE_ADMIN }}" 
                                        {{ old('role') === \App\Models\User::ROLE_ADMIN ? 'selected' : '' }}>
                                    Administrator
                                </option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="profile_image">Profile Image</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('profile_image') is-invalid @enderror" 
                                       id="profile_image" name="profile_image" accept="image/*">
                                <label class="custom-file-label" for="profile_image">Choose file</label>
                                @error('profile_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Optional. Max size: 2MB. Recommended size: 200x200px.
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Minimum 8 characters, at least one letter and one number.
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mt-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" 
                               id="send_welcome_email" name="send_welcome_email" value="1" 
                               {{ old('send_welcome_email') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="send_welcome_email">
                            Send welcome email with login credentials
                        </label>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-user-plus mr-1"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Update file input label with selected filename
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName || 'Choose file');
        });
    });
</script>
@endsection 