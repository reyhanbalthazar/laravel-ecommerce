<?php
// app/Http/Controllers/CartController.php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct()
    {
        // Apply auth middleware to all cart methods except count (if needed for display)
        $this->middleware('auth')->except(['count']);
    }

    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        $subtotal = 0;

        // Validate cart items and remove invalid ones
        $validCart = [];
        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $validCart[$productId] = $item;
                $itemTotal = $item['price'] * $item['quantity'];
                $subtotal += $itemTotal;
            }
        }

        // Update session with valid cart only
        if (count($validCart) != count($cart)) {
            session()->put('cart', $validCart);
            $cart = $validCart;
        }

        $tax = $subtotal * 0.10;
        $shipping = $subtotal > 50 ? 0 : 10;
        $total = $subtotal + $tax + $shipping;

        return view('cart.index', compact('cart', 'subtotal', 'tax', 'shipping', 'total'));
    }

    public function add(Product $product, Request $request)
    {
        // Ensure the product exists and is active
        if (!$product->exists || !$product->is_active) {
            return redirect()->back()->with('error', 'This product is not available.');
        }

        $cart = session()->get('cart', []);
        $quantity = $request->quantity ?? 1;

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                "id" => $product->id,
                "name" => $product->name,
                "price" => $product->current_price,
                "quantity" => $quantity,
                "image" => $product->image,
                "stock" => $product->stock
            ];
        }

        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart!',
                'cart_count' => $this->getCartCount()
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function update(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);
        $quantity = $request->quantity;

        if ($quantity <= 0) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id]['quantity'] = $quantity;
        }

        session()->put('cart', $cart);

        if ($request->ajax()) {
            $subtotal = $cart[$product->id]['price'] * $quantity;
            return response()->json([
                'success' => true,
                'message' => 'Cart updated!',
                'cart_count' => $this->getCartCount(),
                'subtotal' => number_format($subtotal, 2)
            ]);
        }

        return redirect()->back()->with('success', 'Cart updated!');
    }

    public function remove(Product $product)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Product removed from cart!');
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Cart cleared!');
    }

    private function getCartCount()
    {
        $cart = session()->get('cart', []);
        $count = 0;
        foreach ($cart as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }
}
