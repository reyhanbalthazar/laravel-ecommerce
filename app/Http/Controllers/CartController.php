<?php
// app/Http\Controllers\CartController.php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cart = session()->get('cart', []);

        // Clean up invalid products automatically
        $cart = $this->cleanupCart($cart);

        $subtotal = 0;
        foreach ($cart as $productId => $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $tax = $subtotal * 0.10;
        $shipping = $subtotal > 50 ? 0 : 10;
        $total = $subtotal + $tax + $shipping;

        return view('cart.index', compact('cart', 'subtotal', 'tax', 'shipping', 'total'));
    }

    /**
     * Remove products from cart that no longer exist in database
     */
    private function cleanupCart($cart)
    {
        $validCart = [];
        $removedProducts = [];

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $validCart[$productId] = $item;
            } else {
                $removedProducts[] = $item['name'];
            }
        }

        // Update session with valid cart only
        if (count($validCart) != count($cart)) {
            session()->put('cart', $validCart);

            // Flash a message if products were removed
            if (!empty($removedProducts)) {
                session()->flash(
                    'warning',
                    'Some products are no longer available and were removed from your cart: ' .
                        implode(', ', $removedProducts)
                );
            }
        }

        return $validCart;
    }

    public function add(Product $product, Request $request)
    {
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

    public function update(Request $request, $productId)
    {
        $cart = session()->get('cart', []);
        $quantity = $request->quantity;

        // If quantity is 0 or negative, remove the item
        if ($quantity <= 0) {
            return $this->remove($productId);
        }

        // Update quantity if product exists in cart
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session()->put('cart', $cart);

            return redirect()->back()->with('success', 'Cart updated successfully!');
        }

        return redirect()->back()->with('error', 'Product not found in cart!');
    }

    public function remove($productId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $productName = $cart[$productId]['name'];
            unset($cart[$productId]);
            session()->put('cart', $cart);
            return redirect()->back()->with('success', $productName . ' removed from cart!');
        }

        return redirect()->back()->with('error', 'Product not found in cart!');
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Cart cleared successfully!');
    }

    public function count()
    {
        $cart = session()->get('cart', []);
        $count = 0;
        foreach ($cart as $item) {
            $count += $item['quantity'];
        }
        return response()->json(['count' => $count]);
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
