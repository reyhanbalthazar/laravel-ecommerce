<?php
// app/Http/Controllers/OrderController.php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function show(Order $order)
    {
        // Check if user is authorized to view this order
        // For guest orders (user_id is null), we allow access
        // For registered user orders, check if the authenticated user owns the order
        if ($order->user_id && Auth::check() && $order->user_id != Auth::id()) {
            abort(403, 'Unauthorized to access this order');
        }

        $order->load('items.product');

        return view('orders.show', compact('order'));
    }

    public function index()
    {
        // Show order history for logged-in users
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to view your order history.');
        }

        $orders = Order::where('user_id', auth()->id())
            ->with('items.product')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }
}
