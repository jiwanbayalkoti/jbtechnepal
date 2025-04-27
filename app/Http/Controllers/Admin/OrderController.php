<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer']);

        // Filter by order number or customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Order by latest first
        $orders = $query->latest()->paginate(15);

        // Get order status counts for statistics
        $statusCounts = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        // Get all customers
        $customers = \App\Models\User::where('is_admin', 0)->orderBy('name')->get();
        
        // Get all products
        $products = \App\Models\Product::with(['category', 'images'])->orderBy('name')->get();
        
        return view('admin.orders.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Create the order
            $order = new Order();
            $order->customer_id = $request->customer_id;
            $order->order_number = 'ORD-' . strtoupper(uniqid());
            $order->status = 'pending';
            $order->payment_method = $request->payment_method;
            $order->payment_status = 'pending';
            $order->shipping_address = $request->shipping_address;
            $order->shipping = $request->shipping ?? 0;
            $order->tax = $request->tax ?? 0;
            $order->discount = $request->discount ?? 0;
            
            // Calculate subtotal and total
            $subtotal = 0;
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['id']);
                $subtotal += ($product->price * $item['quantity']);
            }
            
            $order->subtotal = $subtotal;
            $order->total = $subtotal + $order->shipping + $order->tax - $order->discount;
            $order->save();

            // Add order items
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['id']);
                
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $product->price * $item['quantity'],
                ]);
                
                // Update product inventory if needed
                if ($product->track_inventory) {
                    $product->decrement('quantity', $item['quantity']);
                }
            }

            DB::commit();
            return redirect()->route('admin.orders.index')->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating order: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'items.product', 'returns']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        $order->load(['customer', 'items.product']);
        $customers = Customer::orderBy('first_name')->get();
        $products = Product::orderBy('name')->get();
        
        return view('admin.orders.edit', compact('order', 'customers', 'products'));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
            'payment_status' => 'required|string',
            'shipping_address' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $order->status = $request->status;
            $order->payment_status = $request->payment_status;
            $order->shipping_address = $request->shipping_address;
            $order->shipping = $request->shipping ?? $order->shipping;
            $order->tax = $request->tax ?? $order->tax;
            $order->discount = $request->discount ?? $order->discount;
            
            // Recalculate total
            $order->total = $order->subtotal + $order->shipping + $order->tax - $order->discount;
            $order->save();

            DB::commit();
            return redirect()->route('admin.orders.show', $order)->with('success', 'Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating order: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update order status via AJAX.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,completed,cancelled',
        ]);

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.'
        ]);
    }

    /**
     * Generate invoice for the order.
     */
    public function generateInvoice(Order $order)
    {
        $order->load(['customer', 'items.product']);
        
        // You can use a PDF package like dompdf or barryvdh/laravel-dompdf
        // For now, we'll just return a view
        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Update payment status for an order.
     */
    public function updatePayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $order->payment_status = 'paid';
        $order->payment_reference = $request->payment_reference;
        $order->payment_date = now();
        $order->save();

        return redirect()->route('admin.orders.edit', $order)
                         ->with('success', 'Payment status updated successfully.');
    }

    /**
     * Send email to customer about their order.
     */
    public function sendEmail(Request $request, Order $order)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'include_order_details' => 'nullable|boolean',
        ]);

        // Here you would implement email sending logic
        // For example using Laravel's Mail facade:
        // Mail::to($order->customer->email)
        //     ->send(new OrderUpdateMail($order, $request->subject, $request->message, $request->include_order_details));

        return redirect()->route('admin.orders.edit', $order)
                         ->with('success', 'Email sent to customer successfully.');
    }

    /**
     * Export orders to CSV/Excel.
     */
    public function export(Request $request)
    {
        return Excel::download(new OrdersExport($request), 'orders-' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order)
    {
        try {
            DB::beginTransaction();
            
            // Delete related order items first
            $order->items()->delete();
            
            // Then delete the order
            $order->delete();
            
            DB::commit();
            return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting order: ' . $e->getMessage());
        }
    }
} 