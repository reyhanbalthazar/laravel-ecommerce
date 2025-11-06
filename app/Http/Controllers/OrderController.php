<?php
// app/Http/Controllers/OrderController.php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show(Order $order)
    {
        // Allow users to view their own orders, or anyone to view by order number for now
        // In a real app, you'd want more robust authorization
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
