<?php
// app/Http/Controllers/CheckoutController.php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\MockPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected $mockPaymentService;

    public function __construct(MockPaymentService $mockPaymentService)
    {
        $this->mockPaymentService = $mockPaymentService;
    }

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

        // Get available payment methods from mock service
        $availablePaymentMethods = $this->mockPaymentService->getAvailablePaymentMethods();

        return view('checkout.show', compact('cart', 'subtotal', 'tax', 'shipping', 'total', 'availablePaymentMethods'));
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
            'payment_method' => 'required|string|in:credit_card,bank_transfer,gopay,shopeepay,qris',
        ]);

        try {
            // Validate stock availability before creating order
            foreach ($cart as $item) {
                $product = \App\Models\Product::find($item['id']);
                if (!$product) {
                    return redirect()->back()->with('error', 'Product not found in cart.');
                }
                
                if ($product->stock < $item['quantity']) {
                    return redirect()->back()->with('error', 
                        "Not enough stock for {$product->name}. Only {$product->stock} available.");
                }
            }

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
                'payment_method' => $validated['payment_method'],
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

            // Prepare customer info for payment
            $customerInfo = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ];

            // Prepare items info for payment
            $items = [];
            foreach ($cart as $item) {
                $items[] = [
                    'id' => $item['id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'name' => $item['name'],
                ];
            }

            // Create payment transaction through mock service
            $paymentData = [
                'order' => $order,
                'customer_info' => $customerInfo,
                'items' => $items,
                'payment_type' => $validated['payment_method'],
            ];

            // For bank transfer payments, we might need additional info
            if ($validated['payment_method'] === 'bank_transfer') {
                $paymentData['bank'] = $request->bank ?? 'bca';
            }

            $paymentResult = $this->mockPaymentService->createTransaction($paymentData);

            // Update order with payment information
            $order->update([
                'transaction_id' => $paymentResult['transaction_id'] ?? null,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $paymentResult['transaction_status'] ?? 'pending',
            ]);

            // Clear the cart
            session()->forget('cart');

            // Redirect to order confirmation or payment page based on payment type
            if (in_array($validated['payment_method'], ['gopay', 'shopeepay', 'qris'])) {
                // These methods typically require customer interaction (like scanning QR)
                return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully! Please follow the payment instructions on the order details page.');
            } else {
                // Standard payment methods
                return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'There was an error processing your order. Please try again. Error: ' . $e->getMessage());
        }
    }
}
