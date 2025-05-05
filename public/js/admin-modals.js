/**
 * Admin Form Modals
 * 
 * This script handles the functionality for popup form modals in the admin area
 */

// Ensure we have both vanilla JS and jQuery implementations for wider browser support
$(document).ready(function() {
    console.log('jQuery admin modals initialization');
    
    // Fix for modal backdrop not disappearing
    $(document).on('hidden.bs.modal', '.modal', function(e) {
        console.log('Modal hidden event fired');
        // Remove any lingering backdrop
        $('.modal-backdrop').remove();
        // Fix body classes
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
        // Log to console for debugging
        console.log('Modal cleanup executed');
    });
    
    // Fix for nested modals causing backdrop issues
    $(document).on('show.bs.modal', '.modal', function(e) {
        // Make sure only one backdrop exists
        if ($('.modal-backdrop').length > 1) {
            $('.modal-backdrop:not(:last)').remove();
        }
    });
    
    // Initialize any existing modals
    $('.modal').each(function() {
        var modalId = $(this).attr('id');
        console.log('jQuery: Found modal with ID:', modalId);
    });
    
    // Form submission handlers
    $('.submit-modal-form').on('click', function() {
        var formId = $(this).data('form-id');
        console.log('jQuery: Submit button clicked for form:', formId);
        
        // First try to find the form by ID
        var $form = $('#' + formId);
        
        // If not found, look for any form in the modal
        if ($form.length === 0) {
            var $modal = $(this).closest('.modal');
            if ($modal.length) {
                console.log('jQuery: Form not found by ID. Looking for any form in the modal...');
                var $modalBody = $modal.find('.modal-body');
                if ($modalBody.length) {
                    var $formInModal = $modalBody.find('form');
                    
                    // If a form was found, assign the expected ID
                    if ($formInModal.length && !$formInModal.attr('id') && formId) {
                        console.log('jQuery: Found form without ID. Assigning ID:', formId);
                        $formInModal.attr('id', formId);
                        $form = $formInModal;
                    }
                    
                    // If still no form found but we have a formContent div, create a form wrapper
                    if ($form.length === 0) {
                        var $formContent = $modalBody.find('#formContent');
                        if ($formContent.length) {
                            console.log('jQuery: No form found, but formContent div exists. Creating form wrapper.');
                            // Create a form around the content
                            var formHtml = $formContent.html();
                            $formContent.html('<form id="' + formId + '">' + formHtml + '</form>');
                            $form = $('#' + formId);
                        }
                    }
                }
            }
        }
        
        if ($form.length) {
            console.log('jQuery: Form found. Submitting:', formId);
            $form.submit();
        } else {
            console.error('jQuery: Form not found:', formId);
            
            // Show error message
            var $modal = $(this).closest('.modal');
            if ($modal.length) {
                var $modalBody = $modal.find('.modal-body');
                
                if ($modalBody.length) {
                    var $errorAlert = $('<div class="alert alert-warning">Form is still loading. Please wait a moment and try again.</div>');
                    $modalBody.prepend($errorAlert);
                    
                    // Remove the error after 3 seconds
                    setTimeout(function() {
                        $errorAlert.remove();
                    }, 3000);
                }
            }
        }
    });
    
    // Open modal buttons
    $('[data-open-modal]').on('click', function() {
        var modalId = $(this).data('open-modal');
        console.log('jQuery: Opening modal:', modalId);
        $('#' + modalId).modal('show');
    });
    
    // Edit buttons with AJAX loading
    $('[data-edit-url]').on('click', function() {
        var url = $(this).data('edit-url');
        var modalId = $(this).data('open-modal');
        var $modal = $('#' + modalId);
        var $modalBody = $modal.find('.modal-body');
        var originalContent = $modalBody.html();
        
        console.log('jQuery: Loading content from:', url, 'into modal:', modalId);
        
        // Show loading indicator
        $modalBody.html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading form data...</p></div>');
        
        // Show the modal
        $modal.modal('show');
        
        // Make the AJAX request
        $.ajax({
            url: url,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                console.log('jQuery: AJAX request successful');
                
                if (response && response.success && response.html) {
                    // Parse the HTML from the response
                    var $parsedHtml = $(response.html);
                    var $formContent = $parsedHtml.filter('#formContent');
                    
                    if ($formContent.length) {
                        $modalBody.empty().append($formContent);
                        
                        // Find the form inside the content and explicitly set its ID
                        const $formInContent = $modalBody.find('form');
                        if ($formInContent.length) {
                            const formId = $modal.attr('id').replace('Modal', 'Form');
                            $formInContent.attr('id', formId);
                            console.log(`jQuery: Set form ID to ${formId}`);
                        }
                    } else {
                        $modalBody.html(response.html);
                    }
                    
                    initializeFormComponents($modalBody);
                    
                    // Execute any scripts in the content
                    var scripts = $modalBody.find('script');
                    if (scripts.length) {
                        scripts.each(function() {
                            var script = document.createElement('script');
                            script.text = this.text;
                            document.head.appendChild(script).parentNode.removeChild(script);
                        });
                    }
                    
                    // Make sure the form ID and submit button are properly connected
                    const loadedForm = $modalBody.find('form');
                    const submitBtn = $modal.find('.submit-modal-form');
                    
                    if (loadedForm.length && submitBtn.length) {
                        // Always ensure the form has the expected ID
                        const formId = submitBtn.data('form-id');
                        if (formId) {
                            console.log('jQuery: Setting form ID to:', formId);
                            loadedForm.attr('id', formId);
                            
                            // Also set a data attribute for redundancy
                            loadedForm.attr('data-form-id', formId);
                        }
                        
                        // Ensure all inline scripts from the loaded form have executed
                        setTimeout(() => {
                            // Always re-enable the submit button after loading
                            submitBtn.prop('disabled', false);
                            console.log('jQuery: Form successfully loaded and connected to button');
                            
                            // Double-check form ID one last time
                            if (formId && !loadedForm.attr('id')) {
                                loadedForm.attr('id', formId);
                            }
                        }, 300); // Give a short delay to allow inline scripts to run
                    }
                } else {
                    $modalBody.html('<div class="alert alert-danger">Invalid response format from server.</div>');
                    setTimeout(function() {
                        $modalBody.html(originalContent);
                    }, 3000);
                }
            },
            error: function(xhr, status, error) {
                console.error('jQuery: AJAX request error:', error);
                
                // Try to determine if the response was meant to be HTML
                var contentType = xhr.getResponseHeader('Content-Type');
                if (contentType && contentType.indexOf('text/html') !== -1) {
                    // This is likely a redirect to login page or an error page
                    $modalBody.html('<div class="alert alert-danger">Session may have expired. Try refreshing the page.</div>');
                    console.error('Server returned HTML instead of JSON. You may need to log in again.');
                } else {
                $modalBody.html('<div class="alert alert-danger">Error loading form: ' + error + '</div>');
                }
                
                setTimeout(function() {
                    $modalBody.html(originalContent);
                }, 3000);
            }
        });
    });
    
    // Delete confirmation
    $('[data-delete-confirm]').on('click', function(e) {
        e.preventDefault();
        var message = $(this).data('delete-confirm') || 'Are you sure you want to delete this item?';
        var $form = $(this).closest('form');
        
        if (confirm(message)) {
            $form.submit();
        }
    });
    
    // Function to initialize form components
    function initializeFormComponents($container) {
        // Initialize select2 if available
        if ($.fn.select2) {
            $container.find('.select2').select2({
                dropdownParent: $container.closest('.modal')
            });
        }
        
        // Initialize datepicker if available
        if ($.fn.datepicker) {
            $container.find('.datepicker').datepicker();
        }
    }
    
    // Auto-open modal if there are validation errors
    var $errors = $('.is-invalid');
    if ($errors.length) {
        var $form = $errors.first().closest('form');
        var $modal = $form.closest('.modal');
        
        if ($modal.length) {
            $modal.modal('show');
        }
    }
});

// Original vanilla JavaScript implementation as fallback
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin modals script loaded');
    
    // Fix for modal backdrop not disappearing
    document.addEventListener('hidden.bs.modal', function(event) {
        if (event.target.classList.contains('modal')) {
            console.log('Modal hidden event fired (vanilla JS)');
            // Remove any lingering backdrop
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => {
                backdrop.parentNode.removeChild(backdrop);
            });
            
            // Fix body classes
            document.body.classList.remove('modal-open');
            document.body.style.paddingRight = '';
            console.log('Modal cleanup executed (vanilla JS)');
        }
    }, true);
    
    // Initialize event handlers
    initializeEventHandlers();
    
    // Initialize Bootstrap modals
    try {
        const modalElements = document.querySelectorAll('.modal');
        console.log('Found ' + modalElements.length + ' modal elements');
        
        if (modalElements.length > 0) {
            modalElements.forEach(function(element) {
                // Only initialize if not already initialized
                if (!bootstrap.Modal.getInstance(element)) {
                    new bootstrap.Modal(element);
                    console.log('Initialized modal: ' + element.id);
                }
            });
        }
    } catch (error) {
        console.error('Error initializing modals:', error);
    }

    // Form submission handlers
    
    try {
        const submitButtons = document.querySelectorAll('.submit-modal-form');
        console.log('Found ' + submitButtons.length + ' submit buttons');
        
        if (submitButtons.length > 0) {
            submitButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const formId = this.getAttribute('data-form-id');
                    console.log('Submit button clicked for form: ' + formId);
                    
                    // First, try to find the form by ID
                    let form = document.getElementById(formId);
                    
                    // If not found by ID, look for any form in the modal
                    if (!form) {
                        const modalElement = this.closest('.modal');
                        if (modalElement) {
                            console.log('Form not found by ID. Looking for any form in the modal...');
                            const modalBody = modalElement.querySelector('.modal-body');
                            if (modalBody) {
                                form = modalBody.querySelector('form');
                                
                                // If a form was found, assign the expected ID
                                if (form && !form.id && formId) {
                                    console.log('Found form without ID. Assigning ID: ' + formId);
                                    form.id = formId;
                                }
                                
                                // If still no form found but we have a formContent div, create a form wrapper
                                if (!form) {
                                    const formContent = modalBody.querySelector('#formContent');
                                    if (formContent) {
                                        console.log('No form found, but formContent div exists. Creating form wrapper.');
                                        // Create a form around the content
                                        const formHtml = formContent.innerHTML;
                                        formContent.innerHTML = '<form id="' + formId + '">' + formHtml + '</form>';
                                        form = document.getElementById(formId);
                                    }
                                }
                            }
                        }
                    }
                    
                    if (form) {
                        console.log('Form found. Submitting: ' + formId);
                        form.submit();
                    } else {
                        console.error('Form not found: ' + formId);
                        // Try to find the modal this button is in
                        const modalElement = this.closest('.modal');
                        if (modalElement) {
                            const modalId = modalElement.id;
                            console.log('Attempting to recover - in modal: ' + modalId);
                            
                            // Disable the button to prevent repeated clicks
                            this.disabled = true;
                            
                            // Show an error message in the modal
                            const modalBody = modalElement.querySelector('.modal-body');
                            if (modalBody) {
                                const errorAlert = document.createElement('div');
                                errorAlert.className = 'alert alert-warning';
                                errorAlert.innerHTML = 'Form is still loading. Please wait a moment and try again.';
                                modalBody.prepend(errorAlert);
                                
                                // Remove the error after 3 seconds
                                setTimeout(() => {
                                    errorAlert.remove();
                                    this.disabled = false;
                                }, 3000);
                            }
                        }
                    }
                });
            });
        }
    } catch (error) {
        console.error('Error setting up form submission:', error);
    }

    // Open modal buttons
    try {
        const openModalButtons = document.querySelectorAll('[data-open-modal]');
        console.log('Found ' + openModalButtons.length + ' open modal buttons');
        
        if (openModalButtons.length > 0) {
            openModalButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const modalId = this.getAttribute('data-open-modal');
                    console.log('Attempting to open modal: ' + modalId);
                    
                    const modalElement = document.getElementById(modalId);
                    if (modalElement) {
                        try {
                            const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                            modal.show();
                            console.log('Modal opened: ' + modalId);
                        } catch (modalError) {
                            console.error('Error opening modal:', modalError);
                        }
                    } else {
                        console.error('Modal element not found: ' + modalId);
                    }
                });
            });
        }
    } catch (error) {
        console.error('Error setting up open modal buttons:', error);
    }

    // Edit buttons - load data via AJAX
    try {
        const editButtons = document.querySelectorAll('[data-edit-url]');
        console.log('Found ' + editButtons.length + ' edit buttons');
        
        if (editButtons.length > 0) {
            editButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const url = this.getAttribute('data-edit-url');
                    const modalId = this.getAttribute('data-open-modal');
                    console.log('Edit button clicked - URL: ' + url + ', Modal: ' + modalId);
                    
                    const modalElement = document.getElementById(modalId);
                    
                    if (modalElement && url) {
                        // Show loading indicator
                        const modalBody = modalElement.querySelector('.modal-body');
                        const originalContent = modalBody.innerHTML;
                        modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading form data...</p></div>';
                        
                        // Show the modal
                        try {
                            const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                            modal.show();
                            console.log('Modal opened for edit: ' + modalId);
                        } catch (modalError) {
                            console.error('Error opening edit modal:', modalError);
                        }
                        
                        // Fetch the data with error handling
                        console.log('Fetching data from: ' + url);
                        fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json, text/html, */*'
                            }
                        })
                        .then(function(response) {
                            if (!response.ok) {
                                throw new Error('HTTP error ' + response.status);
                            }
                            const contentType = response.headers.get('content-type');
                            if (contentType && contentType.includes('application/json')) {
                                return response.json();
                            } else {
                                return response.text();
                            }
                        })
                        .then(function(data) {
                            console.log('Received data from server');
                            
                            // Handle JSON response
                            if (data && typeof data === 'object' && data.success && data.html) {
                                console.log('Received JSON response with HTML content');
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(data.html, 'text/html');
                                const formContent = doc.querySelector('#formContent');
                                
                                if (formContent) {
                                    console.log('Found form content in JSON response');
                                    modalBody.innerHTML = '';
                                    const clonedContent = formContent.cloneNode(true);
                                    modalBody.appendChild(clonedContent);
                                    
                                    // Find the form inside the content and explicitly set its ID
                                    const formInContent = modalBody.querySelector('form');
                                    if (formInContent) {
                                        const formId = modalElement.id.replace('Modal', 'Form');
                                        formInContent.id = formId;
                                        console.log(`Set form ID to ${formId}`);
                                    }
                                } else {
                                    console.log('Using full HTML from JSON response');
                                    modalBody.innerHTML = data.html;
                                }
                            } 
                            // Handle HTML response
                            else if (typeof data === 'string') {
                                console.log('Received HTML response, parsing HTML');
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(data, 'text/html');
                                const formContent = doc.querySelector('#formContent');
                                
                                if (formContent) {
                                    console.log('Found form content, updating modal');
                                    modalBody.innerHTML = '';
                                    // Clone the content instead of moving it
                                    const clonedContent = formContent.cloneNode(true);
                                    modalBody.appendChild(clonedContent);
                                } else {
                                    console.warn('No #formContent found in response, using full response');
                                    modalBody.innerHTML = data;
                                }
                            } else {
                                console.error('Received unexpected response format');
                                throw new Error('Unexpected response format');
                            }
                            
                            // Initialize components
                            initializeFormComponents(modalBody);
                            
                            // Make sure the form ID and submit button are properly connected
                            const loadedForm = modalBody.querySelector('form');
                            const submitBtn = modalElement.querySelector('.submit-modal-form');
                            
                            if (loadedForm && submitBtn) {
                                // Always ensure the form has the expected ID
                                const formId = submitBtn.getAttribute('data-form-id');
                                if (formId) {
                                    console.log('Setting form ID to:', formId);
                                    loadedForm.id = formId;
                                    
                                    // Also set a data attribute for redundancy
                                    loadedForm.setAttribute('data-form-id', formId);
                                }
                                
                                // Ensure all inline scripts from the loaded form have executed
                                setTimeout(() => {
                                    // Always re-enable the submit button after loading
                                    submitBtn.disabled = false;
                                    console.log('Form successfully loaded and connected to button');
                                    
                                    // Double-check form ID one last time
                                    if (formId && !loadedForm.id) {
                                        loadedForm.id = formId;
                                    }
                                }, 300); // Give a short delay to allow inline scripts to run
                            }
                        })
                        .catch(function(error) {
                            console.error('Error loading form data:', error);
                            modalBody.innerHTML = '<div class="alert alert-danger">Error loading form data: ' + error.message + '. Please try again.</div>';
                            setTimeout(function() {
                                modalBody.innerHTML = originalContent;
                            }, 3000);
                        });
                    } else {
                        console.error('Modal element or URL not found: ' + modalId + ', ' + url);
                    }
                });
            });
        }
    } catch (error) {
        console.error('Error setting up edit buttons:', error);
    }

    // Deletion confirmation
    try {
        const deleteButtons = document.querySelectorAll('[data-delete-confirm]');
        console.log('Found ' + deleteButtons.length + ' delete buttons');
        
        if (deleteButtons.length > 0) {
            deleteButtons.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const message = this.getAttribute('data-delete-confirm');
                    const form = this.closest('form');
                    
                    if (confirm(message || 'Are you sure you want to delete this item?')) {
                        console.log('Delete confirmed, submitting form');
                        if (form) {
                            form.submit();
                        } else {
                            console.error('Form not found for delete button');
                        }
                    } else {
                        console.log('Delete canceled');
                    }
                });
            });
        }
    } catch (error) {
        console.error('Error setting up delete buttons:', error);
    }

    // Auto-open modal if there are validation errors
    try {
        function autoOpenModalWithErrors() {
            const errors = document.querySelectorAll('.is-invalid');
            console.log('Found ' + errors.length + ' validation errors');
            
            if (errors.length > 0) {
                const formWithErrors = errors[0].closest('form');
                if (formWithErrors) {
                    const modalElement = formWithErrors.closest('.modal');
                    const modalId = modalElement ? modalElement.id : null;
                    
                    if (modalId) {
                        console.log('Found form with errors in modal: ' + modalId);
                        try {
                            const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                            modal.show();
                            console.log('Auto-opened modal with errors: ' + modalId);
                        } catch (modalError) {
                            console.error('Error auto-opening modal with errors:', modalError);
                        }
                    } else {
                        console.log('Form with errors is not in a modal');
                    }
                } else {
                    console.log('No form found for validation errors');
                }
            }
        }
        
        autoOpenModalWithErrors();
    } catch (error) {
        console.error('Error in auto-open modal with errors:', error);
    }

    // Helper function to initialize form components after dynamic loading
    function initializeFormComponents(container) {
        try {
            console.log('Initializing form components');
            
            // Re-enable the submit button if it was disabled
            const modal = container.closest('.modal');
            if (modal) {
                const submitBtn = modal.querySelector('.submit-modal-form');
                if (submitBtn && submitBtn.disabled) {
                    console.log('Re-enabling submit button');
                    submitBtn.disabled = false;
                }
            }
            
            // Initialize category dropdown for product forms
            const categorySelect = container.querySelector('select[name="category_id"]');
            const subcategorySelect = container.querySelector('select[name="subcategory_id"]');
            
            if (categorySelect && subcategorySelect) {
                categorySelect.addEventListener('change', function() {
                    const categoryId = this.value;
                    
                    // Clear existing options except the first one
                    while (subcategorySelect.options.length > 1) {
                        subcategorySelect.remove(1);
                    }
                    
                    if (categoryId) {
                        // Show loading indicator
                        const loadingOption = document.createElement('option');
                        loadingOption.textContent = 'Loading...';
                        loadingOption.disabled = true;
                        subcategorySelect.appendChild(loadingOption);
                        
                        // Fetch subcategories
                        fetch(`/admin/subcategories/${categoryId}`)
                            .then(response => response.json())
                            .then(data => {
                                // Remove loading option
                                subcategorySelect.remove(subcategorySelect.options.length - 1);
                                
                                if (data.success && data.subcategories) {
                                    data.subcategories.forEach(subcategory => {
                                        const option = document.createElement('option');
                                        option.value = subcategory.id;
                                        option.textContent = subcategory.name;
                                        subcategorySelect.appendChild(option);
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error loading subcategories:', error);
                                subcategorySelect.remove(subcategorySelect.options.length - 1);
                            });
                    }
                });
                console.log('Category-Subcategory relationship initialized');
            }
            
            // Example: initialize select2
            if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
                $(container).find('.select2').select2({
                    dropdownParent: $(container).closest('.modal')
                });
                console.log('Select2 components initialized');
            }
            
            // Example: initialize datepicker
            if (typeof $ !== 'undefined' && $.fn && $.fn.datepicker) {
                $(container).find('.datepicker').datepicker();
                console.log('Datepicker components initialized');
            }
            
            // Example: initialize rich text editor
            if (typeof tinymce !== 'undefined') {
                $(container).find('.tinymce').each(function() {
                    tinymce.init({
                        selector: '#' + this.id,
                        height: 300,
                        menubar: false,
                        plugins: [
                            'advlist autolink lists link image charmap print preview anchor',
                            'searchreplace visualblocks code fullscreen',
                            'insertdatetime media table paste code help wordcount'
                        ],
                        toolbar: 'undo redo | formatselect | bold italic backcolor | \
                        alignleft aligncenter alignright alignjustify | \
                        bullist numlist outdent indent | removeformat | help'
                    });
                });
                console.log('TinyMCE components initialized');
            }
        } catch (error) {
            console.error('Error initializing form components:', error);
        }
    }

    // Function to initialize or reinitialize all event handlers
    function initializeEventHandlers() {
        console.log('Initializing all event handlers');
        
        // Initialize delete confirmation buttons
        try {
            const deleteButtons = document.querySelectorAll('[data-delete-confirm]');
            console.log('Found ' + deleteButtons.length + ' delete buttons');
            
            if (deleteButtons.length > 0) {
                deleteButtons.forEach(function(button) {
                    // Remove existing event handlers to prevent duplicates
                    button.removeEventListener('click', handleDeleteConfirm);
                    // Add event handler
                    button.addEventListener('click', handleDeleteConfirm);
                });
            }
        } catch (error) {
            console.error('Error setting up delete buttons:', error);
        }
        
        // Initialize edit buttons
        try {
            const editButtons = document.querySelectorAll('[data-edit-url]');
            console.log('Found ' + editButtons.length + ' edit buttons');
            
            if (editButtons.length > 0) {
                editButtons.forEach(function(button) {
                    // Remove existing event handlers to prevent duplicates
                    button.removeEventListener('click', handleEditButton);
                    // Add event handler
                    button.addEventListener('click', handleEditButton);
                });
            }
        } catch (error) {
            console.error('Error setting up edit buttons:', error);
        }
        
        // Initialize modal open buttons
        try {
            const openModalButtons = document.querySelectorAll('[data-open-modal]');
            console.log('Found ' + openModalButtons.length + ' open modal buttons');
            
            if (openModalButtons.length > 0) {
                openModalButtons.forEach(function(button) {
                    // Remove existing event handlers to prevent duplicates
                    button.removeEventListener('click', handleOpenModal);
                    // Add event handler
                    button.addEventListener('click', handleOpenModal);
                });
            }
        } catch (error) {
            console.error('Error setting up open modal buttons:', error);
        }
    }
    
    // Handler for delete confirmation
    function handleDeleteConfirm(e) {
        e.preventDefault();
        
        const message = this.getAttribute('data-delete-confirm');
        const form = this.closest('form');
        
        if (confirm(message || 'Are you sure you want to delete this item?')) {
            console.log('Delete confirmed, submitting form');
            if (form) {
                form.submit();
            } else {
                console.error('Form not found for delete button');
            }
        } else {
            console.log('Delete canceled');
        }
    }
    
    // Handler for edit buttons
    function handleEditButton() {
        const url = this.getAttribute('data-edit-url');
        const modalId = this.getAttribute('data-open-modal');
        
        if (url && modalId) {
            const modalElement = document.getElementById(modalId);
            
            if (modalElement) {
                const modalBody = modalElement.querySelector('.modal-body');
                const originalContent = modalBody.innerHTML;
                
                console.log('Loading content from:', url, 'into modal:', modalId);
                
                // Show loading indicator
                modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading form data...</p></div>';
                
                // Show the modal
                const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                modal.show();
                
                // Make the AJAX request using fetch
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('AJAX request successful');
                    
                    if (data && data.success && data.html) {
                        // Insert the HTML
                        modalBody.innerHTML = data.html;
                        
                        // Find the form and setup its properties
                        const loadedForm = modalBody.querySelector('form');
                        const submitBtn = modalElement.querySelector('.submit-modal-form');
                        
                        if (loadedForm && submitBtn) {
                            const formId = submitBtn.getAttribute('data-form-id');
                            
                            if (formId) {
                                console.log('Setting form ID to:', formId);
                                loadedForm.id = formId;
                                loadedForm.setAttribute('data-form-id', formId);
                            }
                            
                            // Initialize form components
                            setTimeout(() => {
                                initializeFormComponents(modalBody);
                                submitBtn.disabled = false;
                                console.log('Form successfully loaded and connected to button');
                            }, 300);
                        }
                    } else {
                        modalBody.innerHTML = '<div class="alert alert-danger">Invalid response format from server.</div>';
                        setTimeout(function() {
                            modalBody.innerHTML = originalContent;
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('AJAX request error:', error);
                    modalBody.innerHTML = '<div class="alert alert-danger">Error loading form: ' + error.message + '</div>';
                    setTimeout(function() {
                        modalBody.innerHTML = originalContent;
                    }, 3000);
                });
            }
        }
    }
    
    // Handler for open modal buttons
    function handleOpenModal() {
        const modalId = this.getAttribute('data-open-modal');
        
        if (modalId) {
            const modalElement = document.getElementById(modalId);
            
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                modal.show();
                console.log('Opened modal:', modalId);
            }
        }
    }
});

// Define global functions for event handling
window.handleDeleteConfirm = function(e) {
    e.preventDefault();
    
    const message = this.getAttribute('data-delete-confirm');
    const form = this.closest('form');
    
    if (confirm(message || 'Are you sure you want to delete this item?')) {
        console.log('Delete confirmed, submitting form');
        if (form) {
            form.submit();
        } else {
            console.error('Form not found for delete button');
        }
    } else {
        console.log('Delete canceled');
    }
};

window.handleEditButton = function() {
    const url = this.getAttribute('data-edit-url');
    const modalId = this.getAttribute('data-open-modal');
    
    if (url && modalId) {
        const modalElement = document.getElementById(modalId);
        
        if (modalElement) {
            const modalBody = modalElement.querySelector('.modal-body');
            const originalContent = modalBody.innerHTML;
            
            console.log('Loading content from:', url, 'into modal:', modalId);
            
            // Show loading indicator
            modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading form data...</p></div>';
            
            // Show the modal
            const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            modal.show();
            
            // Make the AJAX request using fetch
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('AJAX request successful');
                
                if (data && data.success && data.html) {
                    // Insert the HTML
                    modalBody.innerHTML = data.html;
                    
                    // Find the form and setup its properties
                    const loadedForm = modalBody.querySelector('form');
                    const submitBtn = modalElement.querySelector('.submit-modal-form');
                    
                    if (loadedForm && submitBtn) {
                        const formId = submitBtn.getAttribute('data-form-id');
                        
                        if (formId) {
                            console.log('Setting form ID to:', formId);
                            loadedForm.id = formId;
                            loadedForm.setAttribute('data-form-id', formId);
                        }
                        
                        // Initialize form components
                        setTimeout(() => {
                            window.initializeFormComponents(modalBody);
                            submitBtn.disabled = false;
                            console.log('Form successfully loaded and connected to button');
                        }, 300);
                    }
                } else {
                    modalBody.innerHTML = '<div class="alert alert-danger">Invalid response format from server.</div>';
                    setTimeout(function() {
                        modalBody.innerHTML = originalContent;
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('AJAX request error:', error);
                modalBody.innerHTML = '<div class="alert alert-danger">Error loading form: ' + error.message + '</div>';
                setTimeout(function() {
                    modalBody.innerHTML = originalContent;
                }, 3000);
            });
        }
    }
};

window.handleOpenModal = function() {
    const modalId = this.getAttribute('data-open-modal');
    
    if (modalId) {
        const modalElement = document.getElementById(modalId);
        
        if (modalElement) {
            const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            modal.show();
            console.log('Opened modal:', modalId);
        }
    }
};

// Function to initialize or reinitialize all event handlers
window.initializeEventHandlers = function() {
    console.log('Initializing all event handlers');
    
    // Initialize delete confirmation buttons
    try {
        const deleteButtons = document.querySelectorAll('[data-delete-confirm]');
        console.log('Found ' + deleteButtons.length + ' delete buttons');
        
        if (deleteButtons.length > 0) {
            deleteButtons.forEach(function(button) {
                // Remove existing event handlers to prevent duplicates
                button.removeEventListener('click', window.handleDeleteConfirm);
                // Add event handler
                button.addEventListener('click', window.handleDeleteConfirm);
            });
        }
    } catch (error) {
        console.error('Error setting up delete buttons:', error);
    }
    
    // Initialize edit buttons
    try {
        const editButtons = document.querySelectorAll('[data-edit-url]');
        console.log('Found ' + editButtons.length + ' edit buttons');
        
        if (editButtons.length > 0) {
            editButtons.forEach(function(button) {
                // Remove existing event handlers to prevent duplicates
                button.removeEventListener('click', window.handleEditButton);
                // Add event handler
                button.addEventListener('click', window.handleEditButton);
            });
        }
    } catch (error) {
        console.error('Error setting up edit buttons:', error);
    }
    
    // Initialize modal open buttons
    try {
        const openModalButtons = document.querySelectorAll('[data-open-modal]');
        console.log('Found ' + openModalButtons.length + ' open modal buttons');
        
        if (openModalButtons.length > 0) {
            openModalButtons.forEach(function(button) {
                // Remove existing event handlers to prevent duplicates
                button.removeEventListener('click', window.handleOpenModal);
                // Add event handler
                button.addEventListener('click', window.handleOpenModal);
            });
        }
    } catch (error) {
        console.error('Error setting up open modal buttons:', error);
    }
};

// Add a global function to reinitialize event handlers (for use after AJAX content loads)
window.reinitializeEventHandlers = function() {
    if (typeof window.initializeEventHandlers === 'function') {
        window.initializeEventHandlers();
    } else {
        console.warn('initializeEventHandlers function not available');
        // Fallback: reload the page
        window.location.reload();
    }
};

// Make form component initialization function global
window.initializeFormComponents = function(container) {
    try {
        console.log('Initializing form components');
        
        // Re-enable the submit button if it was disabled
        const modal = container.closest('.modal');
        if (modal) {
            const submitBtn = modal.querySelector('.submit-modal-form');
            if (submitBtn && submitBtn.disabled) {
                console.log('Re-enabling submit button');
                submitBtn.disabled = false;
            }
        }
        
        // Initialize category dropdown for product forms
        const categorySelect = container.querySelector('select[name="category_id"]');
        const subcategorySelect = container.querySelector('select[name="subcategory_id"]');
        
        if (categorySelect && subcategorySelect) {
            categorySelect.addEventListener('change', function() {
                const categoryId = this.value;
                
                // Clear existing options except the first one
                while (subcategorySelect.options.length > 1) {
                    subcategorySelect.remove(1);
                }
                
                if (categoryId) {
                    // Show loading indicator
                    const loadingOption = document.createElement('option');
                    loadingOption.textContent = 'Loading...';
                    loadingOption.disabled = true;
                    subcategorySelect.appendChild(loadingOption);
                    
                    // Fetch subcategories
                    fetch(`/admin/subcategories/${categoryId}`)
                        .then(response => response.json())
                        .then(data => {
                            // Remove loading option
                            subcategorySelect.remove(subcategorySelect.options.length - 1);
                            
                            if (data.success && data.subcategories) {
                                data.subcategories.forEach(subcategory => {
                                    const option = document.createElement('option');
                                    option.value = subcategory.id;
                                    option.textContent = subcategory.name;
                                    subcategorySelect.appendChild(option);
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error loading subcategories:', error);
                            subcategorySelect.remove(subcategorySelect.options.length - 1);
                        });
                }
            });
            console.log('Category-Subcategory relationship initialized');
        }
        
        // Example: initialize select2
        if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
            $(container).find('.select2').select2({
                dropdownParent: $(container).closest('.modal')
            });
            console.log('Select2 components initialized');
        }
        
        // Example: initialize datepicker
        if (typeof $ !== 'undefined' && $.fn && $.fn.datepicker) {
            $(container).find('.datepicker').datepicker();
            console.log('Datepicker components initialized');
        }
        
        // Example: initialize rich text editor
        if (typeof tinymce !== 'undefined') {
            $(container).find('.tinymce').each(function() {
                tinymce.init({
                    selector: '#' + this.id,
                    height: 300,
                    menubar: false,
                    plugins: [
                        'advlist autolink lists link image charmap print preview anchor',
                        'searchreplace visualblocks code fullscreen',
                        'insertdatetime media table paste code help wordcount'
                    ],
                    toolbar: 'undo redo | formatselect | bold italic backcolor | \
                    alignleft aligncenter alignright alignjustify | \
                    bullist numlist outdent indent | removeformat | help'
                });
            });
            console.log('TinyMCE components initialized');
        }
        
        // Reinitialize event handlers for newly loaded content
        window.initializeEventHandlers();
    } catch (error) {
        console.error('Error initializing form components:', error);
    }
};