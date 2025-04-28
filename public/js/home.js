document.addEventListener('DOMContentLoaded', function() {
    // Handle category and subcategory filtering
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const categoryId = this.value;
            const subcategoryContainer = document.getElementById(`subcategory_container_${categoryId}`);
            const subcategoryCheckboxes = subcategoryContainer.querySelectorAll('.subcategory-checkbox');
            
            // Toggle subcategories visibility
            subcategoryContainer.style.display = this.checked ? 'block' : 'none';
            
            // If unchecked, uncheck all subcategories
            if (!this.checked) {
                subcategoryCheckboxes.forEach(subCheckbox => {
                    subCheckbox.checked = false;
                });
            }
        });
    });

    // Handle load more button
    const loadMoreBtn = document.getElementById('load-more-btn');
    if (loadMoreBtn) {
        let page = 1;
        let processing = false;

        loadMoreBtn.addEventListener('click', function() {
            if (processing) return;
            
            processing = true;
            loadMoreBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
            page++;

            // Get filter values from the form
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            
            // Build query string
            const params = new URLSearchParams();
            params.append('page', page);
            
            // Add form data to params
            for (let [key, value] of formData.entries()) {
                if (value) {
                    params.append(key, value);
                }
            }

            // Add CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Make AJAX request
            fetch(loadMoreProductsUrl + '?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.html) {
                    document.getElementById('products-container').insertAdjacentHTML('beforeend', data.html);
                }
                
                if (data.lastPage) {
                    loadMoreBtn.classList.add('d-none');
                    document.getElementById('no-more-products').classList.remove('d-none');
                } else {
                    loadMoreBtn.innerHTML = 'Load More Products';
                }
                
                processing = false;
            })
            .catch(error => {
                console.error('Error loading more products:', error);
                processing = false;
                loadMoreBtn.innerHTML = 'Load More Products';
                alert('Error loading more products. Please try again.');
            });
        });
    }

    // Quick View Modal functionality
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
                // You can reuse your existing add to compare functionality here
                // For example, trigger a click on the corresponding compare button
                const compareBtn = document.querySelector(`.add-to-compare[data-product-id="${productId}"]`);
                if (compareBtn) {
                    compareBtn.click();
                } else {
                    // Or implement the comparison logic directly
                    addToCompare(productId);
                }
            }
        });
    }
    
    // Function to add product to compare
    function addToCompare(productId) {
        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Send AJAX request to add to compare
        fetch(addToCompareUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Product added to comparison!');
                // You might want to update a compare counter here
            } else {
                alert(data.message || 'Failed to add product to comparison.');
            }
        })
        .catch(error => {
            console.error('Error adding to compare:', error);
            alert('An error occurred. Please try again.');
        });
    }
}); 