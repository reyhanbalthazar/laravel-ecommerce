<?php
// app/Http/Controllers/Admin/OrderController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Add to Admin OrderController
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        // Search by order number, customer name/email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('shipping_address', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }

    public function markAsProcessing(Order $order)
    {
        $order->update(['status' => 'processing']);
        return back()->with('success', 'Order marked as processing!');
    }

    public function markAsShipped(Order $order)
    {
        // Add tracking number, shipping date, etc.
        $order->update(['status' => 'completed']); // or 'shipped' if you add that status
        return back()->with('success', 'Order marked as shipped!');
    }

    public function cancelOrder(Request $request, Order $order)
    {
        $request->validate(['cancellation_reason' => 'required|string|max:255']);

        $order->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason
        ]);

        return back()->with('success', 'Order cancelled successfully!');
    }
}
