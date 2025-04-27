@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">My Profile</h4>
                    <a href="{{ route('profile.edit') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit Profile
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            @if($user->profile_image)
                                <img src="{{ asset('storage/'.$user->profile_image) }}" class="img-fluid rounded-circle mb-3" style="width: 180px; height: 180px; object-fit: cover;" alt="{{ $user->name }}'s Profile">
                            @else
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 180px; height: 180px; border: 2px solid #eee;">
                                    <i class="fas fa-user fa-5x text-secondary"></i>
                                </div>
                            @endif
                            <h5>{{ $user->name }}</h5>
                            @if($user->is_admin)
                                <span class="badge bg-danger">Administrator</span>
                            @else
                                <span class="badge bg-info">User</span>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2">Account Information</h5>
                                <div class="row mb-3">
                                    <div class="col-md-4 fw-bold">Name:</div>
                                    <div class="col-md-8">{{ $user->name }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 fw-bold">Email:</div>
                                    <div class="col-md-8">{{ $user->email }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 fw-bold">Member Since:</div>
                                    <div class="col-md-8">{{ $user->created_at->format('F d, Y') }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 fw-bold">Account Type:</div>
                                    <div class="col-md-8">
                                        @if($user->is_admin)
                                            <span class="text-danger">Administrator</span>
                                        @else
                                            <span class="text-info">Regular User</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                                    <i class="fas fa-edit me-1"></i>Edit Profile
                                </a>
                                @if($user->is_admin)
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-warning">
                                        <i class="fas fa-tachometer-alt me-1"></i>Admin Panel
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 