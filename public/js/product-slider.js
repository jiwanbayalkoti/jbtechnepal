// Product Slider Functionality 
$(document).ready(function() {
    // Load newest products
    $.ajax({
        url: newestProductsUrl,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data.length > 0) {
                // Clear skeleton loader
                $('#product-slider-items').empty();
                
                // Add products to slider
                $.each(response.data, function(index, product) {
                    let priceHtml = '';
                    
                    if (product.discount_price) {
                        priceHtml = `
                            <div class="product-slide-regular-price">${formatPrice(product.discount_price)}</div>
                            <div class="product-slide-discount-price">${formatPrice(product.price)}</div>
                        `;
                    } else {
                        priceHtml = `
                            <div class="product-slide-regular-price">${formatPrice(product.price)}</div>
                        `;
                    }
                    
                    let slide = `
                        <div class="swiper-slide product-slide">
                            <div class="product-slide-inner">
                                <a href="${product.url}" class="product-slide-image">
                                    <img src="${product.image}" alt="${product.name}">
                                </a>
                                <div class="product-slide-category">${product.category || 'Uncategorized'}</div>
                                <h3 class="product-slide-title">
                                    <a href="${product.url}" class="text-decoration-none text-dark">${product.name}</a>
                                </h3>
                                <div class="product-slide-price">
                                    ${priceHtml}
                                </div>
                                <div class="product-slide-button">
                                    <div class="btn-group w-100">
                                        <button class="btn btn-info btn-sm quick-view-btn" 
                                                data-product-id="${product.id}"
                                                data-product-slug="${product.slug || ''}"
                                                data-product-name="${product.name}"
                                                data-product-price="${product.price || ''}"
                                                data-product-brand="${product.brand || ''}"
                                                data-product-model="${product.model || ''}"
                                                data-product-description="${product.description || ''}"
                                                data-product-image="${product.image || ''}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#quickViewModal">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="${product.url}" class="btn btn-primary btn-sm flex-grow-1">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    $('#product-slider-items').append(slide);
                });
                
                // Initialize Swiper
                initProductSwiper();
            } else {
                // If no products found, hide the slider section
                $('.product-slider-section').hide();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading newest products:', error);
            // Hide the slider section on error
            $('.product-slider-section').hide();
        }
    });
    
    // Helper function to format price
    function formatPrice(price) {
        return '$' + parseFloat(price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
    
    // Initialize Swiper
    function initProductSwiper() {
        new Swiper('.product-swiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            autoplay: sliderAutoplay ? {
                delay: 5000,
                disableOnInteraction: false,
            } : false,
            breakpoints: {
                // when window width is >= 576px
                576: {
                    slidesPerView: 2,
                },
                // when window width is >= 768px
                768: {
                    slidesPerView: 3,
                },
                // when window width is >= 992px
                992: {
                    slidesPerView: 4,
                },
                // when window width is >= 1200px
                1200: {
                    slidesPerView: 5,
                },
            }
        });
    }
    
    // Quick View Modal functionality for sliders
    const quickViewModal = document.getElementById('quickViewModal');
    if (quickViewModal) {
        // When the modal is about to be shown
        quickViewModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            // Extract product info from data attributes
            const productId = button.getAttribute('data-product-id');
            const productSlug = button.getAttribute('data-product-slug');
            const productName = button.getAttribute('data-product-name');
            const productPrice = button.getAttribute('data-product-price');
            const productBrand = button.getAttribute('data-product-brand');
            const productModel = button.getAttribute('data-product-model');
            const productDesc = button.getAttribute('data-product-description');
            const productImage = button.getAttribute('data-product-image');
            
            // Update modal content
            document.getElementById('quickViewName').textContent = productName;
            document.getElementById('quickViewPrice').textContent = productPrice ? '$' + parseFloat(productPrice).toFixed(2) : 'Price not available';
            
            let brandModelText = '';
            if (productBrand) brandModelText += productBrand;
            if (productModel) brandModelText += ' | ' + productModel;
            document.getElementById('quickViewBrandModel').textContent = brandModelText;
            
            document.getElementById('quickViewDescription').textContent = productDesc || 'No description available.';
            
            const imageEl = document.getElementById('quickViewImage');
            if (productImage) {
                imageEl.src = productImage;
                imageEl.style.display = 'block';
            } else {
                imageEl.style.display = 'none';
            }
            
            // Set product ID for the compare button
            document.querySelector('.quick-view-compare').setAttribute('data-product-id', productId);
            
            // Set details link
            const detailsBtn = document.getElementById('quickViewDetailsBtn');
            detailsBtn.href = productSlug ? `/product/${productSlug}` : `/products/${productId}`;
        });
        
        // Handle add to compare from quick view
        document.querySelector('.quick-view-compare').addEventListener('click', function(e) {
            const productId = this.getAttribute('data-product-id');
            if (productId) {
                // Implement the comparison logic
                addToCompare(productId);
            }
        });
    }
}); 