<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    /**
     * Display checkout form
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }
        
        $cartItems = [];
        $subtotal = 0;
        
        // Get updated product information and verify stock
        foreach ($cart as $id => $details) {
            $product = Product::with(['inventory', 'primaryImage', 'images'])
                              ->find($id);
                              
            if ($product) {
                // Verify product stock availability
                if (!$product->in_stock || $product->stock < $details['quantity']) {
                    return redirect()->route('cart.index')
                                     ->with('error', "Sorry! '{$product->name}' doesn't have enough stock.");
                }
                
                $cartItems[$id] = [
                    'product' => $product,
                    'quantity' => $details['quantity'],
                    'total' => $product->price * $details['quantity']
                ];
                
                $subtotal += $cartItems[$id]['total'];
            }
        }
        
        // Default shipping cost - could be dynamic in the future
        $shipping = 10.00;
        $tax = $subtotal * 0.05; // 5% tax
        $total = $subtotal + $shipping + $tax;
        
        // Get logged in customer information if available
        $customer = null;
        if (Auth::check()) {
            // Look for existing customer or create one if needed
            $customer = Customer::where('email', Auth::user()->email)->first();
            if (!$customer) {
                $customer = new Customer();
                $customer->email = Auth::user()->email;
                // Attempt to set user_id if column exists
                try {
                    $customer->user_id = Auth::id();
                } catch (\Exception $e) {
                    // Column might not exist - just ignore
                }
            }
        }
        
        return view('checkout.index', compact('cartItems', 'subtotal', 'shipping', 'tax', 'total', 'customer'));
    }
    
    /**
     * Process checkout and create order
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function process(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:100',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:50',
            'state' => 'required|string|max:50',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:50',
        ]);
        
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }
        
        // Calculate order total
        $subtotal = 0;
        $products = [];
        
        foreach ($cart as $id => $details) {
            $product = Product::find($id);
            
            if ($product) {
                // Verify product stock availability
                if (!$product->in_stock || $product->stock < $details['quantity']) {
                    return redirect()->route('cart.index')
                                     ->with('error', "Sorry! '{$product->name}' doesn't have enough stock.");
                }
                
                $products[$id] = [
                    'product' => $product,
                    'quantity' => $details['quantity'],
                    'price' => $product->price,
                    'total' => $product->price * $details['quantity']
                ];
                
                $subtotal += $products[$id]['total'];
            }
        }
        
        $shipping = 10.00; // Default shipping cost
        $tax = $subtotal * 0.05; // 5% tax rate
        $total = $subtotal + $shipping + $tax;
        
        // Create or get customer record if user is logged in
        $customer = null;
        if (Auth::check()) {
            // Try to find customer by email first
            $customer = Customer::where('email', Auth::user()->email)->first();
            
            // If not found, create a new one
            if (!$customer) {
                $userData = [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' => $request->state,
                    'postal_code' => $request->postal_code,
                    'country' => $request->country,
                ];
                
                // Try to include user_id if column exists
                try {
                    $userData['user_id'] = Auth::id();
                } catch (\Exception $e) {
                    // Column might not exist - just ignore
                }
                
                $customer = Customer::create($userData);
            }
        } else {
            // Create temporary customer record for guest checkout
            $customer = Customer::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
            ]);
        }
        
        // Format shipping address
        $shippingAddress = "{$request->first_name} {$request->last_name}\n"
                          . "{$request->address}\n"
                          . "{$request->city}, {$request->state} {$request->postal_code}\n"
                          . "{$request->country}\n"
                          . "Phone: {$request->phone}";
        
        // Begin database transaction
        DB::beginTransaction();
        
        try {
            // Create the order
            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total,
                'payment_method' => 'pending', // Will be updated later
                'payment_status' => 'pending',
                'shipping_address' => $shippingAddress,
            ]);
            
            // Create order items and reduce inventory
            foreach ($products as $id => $item) {
                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'product_name' => $item['product']->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['total']
                ]);
                
                // Reduce inventory
                $inventory = Inventory::where('product_id', $id)->first();
                if ($inventory) {
                    // Adjust stock and create transaction record
                    $inventory->adjustStock(
                        $item['quantity'],
                        'stock_out',
                        'Order #' . $order->order_number,
                        Auth::id(),
                        $order->order_number
                    );
                }
            }
            
            // Clear the cart
            session()->forget('cart');
            
            // Commit transaction
            DB::commit();
            
            // Redirect to success page
            return redirect()->route('checkout.success', $order->id)
                             ->with('success', 'Your order has been placed successfully!');
                             
        } catch (\Exception $e) {
            // Roll back transaction on error
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());
            
            return redirect()->route('checkout.index')
                             ->with('error', 'Something went wrong. Please try again.');
        }
    }
    
    /**
     * Display order success page
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function success(Order $order)
    {
        // Verify if current user has access to this order
        if (Auth::check()) {
            $customer = Customer::where('user_id', Auth::id())->first();
            
            // If not the customer's order, redirect
            if ($customer && $order->customer_id != $customer->id) {
                return redirect()->route('home')->with('error', 'Unauthorized access.');
            }
        }
        
        return view('checkout.success', compact('order'));
    }
} 