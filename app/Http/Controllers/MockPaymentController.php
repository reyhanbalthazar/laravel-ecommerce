<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MockPaymentController extends Controller
{
    /**
     * Show the mock payment page
     */
    public function show(Order $order)
    {
        // Check if user is authorized to view/access this order
        // For guest orders, skip authorization check
        // For registered user orders, ensure they own the order
        if ($order->user_id && Auth::check() && $order->user_id != Auth::id()) {
            abort(403, 'Unauthorized to access this order');
        }
        
        return view('payment.mock', compact('order'));
    }
    
    /**
     * Mark payment as paid
     */
    public function markAsPaid(Request $request, Order $order)
    {
        // Check if user is authorized to update payment for this order
        if ($order->user_id && Auth::check() && $order->user_id != Auth::id()) {
            abort(403, 'Unauthorized to update payment status');
        }
        
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Payment marked as paid successfully',
            'payment_status' => $order->payment_status
        ]);
    }
    
    /**
     * Check payment status
     */
    public function checkPaymentStatus(Order $order)
    {
        // Check if user is authorized to check payment status for this order
        if ($order->user_id && Auth::check() && $order->user_id != Auth::id()) {
            abort(403, 'Unauthorized to check payment status');
        }
        
        return response()->json([
            'payment_status' => $order->payment_status,
            'order_status' => $order->status,
            'transaction_id' => $order->transaction_id
        ]);
    }
}