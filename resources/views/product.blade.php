@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ $product->name }}</h1>
    <a href="{{ route('categories.show', $product->category->slug) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to {{ $product->category->name }}
    </a>
</div>

<!-- Top product page advertisement -->
<div class="ad-container mb-4" data-ad-position="product_page_top"></div>

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-body">
                <!-- Main Image Display -->
                <div class="main-image-container text-center mb-3">
                    <div class="product-image-container position-relative">
                        @if($product->primary_image)
                            <img src="{{ Storage::url($product->primary_image->path) }}" class="img-fluid main-product-image" alt="{{ $product->name }}" id="mainImage">
                        @elseif($product->images->isNotEmpty())
                            <img src="{{ Storage::url($product->images->first()->path) }}" class="img-fluid main-product-image" alt="{{ $product->name }}" id="mainImage">
                        @else
                            <div class="py-5">
                                <i class="fas fa-laptop fa-7x text-secondary"></i>
                            </div>
                        @endif
                        <div class="zoom-hint">
                            <i class="fas fa-search me-1"></i>Hover to zoom
                        </div>
                        <div id="zoomResult" class="zoom-result-popup"></div>
                    </div>
                </div>

                <!-- Thumbnail Images -->
                @if($product->images->isNotEmpty())
                    <div class="thumbnail-container d-flex flex-wrap gap-2 justify-content-center">
                        @foreach($product->images as $image)
                            <div class="thumbnail-item" style="width: 80px; height: 80px; cursor: pointer;">
                                <img src="{{ Storage::url($image->path) }}" 
                                     class="img-thumbnail thumbnail-image" 
                                     alt="{{ $product->name }}"
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onclick="changeMainImage(this.src)">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Left side advertisement -->
        <div class="ad-container mt-4" data-ad-position="product_page_left"></div>
    </div>
    
    <div class="col-md-7">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Product Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h2>${{ number_format($product->price, 2) }}</h2>
                </div>
                
                @if($product->brand || $product->model)
                    <div class="mb-3">
                        @if($product->brand)
                            <p class="mb-1"><strong>Brand:</strong> {{ $product->brand }}</p>
                        @endif
                        @if($product->model)
                            <p class="mb-1"><strong>Model:</strong> {{ $product->model }}</p>
                        @endif
                    </div>
                @endif
                
                <div class="mb-4">
                    <h5>Description</h5>
                    <p>{{ $product->description }}</p>
                </div>
                
                <div class="d-flex gap-2 mb-4">
                    @if($product->inventory && $product->in_stock)
                        <form action="{{ route('cart.add') }}" method="POST" class="d-flex gap-2 w-100">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="input-group">
                                <span class="input-group-text">Qty</span>
                                <input type="number" name="quantity" class="form-control" value="1" min="1" max="{{ $product->stock }}">
                            </div>
                            <button type="submit" class="btn btn-success flex-grow-1">
                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                            </button>
                        </form>
                    @else
                        <button type="button" class="btn btn-secondary w-100" disabled>
                            <i class="fas fa-times-circle me-2"></i>Out of Stock
                        </button>
                    @endif
                </div>
                
                <div class="d-grid">
                    <button type="button" class="btn btn-primary add-to-compare" data-product-id="{{ $product->id }}">
                        <i class="fas fa-balance-scale me-2"></i>Add to Compare
                    </button>
                    <button type="button" class="btn btn-outline-danger mt-2 add-to-wishlist" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-price="{{ $product->price }}" data-product-slug="{{ $product->slug }}" data-product-image="{{ $product->primary_image ? Storage::url($product->primary_image->path) : ($product->images->isNotEmpty() ? Storage::url($product->images->first()->path) : asset('images/placeholder.jpg')) }}">
                        <i class="fas fa-heart me-2"></i><span class="wishlist-text">Add to Wishlist</span>
                    </button>
                </div>
            </div>
        </div>
        
        @if($product->specifications->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Specifications</h5>
                </div>
                <div class="card-body">
                    <table class="table spec-table">
                        <tbody>
                            @foreach($product->specifications as $spec)
                                <tr>
                                    <th>{{ $spec->specificationType ? $spec->specificationType->name : 'Unknown' }}</th>
                                    <td>
                                        {{ $spec->value }}
                                        @if($spec->specificationType && $spec->specificationType->unit)
                                            {{ $spec->specificationType->unit }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        
        <!-- Right side advertisement -->
        <div class="ad-container mt-4" data-ad-position="product_page_right"></div>
    </div>
</div>

<!-- Middle advertisement before related products -->
<div class="ad-container my-4" data-ad-position="product_page_middle"></div>

@if($relatedProducts->isNotEmpty())
    <div class="mt-5">
        <h3 class="mb-4">Related Products</h3>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @foreach($relatedProducts as $related)
                <div class="col">
                    <div class="card h-100 product-card">
                        @if($related->primary_image)
                            <img src="{{ Storage::url($related->primary_image->path) }}" class="card-img-top p-3" alt="{{ $related->name }}">
                        @elseif($related->images->isNotEmpty())
                            <img src="{{ Storage::url($related->images->first()->path) }}" class="card-img-top p-3" alt="{{ $related->name }}">
                        @else
                            <div class="text-center pt-3">
                                <i class="fas fa-laptop fa-4x text-secondary"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $related->name }}</h5>
                            <p class="card-text text-primary fw-bold">${{ number_format($related->price, 2) }}</p>
                        </div>
                        <div class="card-footer bg-white border-top-0 d-flex gap-2">
                            <a href="{{ route('product', $related->slug) }}" class="btn btn-outline-primary flex-grow-1">
                                Details
                            </a>
                            @if($related->inventory && $related->in_stock)
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $related->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </form>
                            @endif
                            <button type="button" class="btn btn-primary add-to-compare" data-product-id="{{ $related->id }}">
                                <i class="fas fa-balance-scale"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Bottom advertisement before cart items -->
<div class="ad-container my-4" data-ad-position="product_page_bottom"></div>

<!-- Recently Added to Cart -->
@php
    $cart = session()->get('cart', []);
    $recentlyAdded = [];
    
    // Get the last 4 added items
    $i = 0;
    foreach(array_reverse($cart, true) as $id => $details) {
        if($i >= 4) break;
        $recentlyAdded[$id] = $details;
        $i++;
    }
@endphp

@if(count($recentlyAdded) > 0)
    <div class="mt-5">
        <h3 class="mb-4">Recently Added to Cart</h3>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @foreach($recentlyAdded as $id => $details)
                        @php
                            $cartProduct = App\Models\Product::with(['inventory', 'primaryImage', 'images'])->find($id);
                        @endphp
                        @if($cartProduct)
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <img src="{{ $cartProduct->primary_image ? 
                                            Storage::url($cartProduct->primary_image->path) : 
                                            ($cartProduct->images->first() ? 
                                                Storage::url($cartProduct->images->first()->path) : 
                                                asset('images/placeholder.jpg')) }}" 
                                            alt="{{ $cartProduct->name }}" 
                                            class="img-fluid mb-3" style="max-height: 120px; object-fit: contain;">
                                        <h6>{{ $cartProduct->name }}</h6>
                                        <p class="mb-0">Qty: {{ $details['quantity'] }}</p>
                                        <p class="text-primary">${{ number_format($cartProduct->price * $details['quantity'], 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('cart.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-2"></i>View Cart
                    </a>
                    <a href="{{ route('checkout.index') }}" class="btn btn-success ms-2">
                        <i class="fas fa-credit-card me-2"></i>Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Wishlist Modal -->
<div class="modal fade" id="wishlistModal" tabindex="-1" aria-labelledby="wishlistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="wishlistModalLabel">My Wishlist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="wishlist-items" class="row g-3">
                    <!-- Wishlist items will be dynamically loaded here -->
                </div>
                <div id="empty-wishlist" class="text-center py-5">
                    <i class="fas fa-heart text-muted fa-3x mb-3"></i>
                    <p>Your wishlist is empty</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="clear-wishlist">Clear Wishlist</button>
            </div>
        </div>
    </div>
</div>

<style>
.main-product-image {
    max-height: 400px;
    width: 100%;
    object-fit: contain;
    transition: all 0.3s ease;
    cursor: crosshair;
}

.thumbnail-image {
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.thumbnail-image:hover {
    border-color: #0d6efd;
}

.thumbnail-item.active .thumbnail-image {
    border-color: #0d6efd;
}

/* Popup zoom styles */
.product-image-container {
    position: relative;
    overflow: visible;
}

.zoom-result-popup {
    position: absolute;
    width: 350px;
    height: 350px;
    border: 1px solid #ddd;
    background-repeat: no-repeat;
    background-color: white;
    overflow: hidden;
    box-shadow: 0 5px 25px rgba(0,0,0,0.2);
    border-radius: 5px;
    z-index: 1000;
    display: none;
    pointer-events: none; /* Prevents the popup from interfering with mouse events */
    top: 0;
    right: -370px; /* Position to the right of the image */
}

.zoom-hint {
    position: absolute;
    top: 10px;
    right: 10px;
    color: #6c757d;
    font-size: 0.75rem;
    background-color: rgba(255, 255, 255, 0.8);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    z-index: 100;
    transition: opacity 0.3s ease;
}

/* Wishlist styles */
.btn-outline-danger.in-wishlist {
    background-color: #dc3545;
    color: white;
}

.wishlist-badge {
    position: fixed;
    top: 70px;
    right: 20px;
    z-index: 1000;
    cursor: pointer;
}

.wishlist-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: #dc3545;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.wishlist-item {
    transition: all 0.3s ease;
}

.wishlist-item .remove-from-wishlist {
    visibility: hidden;
    opacity: 0;
    transition: all 0.2s ease;
}

.wishlist-item:hover .remove-from-wishlist {
    visibility: visible;
    opacity: 1;
}

/* Sticky navbar styles */
.sticky-navbar {
    position: fixed !important;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    z-index: 1030;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    background-color: white !important;
    animation: slideDown 0.35s ease-out;
}

@keyframes slideDown {
    from {
        transform: translateY(-100%);
    }
    to {
        transform: translateY(0);
    }
}
</style>

<script>
function changeMainImage(src) {
    const mainImage = document.getElementById('mainImage');
    mainImage.src = src;
    
    // Update active thumbnail
    document.querySelectorAll('.thumbnail-item').forEach(item => {
        item.classList.remove('active');
        if (item.querySelector('img').src === src) {
            item.classList.add('active');
        }
    });
    
    // Reset zoom view
    const zoomResult = document.getElementById('zoomResult');
    zoomResult.style.backgroundImage = '';
    zoomResult.style.display = 'none';
}

// Wishlist functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize wishlist
    let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    
    // Check for and migrate old wishlist items
    migrateOldWishlistItems();
    
    updateWishlistUI();
    
    // Add Wishlist badge to the page
    const badgeHtml = `
        <div class="wishlist-badge" data-bs-toggle="modal" data-bs-target="#wishlistModal">
            <div class="btn btn-light rounded-circle shadow-sm p-2">
                <i class="fas fa-heart text-danger"></i>
                <span class="wishlist-count">${wishlist.length}</span>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', badgeHtml);
    
    // Add to Wishlist button click handler
    document.querySelectorAll('.add-to-wishlist').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const productPrice = this.getAttribute('data-product-price');
            const productImage = this.getAttribute('data-product-image');
            const productSlug = this.getAttribute('data-product-slug');
            
            // Check if product is already in wishlist
            const index = wishlist.findIndex(item => item.id === productId);
            
            if (index === -1) {
                // Add to wishlist
                wishlist.push({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    image: productImage,
                    slug: productSlug
                });
                
                this.classList.add('in-wishlist');
                this.querySelector('.wishlist-text').textContent = 'Remove from Wishlist';
                
                // Show toast notification
                showToast('Product added to wishlist!', 'success');
            } else {
                // Remove from wishlist
                wishlist.splice(index, 1);
                
                this.classList.remove('in-wishlist');
                this.querySelector('.wishlist-text').textContent = 'Add to Wishlist';
                
                // Show toast notification
                showToast('Product removed from wishlist', 'warning');
            }
            
            // Save wishlist to localStorage
            localStorage.setItem('wishlist', JSON.stringify(wishlist));
            
            // Update UI
            updateWishlistUI();
            
            // Update wishlist count
            document.querySelector('.wishlist-count').textContent = wishlist.length;
        });
    });
    
    // Clear wishlist button
    const clearWishlistBtn = document.getElementById('clear-wishlist');
    if (clearWishlistBtn) {
        clearWishlistBtn.addEventListener('click', function() {
            // Clear wishlist
            wishlist = [];
            localStorage.setItem('wishlist', JSON.stringify(wishlist));
            
            // Update UI
            updateWishlistUI();
            
            // Update wishlist count
            document.querySelector('.wishlist-count').textContent = '0';
            
            // Update add to wishlist buttons
            document.querySelectorAll('.add-to-wishlist').forEach(button => {
                button.classList.remove('in-wishlist');
                button.querySelector('.wishlist-text').textContent = 'Add to Wishlist';
            });
            
            // Show toast notification
            showToast('Wishlist cleared', 'info');
        });
    }
    
    // Function to update wishlist UI
    function updateWishlistUI() {
        const wishlistItems = document.getElementById('wishlist-items');
        const emptyWishlist = document.getElementById('empty-wishlist');
        
        if (wishlistItems && emptyWishlist) {
            if (wishlist.length === 0) {
                wishlistItems.style.display = 'none';
                emptyWishlist.style.display = 'block';
            } else {
                wishlistItems.style.display = 'flex';
                emptyWishlist.style.display = 'none';
                
                // Clear existing items
                wishlistItems.innerHTML = '';
                
                // Add items to wishlist modal
                wishlist.forEach(item => {
                    wishlistItems.innerHTML += `
                        <div class="col-md-4 col-sm-6 wishlist-item">
                            <div class="card h-100">
                                <div class="position-relative">
                                    <img src="${item.image}" alt="${item.name}" class="card-img-top p-2" style="height: 180px; object-fit: contain;">
                                    <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle remove-from-wishlist" data-product-id="${item.id}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">${item.name}</h6>
                                    <p class="card-text text-primary">$${parseFloat(item.price).toFixed(2)}</p>
                                    <a href="{{ route('product', '') }}/${item.slug}" class="btn btn-sm btn-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                // Add event listeners to remove buttons
                document.querySelectorAll('.remove-from-wishlist').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const productId = this.getAttribute('data-product-id');
                        
                        // Remove from wishlist
                        wishlist = wishlist.filter(item => item.id !== productId);
                        localStorage.setItem('wishlist', JSON.stringify(wishlist));
                        
                        // Update UI
                        updateWishlistUI();
                        
                        // Update wishlist count
                        document.querySelector('.wishlist-count').textContent = wishlist.length;
                        
                        // Update add to wishlist button if on product page
                        const addToWishlistBtn = document.querySelector(`.add-to-wishlist[data-product-id="${productId}"]`);
                        if (addToWishlistBtn) {
                            addToWishlistBtn.classList.remove('in-wishlist');
                            addToWishlistBtn.querySelector('.wishlist-text').textContent = 'Add to Wishlist';
                        }
                        
                        // Show toast notification
                        showToast('Product removed from wishlist', 'warning');
                    });
                });
            }
        }
        
        // Update current product button state
        const currentProductId = document.querySelector('.add-to-wishlist')?.getAttribute('data-product-id');
        if (currentProductId) {
            const inWishlist = wishlist.some(item => item.id === currentProductId);
            const addToWishlistBtn = document.querySelector('.add-to-wishlist');
            
            if (inWishlist) {
                addToWishlistBtn.classList.add('in-wishlist');
                addToWishlistBtn.querySelector('.wishlist-text').textContent = 'Remove from Wishlist';
            } else {
                addToWishlistBtn.classList.remove('in-wishlist');
                addToWishlistBtn.querySelector('.wishlist-text').textContent = 'Add to Wishlist';
            }
        }
    }
    
    // Function to show toast notification
    function showToast(message, type = 'info') {
        // Create toast container if it doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }
        
        // Create toast
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'info' ? 'info' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'danger'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Initialize Bootstrap toast
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: 3000
        });
        
        // Show toast
        bsToast.show();
        
        // Remove toast from DOM after it's hidden
        toast.addEventListener('hidden.bs.toast', function() {
            toast.remove();
        });
    }
    
    // Function to update older wishlist items that might not have slugs
    function migrateOldWishlistItems() {
        let needsMigration = false;
        
        // Check if any items are missing slug
        wishlist.forEach(item => {
            if (!item.slug) {
                needsMigration = true;
            }
        });
        
        if (needsMigration) {
            // Show migration notification
            showToast('Updating your wishlist items...', 'info');
            
            // Filter out items without slugs (they can't be accessed anymore)
            wishlist = wishlist.filter(item => item.slug);
            
            // Save updated wishlist
            localStorage.setItem('wishlist', JSON.stringify(wishlist));
            
            // Update UI after migration
            updateWishlistUI();
            document.querySelector('.wishlist-count').textContent = wishlist.length;
        }
    }
    
    // Existing zoom functionality
    const mainImage = document.getElementById('mainImage');
    const zoomResult = document.getElementById('zoomResult');
    const zoomHint = document.querySelector('.zoom-hint');
    
    if (mainImage && zoomResult) {
        // Set active thumbnail
        document.querySelectorAll('.thumbnail-item').forEach(item => {
            if (item.querySelector('img').src === mainImage.src) {
                item.classList.add('active');
            }
        });
        
        // Get main image natural dimensions for accurate zooming
        let imgWidth, imgHeight;
        
        // Initialize with default size
        imgWidth = mainImage.naturalWidth || mainImage.width;
        imgHeight = mainImage.naturalHeight || mainImage.height;
        
        // Update when image is fully loaded
        mainImage.onload = function() {
            imgWidth = this.naturalWidth;
            imgHeight = this.naturalHeight;
        };
        
        // Initialize zoom functionality
        const zoom = 3; // Zoom level
        
        // Add event listeners for zoom
        mainImage.addEventListener('mousemove', function(e) {
            // Show zoom result
            zoomResult.style.display = 'block';
            
            // Hide hint when zooming
            if (zoomHint) zoomHint.style.opacity = '0.3';
            
            // Get cursor position relative to the image
            const bounds = mainImage.getBoundingClientRect();
            const x = (e.clientX - bounds.left) / bounds.width;
            const y = (e.clientY - bounds.top) / bounds.height;
            
            // Calculate background position for zoom result
            const bgX = Math.min(Math.max(x * 100, 0), 100);
            const bgY = Math.min(Math.max(y * 100, 0), 100);
            
            // Display the zoomed result (no need to reposition, only update background)
            zoomResult.style.backgroundImage = `url('${mainImage.src}')`;
            zoomResult.style.backgroundSize = `${zoom * 100}%`;
            zoomResult.style.backgroundPosition = `${bgX}% ${bgY}%`;
        });
        
        // Hide zoom when mouse leaves the image
        mainImage.addEventListener('mouseleave', function() {
            zoomResult.style.display = 'none';
            if (zoomHint) zoomHint.style.opacity = '1';
        });
    }
    
    // Enhanced sticky navbar functionality
    // This targets any navigation elements commonly used in Laravel apps
    const possibleNavbars = [
        'nav.navbar', 
        'header nav', 
        'header',
        '#app > nav',
        '.navbar',
        '.navigation',
        '.site-header'
    ];
    
    // Find the first matching navbar element
    let navbar = null;
    for (const selector of possibleNavbars) {
        const element = document.querySelector(selector);
        if (element) {
            navbar = element;
            break;
        }
    }
    
    if (navbar) {
        // Store original styles
        const originalPosition = window.getComputedStyle(navbar).position;
        const originalTop = window.getComputedStyle(navbar).top;
        const navbarHeight = navbar.offsetHeight;
        let paddingAdded = false;
        
        // Function to handle scroll
        function handleScroll() {
            if (window.pageYOffset > 100) { // Activate after scrolling 100px
                if (!navbar.classList.contains('sticky-navbar')) {
                    navbar.classList.add('sticky-navbar');
                    
                    // Add padding to prevent content jump, only if we haven't already
                    if (!paddingAdded) {
                        document.body.style.paddingTop = navbarHeight + 'px';
                        paddingAdded = true;
                    }
                }
            } else {
                navbar.classList.remove('sticky-navbar');
                if (paddingAdded) {
                    document.body.style.paddingTop = '0';
                    paddingAdded = false;
                }
            }
        }
        
        // Attach scroll handler
        window.addEventListener('scroll', handleScroll);
        
        // Initial check
        handleScroll();
    } else {
        console.log('No navigation element found to make sticky');
    }
});
</script>
@endsection 