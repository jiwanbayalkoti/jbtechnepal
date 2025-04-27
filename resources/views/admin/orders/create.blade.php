@extends('layouts.admin')

@section('title', 'Create New Order')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Create New Order</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.orders.store') }}" method="POST" id="orderForm">
                @csrf
                
                <div class="row">
                    <!-- Customer Information -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Customer Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Select Customer</label>
                                    <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                        <option value="">-- Select Customer --</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} ({{ $customer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="shipping_address" class="form-label">Shipping Address</label>
                                    <textarea class="form-control @error('shipping_address') is-invalid @enderror" id="shipping_address" name="shipping_address" rows="3">{{ old('shipping_address') }}</textarea>
                                    @error('shipping_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="billing_address" class="form-label">Billing Address</label>
                                    <textarea class="form-control @error('billing_address') is-invalid @enderror" id="billing_address" name="billing_address" rows="3">{{ old('billing_address') }}</textarea>
                                    @error('billing_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="same_as_shipping" checked>
                                    <label class="form-check-label" for="same_as_shipping">
                                        Same as shipping address
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Order Details -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Order Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Order Status</label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                                <option value="shipped" {{ old('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                                <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="payment_method" class="form-label">Payment Method</label>
                                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method">
                                                <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                                <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                                            </select>
                                            @error('payment_method')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="payment_status" class="form-label">Payment Status</label>
                                            <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status">
                                                <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                                <option value="failed" {{ old('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                                <option value="refunded" {{ old('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                            </select>
                                            @error('payment_status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="shipping_method" class="form-label">Shipping Method</label>
                                            <select class="form-select @error('shipping_method') is-invalid @enderror" id="shipping_method" name="shipping_method">
                                                <option value="standard" {{ old('shipping_method') == 'standard' ? 'selected' : '' }}>Standard Shipping</option>
                                                <option value="express" {{ old('shipping_method') == 'express' ? 'selected' : '' }}>Express Shipping</option>
                                                <option value="pickup" {{ old('shipping_method') == 'pickup' ? 'selected' : '' }}>Store Pickup</option>
                                            </select>
                                            @error('shipping_method')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Order Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product Selection -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Order Items</h6>
                                <button type="button" class="btn btn-sm btn-primary" id="addProductBtn">
                                    <i class="fas fa-plus me-1"></i>Add Product
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="productTable">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th width="100">Price</th>
                                                <th width="80">Quantity</th>
                                                <th width="120">Total</th>
                                                <th width="50">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productTableBody">
                                            <!-- Products will be added dynamically -->
                                            <tr id="noProductsRow">
                                                <td colspan="5" class="text-center text-muted">No products added yet</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Order Summary -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Order Summary</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="subtotal">$0.00</span>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="shipping_cost" class="form-label">Shipping Cost</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="shipping_cost" name="shipping_cost" value="{{ old('shipping_cost', 0) }}" min="0" step="0.01">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', 0) }}" min="0" max="100" step="0.01">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="discount" class="form-label">Discount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="discount" name="discount" value="{{ old('discount', 0) }}" min="0" step="0.01">
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>Total:</strong>
                                    <strong id="totalAmount">$0.00</strong>
                                    <input type="hidden" name="total_amount" id="totalAmountInput" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Create Order
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary ms-2">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Product Selection Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Select Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="productSearch" placeholder="Search products...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="productSelectionTable">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}">
                                <td>
                                    @if($product->images && $product->images->count() > 0)
                                        <img src="{{ asset('storage/' . $product->images->first()->path) }}" alt="{{ $product->name }}" width="50" height="50" style="object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                                <td>${{ number_format($product->price, 2) }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary select-product">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle "Same as shipping" checkbox
        $('#same_as_shipping').change(function() {
            if ($(this).is(':checked')) {
                $('#billing_address').val($('#shipping_address').val());
                $('#billing_address').prop('disabled', true);
            } else {
                $('#billing_address').prop('disabled', false);
            }
        });

        $('#shipping_address').on('input', function() {
            if ($('#same_as_shipping').is(':checked')) {
                $('#billing_address').val($(this).val());
            }
        });
        
        // Initialize total calculations
        const calculateTotals = function() {
            let subtotal = 0;
            $('.product-row').each(function() {
                const rowTotal = parseFloat($(this).find('.product-total').data('total') || 0);
                subtotal += rowTotal;
            });
            
            const shippingCost = parseFloat($('#shipping_cost').val() || 0);
            const taxRate = parseFloat($('#tax_rate').val() || 0) / 100;
            const discount = parseFloat($('#discount').val() || 0);
            
            const tax = subtotal * taxRate;
            const total = subtotal + shippingCost + tax - discount;
            
            $('#subtotal').text('$' + subtotal.toFixed(2));
            $('#totalAmount').text('$' + total.toFixed(2));
            $('#totalAmountInput').val(total.toFixed(2));
            
            // Hide "no products" row if we have products
            if ($('.product-row').length > 0) {
                $('#noProductsRow').hide();
            } else {
                $('#noProductsRow').show();
            }
        };
        
        // Handle product modal
        $('#addProductBtn').click(function() {
            $('#productModal').modal('show');
        });
        
        // Product search
        $('#productSearch').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#productSelectionTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
        
        // Select product from modal
        $(document).on('click', '.select-product', function() {
            const row = $(this).closest('tr');
            const productId = row.data('id');
            const productName = row.data('name');
            const productPrice = parseFloat(row.data('price'));
            
            // Check if product already exists in table
            if ($('#product-' + productId).length > 0) {
                // Increment quantity
                const qtyInput = $('#product-' + productId).find('.product-qty');
                const currentQty = parseInt(qtyInput.val());
                qtyInput.val(currentQty + 1).trigger('change');
            } else {
                // Add new row
                const newRow = `
                    <tr id="product-${productId}" class="product-row">
                        <td>
                            ${productName}
                            <input type="hidden" name="products[${productId}][product_id]" value="${productId}">
                            <input type="hidden" name="products[${productId}][price]" value="${productPrice.toFixed(2)}">
                        </td>
                        <td>$${productPrice.toFixed(2)}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm product-qty" 
                                name="products[${productId}][quantity]" value="1" min="1" max="100">
                        </td>
                        <td>
                            <span class="product-total" data-total="${productPrice.toFixed(2)}">
                                $${productPrice.toFixed(2)}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-product">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#productTableBody').append(newRow);
            }
            
            calculateTotals();
            $('#productModal').modal('hide');
        });
        
        // Handle quantity change
        $(document).on('change', '.product-qty', function() {
            const row = $(this).closest('tr');
            const price = parseFloat(row.find('input[name$="[price]"]').val());
            const quantity = parseInt($(this).val());
            const total = price * quantity;
            
            row.find('.product-total').data('total', total.toFixed(2));
            row.find('.product-total').text('$' + total.toFixed(2));
            
            calculateTotals();
        });
        
        // Handle remove product
        $(document).on('click', '.remove-product', function() {
            $(this).closest('tr').remove();
            calculateTotals();
        });
        
        // Update totals when shipping, tax, or discount changes
        $('#shipping_cost, #tax_rate, #discount').on('input', calculateTotals);
    });
</script>
@endsection 