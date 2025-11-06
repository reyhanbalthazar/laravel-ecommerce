<?php
// app/Http/Controllers/CheckoutController.php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function show()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        // Calculate totals
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $tax = $subtotal * 0.10; // 10% tax
        $shipping = $subtotal > 50 ? 0 : 10; // Free shipping over $50
        $total = $subtotal + $tax + $shipping;

        return view('checkout.show', compact('cart', 'subtotal', 'tax', 'shipping', 'total'));
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        // Validate form data
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'country' => 'required|string|max:255',
            'customer_note' => 'nullable|string|max:1000',
        ]);

        try {
            // Calculate totals
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            $tax = $subtotal * 0.10;
            $shipping = $subtotal > 50 ? 0 : 10;
            $total = $subtotal + $tax + $shipping;

            // Build shipping address string
            $shippingAddress = "{$validated['first_name']} {$validated['last_name']}\n";
            $shippingAddress .= "{$validated['email']}\n";
            $shippingAddress .= "{$validated['phone']}\n";
            $shippingAddress .= "{$validated['address']}\n";
            $shippingAddress .= "{$validated['city']}, {$validated['state']} {$validated['zip_code']}\n";
            $shippingAddress .= "{$validated['country']}";

            // Create order - user_id can be null for guest orders
            $order = Order::create([
                'order_number' => 'ORD-' . Str::upper(Str::random(10)),
                'user_id' => auth()->check() ? auth()->id() : null, // Null for guest orders
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total,
                'payment_method' => 'card',
                'payment_status' => 'pending',
                'shipping_address' => $shippingAddress,
                'customer_note' => $validated['customer_note'] ?? null,
            ]);

            // Create order items
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                ]);
            }

            // Clear the cart
            session()->forget('cart');

            // Redirect to order confirmation
            return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'There was an error processing your order. Please try again. Error: ' . $e->getMessage());
        }
    }
}
