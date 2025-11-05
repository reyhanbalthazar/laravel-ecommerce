<?php
// app/Http/Controllers/CartController.php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Product $product, Request $request)
    {
        $cart = session()->get('cart', []);
        $quantity = $request->quantity ?? 1;

        // Check if product is already in cart
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                "id" => $product->id,
                "name" => $product->name,
                "slug" => $product->slug,
                "quantity" => $quantity,
                "price" => $product->current_price,
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

    public function update(Product $product, Request $request)
    {
        $cart = session()->get('cart', []);
        $quantity = $request->quantity;

        if ($quantity <= 0) {
            return $this->remove($product, $request);
        }

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart updated!',
                'cart_count' => $this->getCartCount(),
                'subtotal' => $cart[$product->id]['price'] * $quantity
            ]);
        }

        return redirect()->back()->with('success', 'Cart updated!');
    }

    public function remove(Product $product, Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart!',
                'cart_count' => $this->getCartCount()
            ]);
        }

        return redirect()->back()->with('success', 'Product removed from cart!');
    }

    public function clear(Request $request)
    {
        session()->forget('cart');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart cleared!',
                'cart_count' => 0
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Cart cleared!');
    }

    public function count()
    {
        return response()->json([
            'count' => $this->getCartCount()
        ]);
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
