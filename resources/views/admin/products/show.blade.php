@extends('layouts.admin')

@section('title', $product->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Product Details</h2>
    <div>
        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary me-2">
            <i class="fas fa-edit me-1"></i>Edit Product
        </a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Products
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Product Images</h5>
            </div>
            <div class="card-body">
                @if($product->images->isNotEmpty())
                    <div id="productImagesCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($product->images as $index => $image)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ $image->url }}" class="d-block w-100" alt="{{ $product->name }}" style="max-height: 300px; object-fit: contain;">
                                    @if($image->is_primary)
                                        <div class="badge bg-primary position-absolute top-0 end-0 m-2">Primary</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if($product->images->count() > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#productImagesCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productImagesCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        @endif
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">{{ $product->images->count() }} image(s) available</small>
                    </div>
                @else
                    <div class="py-5 text-center">
                        <i class="fas fa-image fa-8x text-secondary"></i>
                        <p class="mt-3">No images available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <th style="width: 30%">Product ID</th>
                            <td>{{ $product->id }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{ $product->name }}</td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td>
                                <a href="{{ route('admin.categories.edit', $product->category_id) }}">
                                    {{ $product->category->name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Price</th>
                            <td>${{ number_format($product->price, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Brand</th>
                            <td>{{ $product->brand ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Model</th>
                            <td>{{ $product->model ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Slug</th>
                            <td><code>{{ $product->slug }}</code></td>
                        </tr>
                        <tr>
                            <th>Created</th>
                            <td>{{ $product->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ $product->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Description</h5>
            </div>
            <div class="card-body">
                @if($product->description)
                    <p class="mb-0">{{ $product->description }}</p>
                @else
                    <p class="text-muted mb-0">No description available.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@if($product->specifications->isNotEmpty())
    <div class="card mt-2">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Product Specifications</h5>
            <a href="{{ route('admin.products.edit', $product->id) }}#specifications" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-edit me-1"></i>Edit Specifications
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 30%">Specification</th>
                            <th>Value</th>
                            <th style="width: 20%">Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->specifications as $spec)
                            <tr>
                                <td>{{ $spec->specificationType ? $spec->specificationType->name : 'Unknown Specification' }}</td>
                                <td>{{ $spec->value }}</td>
                                <td>{{ $spec->specificationType ? $spec->specificationType->unit : '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info mt-4">
        <h5 class="alert-heading">No Specifications</h5>
        <p class="mb-0">This product doesn't have any specifications defined. 
            <a href="{{ route('admin.products.edit', $product) }}">Edit this product</a> to add specifications.</p>
    </div>
@endif

<div class="mt-4 d-flex justify-content-between">
    <a href="{{ route('product', $product->slug) }}" class="btn btn-info" target="_blank">
        <i class="fas fa-eye me-1"></i>View on Website
    </a>
    
    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash me-1"></i>Delete Product
        </button>
    </form>
</div>
@endsection 