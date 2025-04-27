@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ $category->name }}</h1>
    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Categories
    </a>
</div>

@if($category->description)
    <p class="lead mb-4">{{ $category->description }}</p>
@endif

<!-- Top category page advertisement -->
<div class="ad-container mb-4" data-ad-position="category_page_top"></div>

<div class="row">
    <!-- Sidebar with advertisements -->
    <div class="col-md-3">
        <div class="ad-container mb-4" data-ad-position="category_page_sidebar"></div>
    </div>
    
    <!-- Main products grid -->
    <div class="col-md-9">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @forelse($products as $product)
                <div class="col">
                    <div class="card h-100 product-card">
                        @if($product->primary_image)
                            <img src="{{ Storage::url($product->primary_image->path) }}" class="card-img-top p-3" alt="{{ $product->name }}">
                        @elseif($product->images->isNotEmpty())
                            <img src="{{ Storage::url($product->images->first()->path) }}" class="card-img-top p-3" alt="{{ $product->name }}">
                        @else
                            <div class="text-center pt-4">
                                <i class="fas fa-laptop fa-5x text-secondary"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            @if($product->brand)
                                <p class="card-text text-muted mb-1">Brand: {{ $product->brand }}</p>
                            @endif
                            @if($product->model)
                                <p class="card-text text-muted mb-2">Model: {{ $product->model }}</p>
                            @endif
                            <p class="card-text text-primary fw-bold fs-5 mb-3">
                                ${{ number_format($product->price, 2) }}
                            </p>
                            <p class="card-text">
                                {{ Str::limit($product->description, 100) }}
                            </p>
                        </div>
                        <div class="card-footer bg-white border-top-0 d-flex gap-2">
                            <a href="{{ route('product', $product->slug) }}" class="btn btn-outline-primary flex-grow-1">
                                Details
                            </a>
                            <button type="button" class="btn btn-primary add-to-compare" data-product-id="{{ $product->id }}">
                                <i class="fas fa-balance-scale"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        No products available in this category yet. Check back soon!
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Bottom category page advertisement -->
<div class="ad-container mt-4" data-ad-position="category_page_bottom"></div>
@endsection 