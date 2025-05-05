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
        $products = [];
        $total = 0;
        
        // Get updated product information
        foreach ($cart as $id => $item) {
            $product = Product::with('images')->findOrFail($id);
            $products[$id] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'subtotal' => $product->price * $item['quantity']
            ];
            $total += $products[$id]['subtotal'];
        }
        
        return view('cart.index', compact('products', 'total'));
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
        
        $productId = $request->product_id;
        $quantity = $request->quantity;
        
        // Check product stock availability
        $product = Product::with('inventory')->findOrFail($productId);
        
        if (!$product->in_stock || $product->stock < $quantity) {
            return back()->with('error', 'Sorry! The requested quantity is not available in stock.');
        }
        
        $cart = session()->get('cart', []);
        
        // If item already in cart, update quantity
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
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
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1'
        ]);
        
        $cart = session()->get('cart', []);
        
        foreach ($request->quantities as $productId => $quantity) {
            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] = $quantity;
            }
        }
        
        session()->put('cart', $cart);
        
        return redirect()->back()->with('success', 'Cart updated successfully!');
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
    
    /**
     * Add an item to the cart via AJAX.
     */
    public function addToCartAjax(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        
        $productId = $request->product_id;
        $quantity = $request->quantity;
        
        $cart = session()->get('cart', []);
        
        // If the product is already in the cart, update the quantity
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'quantity' => $quantity
            ];
        }
        
        session()->put('cart', $cart);
        
        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cartCount' => count($cart)
        ]);
    }
    
    /**
     * Update cart quantities via AJAX.
     */
    public function updateCartAjax(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        
        $productId = $request->product_id;
        $quantity = $request->quantity;
        
        $cart = session()->get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session()->put('cart', $cart);
            
            // Get the updated product for the response
            $product = Product::findOrFail($productId);
            $subtotal = $product->price * $quantity;
            
            // Calculate the new cart total
            $total = 0;
            foreach ($cart as $id => $item) {
                $product = Product::findOrFail($id);
                $total += $product->price * $item['quantity'];
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully!',
                'quantity' => $quantity,
                'subtotal' => $subtotal,
                'subtotalFormatted' => '$' . number_format($subtotal, 2),
                'total' => $total,
                'totalFormatted' => '$' . number_format($total, 2),
                'cartCount' => count($cart)
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Product not found in cart'
        ], 404);
    }
    
    /**
     * Remove an item from the cart via AJAX.
     */
    public function removeFromCartAjax($id)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            
            // Calculate the new cart total
            $total = 0;
            foreach ($cart as $itemId => $item) {
                $product = Product::findOrFail($itemId);
                $total += $product->price * $item['quantity'];
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart!',
                'total' => $total,
                'totalFormatted' => '$' . number_format($total, 2),
                'cartCount' => count($cart)
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Product not found in cart'
        ], 404);
    }
} 