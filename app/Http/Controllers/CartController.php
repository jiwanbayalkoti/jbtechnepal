<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;

class CartController extends Controller
{
    /**
     * Display the shopping cart
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $subtotal = 0;
        
        // Get updated product information
        foreach ($cart as $id => $details) {
            $product = Product::with(['inventory', 'primaryImage', 'images'])
                              ->find($id);
                              
            if ($product) {
                $cartItems[$id] = [
                    'product' => $product,
                    'quantity' => $details['quantity'],
                    'total' => $product->price * $details['quantity']
                ];
                
                $subtotal += $cartItems[$id]['total'];
            }
        }
        
        return view('cart.index', compact('cartItems', 'subtotal'));
    }
    
    /**
     * Add a product to the cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        $id = $request->product_id;
        $quantity = $request->quantity;
        
        // Check product stock availability
        $product = Product::with('inventory')->findOrFail($id);
        
        if (!$product->in_stock || $product->stock < $quantity) {
            return back()->with('error', 'Sorry! The requested quantity is not available in stock.');
        }
        
        $cart = session()->get('cart', []);
        
        // If item already in cart, update quantity
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'quantity' => $quantity
            ];
        }
        
        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Product added to cart successfully!');
    }
    
    /**
     * Update the cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);
        
        $id = $request->product_id;
        $quantity = $request->quantity;
        
        // Remove item if quantity is 0
        if ($quantity == 0) {
            return $this->removeFromCart($request);
        }
        
        // Check product stock availability
        $product = Product::with('inventory')->findOrFail($id);
        
        if (!$product->in_stock || $product->stock < $quantity) {
            return back()->with('error', 'Sorry! The requested quantity is not available in stock.');
        }
        
        $cart = session()->get('cart', []);
        
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $quantity;
            session()->put('cart', $cart);
            return back()->with('success', 'Cart updated successfully!');
        }
        
        return back()->with('error', 'Product not found in cart!');
    }
    
    /**
     * Remove an item from the cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        
        $id = $request->product_id;
        $cart = session()->get('cart', []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            return back()->with('success', 'Product removed from cart successfully!');
        }
        
        return back()->with('error', 'Product not found in cart!');
    }
} 