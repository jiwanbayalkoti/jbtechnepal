@extends('layouts.admin')

@section('title', 'Admin Profile')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
        <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary">
            <i class="fas fa-edit me-1"></i>Edit Profile
        </a>
    </div>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
                </div>
                <div class="card-body text-center">
                    @if (Auth::user()->profile_image)
                        <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" 
                             alt="{{ Auth::user()->name }}" class="img-profile rounded-circle mb-3"
                             style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-gray-300 text-center mx-auto mb-3" 
                             style="width: 150px; height: 150px; line-height: 150px; font-size: 60px;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    
                    <h4 class="mb-1">{{ Auth::user()->name }}</h4>
                    <p class="text-muted">{{ Auth::user()->email }}</p>
                    
                    <div class="mt-3">
                        <span class="badge bg-danger p-2">Administrator</span>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="small text-muted">Joined {{ Auth::user()->created_at->format('F d, Y') }}</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-8 col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Account Details</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">ID</th>
                                    <td>{{ Auth::user()->id }}</td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ Auth::user()->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ Auth::user()->email }}</td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td>Administrator</td>
                                </tr>
                                <tr>
                                    <th>Last Login</th>
                                    <td>{{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('F d, Y H:i:s') : 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 