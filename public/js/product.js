/**
 * Product page functionality
 * Handles image zoom, wishlist, and sticky navbar
 */

// Image functionality
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

// Initialize functionality when DOM is loaded
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
                
                // Get the base URL for product links
                const productBaseUrl = document.querySelector('meta[name="product-base-url"]').getAttribute('content');
                
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
                                    <a href="${productBaseUrl}/${item.slug}" class="btn btn-sm btn-primary">View Details</a>
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
    
    // Initialize zoom functionality
    initZoom();
    
    // Initialize sticky navbar
    initStickyNavbar();
});

// Initialize zoom functionality
function initZoom() {
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
}

// Initialize sticky navbar
function initStickyNavbar() {
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
} 