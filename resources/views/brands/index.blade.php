@extends('layouts.app')

@section('title', 'All Brands')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 fs-2 fw-bold">All Brands</h1>
    
    <div class="row">
        <div class="col-md-12 mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Brands</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-4">
        @foreach($brands as $brand)
            <div class="col">
                <a href="{{ route('brand.show', $brand->slug) }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0 brand-card">
                        <div class="card-body text-center">
                            @if($brand->logo)
                                <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" class="mb-3 brand-logo" style="height: 80px; width: auto; object-fit: contain;">
                            @else
                                <div class="bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="height: 80px; width: 80px;">
                                    <i class="fas fa-trademark text-primary fa-2x"></i>
                                </div>
                            @endif
                            <h5 class="card-title text-dark mb-0">{{ $brand->name }}</h5>
                            
                            @if($brand->products_count)
                                <p class="text-muted small mt-2">{{ $brand->products_count }} products</p>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
    
    <div class="d-flex justify-content-center mt-4">
        {{ $brands->links() }}
    </div>
</div>

@push('styles')
<style>
    .brand-card {
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .brand-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .brand-logo {
        max-width: 100%;
        transition: all 0.3s ease;
    }
    
    .brand-card:hover .brand-logo {
        transform: scale(1.05);
    }
</style>
@endpush
@endsection 