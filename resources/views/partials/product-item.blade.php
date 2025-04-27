<div class="col-md-4 mb-4">
    <div class="card h-100 product-card">
        <div class="position-relative">
            @if($product->primary_image)
                <img src="{{ Storage::url($product->primary_image->path) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 180px; object-fit: contain;">
            @elseif($product->images->isNotEmpty())
                <img src="{{ Storage::url($product->images->first()->path) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 180px; object-fit: contain;">
            @else
                <div class="bg-light text-center py-5">
                    <i class="fas fa-image text-secondary fa-3x"></i>
                </div>
            @endif
            
            <button class="btn btn-sm btn-primary position-absolute top-0 end-0 m-2 add-to-compare" 
                    data-product-id="{{ $product->id }}">
                <i class="fas fa-plus me-1"></i>Compare
            </button>
        </div>
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('product', $product->slug) }}" class="text-decoration-none text-dark">
                    {{ $product->name }}
                </a>
            </h5>
            <p class="card-text mb-1 text-muted">
                @if($product->brand)
                    <strong>{{ $product->brand }}</strong>
                @endif
                @if($product->model)
                    | {{ $product->model }}
                @endif
            </p>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="price">
                    @if($product->price)
                        <span class="fw-bold">${{ number_format($product->price, 2) }}</span>
                    @else
                        <span class="text-muted">Price not available</span>
                    @endif
                </div>
                <a href="{{ route('product', $product->slug) }}" class="btn btn-sm btn-outline-primary">Details</a>
            </div>
        </div>
        <div class="card-footer bg-white">
            <small class="text-muted">
                {{ $product->category ? $product->category->name : 'Uncategorized' }}
                @if($product->subcategory)
                    &raquo; {{ $product->subcategory->name }}
                @endif
            </small>
        </div>
    </div>
</div> 