@extends('layouts.admin')

@section('title', 'Banner Details')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Banner Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">Banners</a></li>
        <li class="breadcrumb-item active">Banner Details</li>
    </ol>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-image me-1"></i>
                        Banner Preview
                    </div>
                    <div>
                        <span class="badge {{ $banner->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $banner->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{ Storage::url($banner->image_path) }}" alt="{{ $banner->title }}" class="img-fluid banner-preview">
                    </div>
                    
                    <div class="banner-overlay-text">
                        @if($banner->title)
                            <h2 class="banner-title">{{ $banner->title }}</h2>
                        @endif
                        
                        @if($banner->subtitle)
                            <p class="banner-subtitle">{{ $banner->subtitle }}</p>
                        @endif
                        
                        @if($banner->link)
                            <div class="mt-2">
                                <a href="{{ $banner->link }}" class="btn btn-primary" target="_blank">View Link</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Banner Information
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 40%">ID</th>
                                <td>{{ $banner->id }}</td>
                            </tr>
                            <tr>
                                <th>Title</th>
                                <td>{{ $banner->title }}</td>
                            </tr>
                            @if($banner->subtitle)
                            <tr>
                                <th>Subtitle</th>
                                <td>{{ $banner->subtitle }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Order</th>
                                <td>{{ $banner->order }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge {{ $banner->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $banner->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            @if($banner->link)
                            <tr>
                                <th>Link</th>
                                <td>
                                    <a href="{{ $banner->link }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 200px;">
                                        {{ $banner->link }}
                                    </a>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <th>Image Path</th>
                                <td class="text-truncate" title="{{ $banner->image_path }}">
                                    {{ basename($banner->image_path) }}
                                </td>
                            </tr>
                            <tr>
                                <th>Created</th>
                                <td>{{ $banner->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated</th>
                                <td>{{ $banner->updated_at->format('M d, Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="d-inline banner-delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .banner-preview {
        max-height: 400px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    
    .banner-overlay-text {
        background-color: rgba(0,0,0,0.05);
        padding: 15px;
        border-radius: 5px;
        margin-top: 20px;
        text-align: center;
    }
    
    .banner-title {
        color: #333;
        margin-bottom: 10px;
    }
    
    .banner-subtitle {
        color: #666;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle banner deletion confirmation
        const deleteForm = document.querySelector('.banner-delete-form');
        
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (confirm('Are you sure you want to delete this banner? This action cannot be undone.')) {
                    this.submit();
                }
            });
        }
    });
</script>
@endpush 