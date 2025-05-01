@extends('layouts.admin')

@section('title', 'Create Return Request')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Return Request</h1>
        <a href="{{ route('admin.returns.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Back to Returns
        </a>
    </div>

    <!-- Create Return Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Return Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.returns.store') }}" method="POST" id="returnForm">
                @csrf
                
                <div class="row mb-3">
                    <!-- Order Selection -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="order_id">Select Order <span class="text-danger">*</span></label>
                            <select class="form-select @error('order_id') is-invalid @enderror" 
                                id="order_id" name="order_id" required>
                                <option value="">-- Select Order --</option>
                                @foreach($orders as $order)
                                    <option value="{{ $order->id }}" {{ (old('order_id') == $order->id || request('order_id') == $order->id) ? 'selected' : '' }}>
                                        #{{ $order->order_number }} - {{ $order->customer->full_name }} ({{ $order->created_at->format('M d, Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('order_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Customer ID (hidden) -->
                    <input type="hidden" id="customer_id" name="customer_id" value="{{ old('customer_id') }}">
                </div>
                
                <!-- Order Details Placeholder -->
                <div id="orderDetails" class="mb-4" style="{{ old('order_id') ? '' : 'display: none;' }}">
                    <div class="card">
                        <div class="card-header py-2 bg-light">
                            <h6 class="m-0 font-weight-bold">Order Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <strong>Order #:</strong> <span id="orderNumber"></span>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <strong>Date:</strong> <span id="orderDate"></span>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <strong>Status:</strong> <span id="orderStatus"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <strong>Customer:</strong> <span id="customerName"></span>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <strong>Total:</strong> <span id="orderTotal"></span>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <strong>Items:</strong> <span id="itemCount"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Return Items -->
                <div id="returnItems" style="{{ old('order_id') ? '' : 'display: none;' }}">
                    <h5 class="mb-3">Return Items</h5>
                    
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered" id="itemsTable">
                            <thead>
                                <tr>
                                    <th width="5%">Select</th>
                                    <th width="40%">Product</th>
                                    <th width="10%">Price</th>
                                    <th width="10%">Ordered</th>
                                    <th width="10%">Returned</th>
                                    <th width="10%">Available</th>
                                    <th width="15%">Return Quantity</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <!-- Items will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div id="returnItemsContainer">
                        <!-- Selected items details will be added here -->
                    </div>
                </div>
                
                <!-- Return Reason -->
                <div class="form-group mb-3">
                    <label for="reason">Return Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('reason') is-invalid @enderror" 
                        id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                    @error('reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Admin Notes -->
                <div class="form-group mb-3">
                    <label for="admin_notes">Admin Notes</label>
                    <textarea class="form-control @error('admin_notes') is-invalid @enderror" 
                        id="admin_notes" name="admin_notes" rows="2">{{ old('admin_notes') }}</textarea>
                    @error('admin_notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <hr>
                
                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        <i class="fas fa-save me-1"></i> Create Return Request
                    </button>
                    <a href="{{ route('admin.returns.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        console.log("Return creation form loaded - Debug version");
        
        // When order is selected
        $('#order_id').change(function() {
            const orderId = $(this).val();
            
            if (orderId) {
                // Clear previous item selection
                $('#returnItemsContainer').empty();
                $('#itemsTableBody').empty();
                
                // Show loading state
                $('#itemsTableBody').html('<tr><td colspan="7" class="text-center">Loading order items...</td></tr>');
                $('#orderDetails').show();
                $('#returnItems').show();
                
                // Get order details and items via AJAX
                $.ajax({
                    url: `/admin/orders/${orderId}`,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        console.log("Order details response:", response);
                        // Set hidden customer ID field
                        $('#customer_id').val(response.customer_id);
                        
                        // Update order details
                        $('#orderNumber').text(response.order_number);
                        var rawDate = response.updated_at;
                        var formattedDate = new Date(rawDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
                        $('#orderDate').text(formattedDate);
                        // $('#orderStatus').html(`<span class="badge bg-${response.status_badge_color} text-white">${response.status}</span>`);
                        $('#orderStatus').text(response.status);
                        $('#customerName').text(response.first_name + ' ' + response.last_name);
                        $('#orderTotal').text(`$${response.total}`);
                        $('#itemCount').text(response.quantity);
                        
                        // Get returnable items via AJAX
                        // Try both route helper and direct URL construction
                        const routeUrl = `{{ route('admin.returns.get-order-items', ['order' => '__id__']) }}`.replace('__id__', orderId);
                        // Use a more explicit direct URL construction with base URL
                        const baseUrl = '{{ url('/') }}';
                        const directUrl = `${baseUrl}/admin/returns/get-order-items/${orderId}`;
                        
                        console.log("Route URL:", routeUrl);
                        console.log("Direct URL:", directUrl);
                        
                        $.ajax({
                            type: 'GET',
                            url: directUrl, // Use the direct URL construction
                            dataType: 'json',
                            success: function(data) {
                                console.log("Received data:", data);
                                // Clear loading state
                                $('#itemsTableBody').empty();
                                
                                if (data.items && data.items.length > 0) {
                                    // Add items to table
                                    $.each(data.items, function(index, item) {
                                        if (item.available_quantity > 0) {
                                            const row = `
                                                <tr>
                                                    <td class="text-center">
                                                        <div class="form-check">
                                                            <input class="form-check-input item-checkbox" type="checkbox" 
                                                                   data-item-id="${item.id}" 
                                                                   data-product-name="${item.product_name}" 
                                                                   data-available="${item.available_quantity}"
                                                                   data-price="${item.price}">
                                                        </div>
                                                    </td>
                                                    <td>${item.product_name}</td>
                                                    <td>$${parseFloat(item.price).toFixed(2)}</td>
                                                    <td>${item.quantity}</td>
                                                    <td>${item.returned_quantity}</td>
                                                    <td>${item.available_quantity}</td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm quantity-input" 
                                                               min="1" max="${item.available_quantity}" value="1" disabled
                                                               data-item-id="${item.id}">
                                                    </td>
                                                </tr>
                                            `;
                                            $('#itemsTableBody').append(row);
                                        }
                                    });
                                    
                                    // Enable/disable submit button based on selection
                                    $('.item-checkbox').change(function() {
                                        const itemId = $(this).data('item-id');
                                        const isChecked = $(this).prop('checked');
                                        
                                        // Enable/disable quantity input
                                        $(`.quantity-input[data-item-id="${itemId}"]`).prop('disabled', !isChecked);
                                        
                                        // Update form submission capability
                                        updateFormSubmissionState();
                                        
                                        // Add or remove hidden fields for selected items
                                        if (isChecked) {
                                            addItemToForm(itemId);
                                        } else {
                                            removeItemFromForm(itemId);
                                        }
                                    });
                                    
                                    // Update form when quantity changes
                                    $(document).on('change', '.quantity-input', function() {
                                        const itemId = $(this).data('item-id');
                                        const quantity = $(this).val();
                                        
                                        // Update hidden quantity field
                                        $(`input[name="items[${itemId}][quantity]"]`).val(quantity);
                                    });
                                    
                                } else {
                                    $('#itemsTableBody').html('<tr><td colspan="7" class="text-center">No returnable items found for this order.</td></tr>');
                                    $('#submitBtn').prop('disabled', true);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("AJAX Error:", status, error);
                                console.error("Response:", xhr.responseText);
                                $('#itemsTableBody').html('<tr><td colspan="7" class="text-center text-danger">Error loading order items. Please try again.</td></tr>');
                            }
                        });
                    },
                    error: function(xhr) {
                        $('#orderDetails, #returnItems').hide();
                        alert('Error loading order details. Please try again.');
                    }
                });
            } else {
                // Hide and reset sections when no order is selected
                $('#orderDetails, #returnItems').hide();
                $('#returnItemsContainer').empty();
                $('#customer_id').val('');
                $('#submitBtn').prop('disabled', true);
            }
        });
        
        // Function to add item to form
        function addItemToForm(itemId) {
            const checkbox = $(`.item-checkbox[data-item-id="${itemId}"]`);
            const productName = checkbox.data('product-name');
            const quantity = $(`.quantity-input[data-item-id="${itemId}"]`).val();
            
            // Create container for this item if it doesn't exist
            if ($(`#item-container-${itemId}`).length === 0) {
                const itemHtml = `
                    <div id="item-container-${itemId}" class="card mb-3">
                        <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center">
                            <h6 class="m-0">${productName}</h6>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-item" data-item-id="${itemId}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="items[${itemId}][order_item_id]" value="${itemId}">
                            <input type="hidden" name="items[${itemId}][quantity]" value="${quantity}">
                            
                            <div class="mb-3">
                                <label for="items[${itemId}][condition]" class="form-label">Condition</label>
                                <select name="items[${itemId}][condition]" class="form-select">
                                    <option value="new">New (Unopened)</option>
                                    <option value="used" selected>Used (Opened)</option>
                                    <option value="damaged">Damaged</option>
                                </select>
                            </div>
                            
                            <div class="mb-0">
                                <label for="items[${itemId}][reason]" class="form-label">Reason for Return</label>
                                <select name="items[${itemId}][reason]" class="form-select">
                                    <option value="Wrong item">Wrong item received</option>
                                    <option value="Defective">Defective or does not work</option>
                                    <option value="Not as described">Item not as described</option>
                                    <option value="Changed mind">Changed mind</option>
                                    <option value="Other">Other reason</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#returnItemsContainer').append(itemHtml);
                
                // Add remove button functionality
                $(`#item-container-${itemId} .remove-item`).click(function() {
                    const itemId = $(this).data('item-id');
                    $(`.item-checkbox[data-item-id="${itemId}"]`).prop('checked', false).trigger('change');
                });
            }
        }
        
        // Function to remove item from form
        function removeItemFromForm(itemId) {
            $(`#item-container-${itemId}`).remove();
        }
        
        // Function to update form submission state
        function updateFormSubmissionState() {
            const hasSelectedItems = $('.item-checkbox:checked').length > 0;
            $('#submitBtn').prop('disabled', !hasSelectedItems);
        }
        
        // Trigger change event if order ID is already set (for error handling)
        if ($('#order_id').val()) {
            $('#order_id').trigger('change');
        }
    });
</script>
@endsection