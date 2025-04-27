<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }
        .company-info {
            text-align: right;
        }
        .invoice-details {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
        }
        .customer-info, .order-info {
            width: 48%;
        }
        .invoice-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .invoice-items th, .invoice-items td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .invoice-items th {
            background-color: #f9f9f9;
        }
        .invoice-totals {
            width: 300px;
            margin-left: auto;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .grand-total {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }
        .invoice-footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                font-size: 12px;
            }
            .invoice-container {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Print Button -->
        <div class="no-print" style="text-align: right; margin-bottom: 20px;">
            <button onclick="window.print();" style="padding: 8px 16px; background: #4e73df; color: white; border: none; border-radius: 4px; cursor: pointer;">
                <i class="fas fa-print"></i> Print Invoice
            </button>
            <a href="{{ route('admin.orders.index') }}" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; text-decoration: none; margin-left: 10px;">
                Back to Orders
            </a>
        </div>

        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="logo">
                <h1>{{ config('app.name') }}</h1>
                <p>Invoice</p>
            </div>
            <div class="company-info">
                <h3>{{ config('app.name') }}</h3>
                <p>123 Company Street</p>
                <p>City, State 12345</p>
                <p>Phone: (123) 456-7890</p>
                <p>Email: info@example.com</p>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="customer-info">
                <h3>Bill To</h3>
                <p><strong>{{ $order->customer->full_name }}</strong></p>
                <p>{!! nl2br(e($order->shipping_address)) !!}</p>
                <p>Email: {{ $order->customer->email }}</p>
                <p>Phone: {{ $order->customer->phone }}</p>
            </div>
            <div class="order-info">
                <h3>Invoice Details</h3>
                <p><strong>Invoice #:</strong> INV-{{ $order->order_number }}</p>
                <p><strong>Order #:</strong> {{ $order->order_number }}</p>
                <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                <p><strong>Status:</strong> <span class="badge {{ $order->status_badge }}">{{ ucfirst($order->status) }}</span></p>
                <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
                <p><strong>Payment Status:</strong> <span class="badge {{ $order->payment_status_badge }}">{{ ucfirst($order->payment_status) }}</span></p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="invoice-items">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td>${{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="invoice-totals">
            <div class="total-row">
                <div>Subtotal:</div>
                <div>${{ number_format($order->subtotal, 2) }}</div>
            </div>
            <div class="total-row">
                <div>Shipping:</div>
                <div>${{ number_format($order->shipping, 2) }}</div>
            </div>
            <div class="total-row">
                <div>Tax:</div>
                <div>${{ number_format($order->tax, 2) }}</div>
            </div>
            @if($order->discount > 0)
            <div class="total-row">
                <div>Discount:</div>
                <div>-${{ number_format($order->discount, 2) }}</div>
            </div>
            @endif
            <div class="total-row grand-total">
                <div>TOTAL:</div>
                <div>${{ number_format($order->total, 2) }}</div>
            </div>
        </div>

        <!-- Notes & Terms -->
        <div style="margin-top: 30px;">
            <h4>Notes</h4>
            <p>Thank you for your business! All items are subject to our return policy of 30 days.</p>
            <h4>Payment Terms</h4>
            <p>Payment is due upon receipt of invoice.</p>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <p>This invoice was generated by {{ config('app.name') }} on {{ now()->format('M d, Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html> 