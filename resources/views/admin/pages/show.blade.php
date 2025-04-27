@extends('layouts.admin')

@section('title', 'Page Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Page Details: {{ $page->title }}</h1>
        <div>
            <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-1"></i> Edit Page
            </a>
            <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="btn btn-info ml-2">
                <i class="fas fa-external-link-alt mr-1"></i> View Page
            </a>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-arrow-left mr-1"></i> Back to Pages
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Page Information</h6>
                    <span class="badge badge-{{ $page->is_active ? 'success' : 'danger' }}">
                        {{ $page->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if ($page->featured_image)
                            <img src="{{ asset('storage/' . $page->featured_image) }}" 
                                 alt="{{ $page->title }}" class="img-fluid mb-3 border"
                                 style="max-height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-light text-center py-5 mb-3 border">
                                <i class="fas fa-file-image fa-4x text-gray-400"></i>
                                <p class="mt-2 text-muted">No featured image</p>
                            </div>
                        @endif
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">ID</th>
                                    <td>{{ $page->id }}</td>
                                </tr>
                                <tr>
                                    <th>Title</th>
                                    <td>{{ $page->title }}</td>
                                </tr>
                                <tr>
                                    <th>Slug</th>
                                    <td>
                                        <code>{{ $page->slug }}</code>
                                        @if(in_array($page->slug, ['about-us', 'contact-us']))
                                            <span class="badge badge-info ml-1">System</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>URL</th>
                                    <td>
                                        <a href="{{ route('page.show', $page->slug) }}" target="_blank">
                                            {{ route('page.show', $page->slug) }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Meta Title</th>
                                    <td>{{ $page->meta_title ?: 'Not set (using page title)' }}</td>
                                </tr>
                                <tr>
                                    <th>Meta Description</th>
                                    <td>{{ $page->meta_description ?: 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <th>Created</th>
                                    <td>{{ $page->created_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $page->updated_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Page Content</h6>
                </div>
                <div class="card-body">
                    <div class="border p-4 bg-white content-preview">
                        {!! $page->content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .content-preview {
        max-height: 600px;
        overflow-y: auto;
    }
    
    .content-preview img {
        max-width: 100%;
        height: auto;
    }
</style>
@endsection 