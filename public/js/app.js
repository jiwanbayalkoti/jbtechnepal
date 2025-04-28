// AJAX CSRF Setup
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Compare Product Functions
$(document).ready(function() {
    $('.add-to-compare').click(function(e) {
        e.preventDefault();
        
        const productId = $(this).data('product-id');
        
        $.ajax({
            url: addToCompareUrl,
            type: 'POST',
            data: {
                _token: csrfToken,
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    updateCompareCounter(response.count);
                    alert(response.message);
                } else {
                    alert(response.message);
                }
            }
        });
    });
    
    $('.remove-from-compare').click(function(e) {
        e.preventDefault();
        
        const productId = $(this).data('product-id');
        
        $.ajax({
            url: removeFromCompareUrl,
            type: 'POST',
            data: {
                _token: csrfToken,
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    updateCompareCounter(response.count);
                    location.reload();
                }
            }
        });
    });
});

function updateCompareCounter(count) {
    if (count > 0) {
        $('#compareCounter').html(`
            <a href="${compareUrl}" class="btn btn-danger rounded-circle position-relative p-3">
                <i class="fas fa-balance-scale fa-lg"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">
                    ${count}
                </span>
            </a>
        `);
    } else {
        $('#compareCounter').html('');
    }
}

// Initialize tooltips
$(document).ready(function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Promotional banner close functionality
    // Check if the banner was closed before
    if (getCookie('promotional_banner_closed') !== 'true') {
        $('.promotional-banner').show();
    } else {
        $('.promotional-banner').hide();
    }
    
    // Promotional banner close functionality
    // Only hide the banner if it was explicitly closed before
    if (getCookie('promotional_banner_closed') === 'true') {
        $('.promotional-banner').hide();
    }
    
    // Close banner button click
    $('.close-banner').on('click', function() {
        $(this).closest('.promotional-banner').slideUp();
        setCookie('promotional_banner_closed', 'true', 1); // Set cookie to expire in 1 day
    });

    // Promotional banner functionality
    // Only hide the banner if it was explicitly closed before
    if (getCookie('promotional_banner_closed') === 'true') {
        $('.promotional-banner').hide();
    }
    
    // Close banner button click
    $('.close-banner').on('click', function() {
        $(this).closest('.promotional-banner').slideUp();
        setCookie('promotional_banner_closed', 'true', 1); // Set cookie to expire in 1 day
    });
});

// Helper function to set a cookie
function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

// Helper function to get a cookie
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
} 