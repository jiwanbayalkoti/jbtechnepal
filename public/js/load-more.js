$(document).ready(function() {
    let currentPage = 1;
    let isLoading = false;
    let noMoreProducts = false;
    
    // Function to handle load more button click
    $('#load-more').on('click', function() {
        if (isLoading || noMoreProducts) return;
        
        const $button = $(this);
        const $productsContainer = $('#products-container');
        
        // Set loading state
        isLoading = true;
        $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
        
        // Get filter values
        const searchQuery = $('#search-query').val();
        const categoryId = $('#filter-category').val();
        const subcategoryId = $('#filter-subcategory').val();
        const brandFilter = $('#filter-brand').val();
        const priceMin = $('#price-min').val();
        const priceMax = $('#price-max').val();
        const sortBy = $('#sort-by').val();
        
        // Increment page number
        currentPage++;
        
        // Make AJAX request
        $.ajax({
            url: '/load-more-products',
            type: 'GET',
            data: {
                page: currentPage,
                search: searchQuery,
                category_id: categoryId,
                subcategory_id: subcategoryId,
                brand: brandFilter,
                price_min: priceMin,
                price_max: priceMax,
                sort_by: sortBy
            },
            success: function(response) {
                // Append new products
                $productsContainer.append(response.html);
                
                // Reset button state
                isLoading = false;
                $button.html('Load More');
                
                // Handle last page
                if (response.last_page) {
                    noMoreProducts = true;
                    $button.html('No More Products').addClass('disabled');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading more products:', error);
                isLoading = false;
                $button.html('Load More');
            }
        });
    });
    
    // Handle filter changes
    $('.filter-control').on('change', function() {
        // Reset pagination
        currentPage = 1;
        noMoreProducts = false;
        $('#load-more').html('Load More').removeClass('disabled');
        
        // Submit form to refresh products
        $('#filter-form').submit();
    });
}); 