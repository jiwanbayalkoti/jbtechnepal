@extends('layouts.admin')

@section('title', 'Test Advertisements')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Advertisement Test Page</h6>
        </div>
        <div class="card-body">
            <p class="mb-4">This page allows you to test how advertisements appear in different positions on your site. Any containers without ads will be hidden.</p>
            
            <div class="row">
                <div class="col-lg-9">
                    <!-- Top banner ad -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Homepage Top</h6>
                        </div>
                        <div class="card-body">
                            <div data-ad-position="homepage_top"></div>
                        </div>
                    </div>
                    
                    <!-- Homepage Slider -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Homepage Slider</h6>
                        </div>
                        <div class="card-body">
                            <div data-ad-position="homepage_slider"></div>
                        </div>
                    </div>
                    
                    <!-- Middle banner ad -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Homepage Middle</h6>
                        </div>
                        <div class="card-body">
                            <div data-ad-position="homepage_middle"></div>
                        </div>
                    </div>
                    
                    <!-- Bottom banner ad -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Homepage Bottom</h6>
                        </div>
                        <div class="card-body">
                            <div data-ad-position="homepage_bottom"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3">
                    <!-- Sidebar ad -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Sidebar</h6>
                        </div>
                        <div class="card-body">
                            <div data-ad-position="sidebar"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <!-- Category Page ad -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Category Page</h6>
                        </div>
                        <div class="card-body">
                            <div data-ad-position="category_page"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <!-- Product Page ad -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Product Page</h6>
                        </div>
                        <div class="card-body">
                            <div data-ad-position="product_page"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info">
                <p><strong>Note:</strong> If you don't see advertisements in some positions, it means there are no active ads configured for those positions.</p>
                <a href="{{ route('admin.advertisements.create') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-plus-circle mr-1"></i> Create New Advertisement
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/advertisements.js') }}"></script>
<script>
    // Reinitialize the advertisement manager for testing
    document.addEventListener('DOMContentLoaded', function() {
        if (window.adManager) {
            window.adManager.loadAdContainers();
        } else {
            window.adManager = new AdvertisementManager();
        }
    });
</script>
@endsection 