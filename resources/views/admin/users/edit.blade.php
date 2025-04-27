@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit User: {{ $user->name }}</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Users
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
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
                                        {{ old('role', $user->role) === \App\Models\User::ROLE_CUSTOMER ? 'selected' : '' }}>
                                    Customer
                                </option>
                                <option value="{{ \App\Models\User::ROLE_EDITOR }}" 
                                        {{ old('role', $user->role) === \App\Models\User::ROLE_EDITOR ? 'selected' : '' }}>
                                    Editor
                                </option>
                                <option value="{{ \App\Models\User::ROLE_MANAGER }}" 
                                        {{ old('role', $user->role) === \App\Models\User::ROLE_MANAGER ? 'selected' : '' }}>
                                    Manager
                                </option>
                                <option value="{{ \App\Models\User::ROLE_ADMIN }}" 
                                        {{ old('role', $user->role) === \App\Models\User::ROLE_ADMIN ? 'selected' : '' }}>
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
                                Optional. Max size: 2MB. Leave empty to keep current image.
                            </small>
                            
                            @if($user->profile_image)
                                <div class="mt-2">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/' . $user->profile_image) }}" 
                                             alt="{{ $user->name }}" class="img-thumbnail" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                        <div class="ml-2">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" 
                                                       id="remove_image" name="remove_image" value="1">
                                                <label class="custom-control-label" for="remove_image">
                                                    Remove current image
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Leave empty to keep current password. Minimum 8 characters if changing.
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save mr-1"></i> Update User
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
        
        // Disable file input when remove image is checked
        $('#remove_image').on('change', function() {
            if($(this).is(':checked')) {
                $('#profile_image').prop('disabled', true);
            } else {
                $('#profile_image').prop('disabled', false);
            }
        });
    });
</script>
@endsection 