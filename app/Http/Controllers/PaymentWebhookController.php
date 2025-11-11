<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MockPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    protected $mockPaymentService;

    public function __construct(MockPaymentService $mockPaymentService)
    {
        $this->mockPaymentService = $mockPaymentService;
    }

    /**
     * Handle Midtrans-like webhook notification
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleWebhook(Request $request)
    {
        // Verify webhook signature (in real implementation)
        // For mock purposes, we'll skip this validation

        try {
            // Get payload from request
            $payload = $request->all();

            // Process the webhook
            $result = $this->mockPaymentService->handleWebhook($payload);

            if ($result['status_code'] == '400') {
                return response()->json(['status' => 'failed'], 400);
            }

            // Find the order by order_id
            $orderId = $payload['order_id'] ?? null;
            $transactionStatus = $payload['transaction_status'] ?? null;
            $paymentType = $payload['payment_type'] ?? null;
            $fraudStatus = $payload['fraud_status'] ?? 'accept';

            if (!$orderId) {
                return response()->json(['status' => 'failed', 'message' => 'Order ID not found'], 400);
            }

            $order = Order::where('order_number', $orderId)->first();

            if (!$order) {
                return response()->json(['status' => 'failed', 'message' => 'Order not found'], 400);
            }

            // Update payment information based on status
            if ($transactionStatus) {
                $order->payment_status = $transactionStatus;

                // Update order status based on payment status
                switch ($transactionStatus) {
                    case 'paid':
                    case 'capture':
                        if ($fraudStatus === 'accept') {
                            $order->status = 'processing'; // Move to processing once payment is confirmed
                        }
                        break;
                    case 'cancel':
                    case 'expire':
                    case 'deny':
                        $order->status = 'cancelled';
                        break;
                    case 'pending':
                        $order->status = 'pending';
                        break;
                }

                // Add payment type to the order if provided
                if ($paymentType) {
                    $order->payment_method = $paymentType;
                }

                // Add transaction ID if provided
                if (isset($payload['transaction_id'])) {
                    $order->transaction_id = $payload['transaction_id'];
                }

                $order->save();
            }

            // Return success response
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle payment status updates for an order
     *
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPaymentStatus(Order $order)
    {
        // Check if user is authorized to check payment status for this order
        if ($order->user_id && Auth::check() && $order->user_id != Auth::id()) {
            return response()->json(['error' => 'Unauthorized to check payment status'], 403);
        }
        
        // In a real implementation, this would call the actual payment provider
        // For mock purposes, we'll return the current status from the order
        return response()->json([
            'order_id' => $order->order_number,
            'transaction_status' => $order->payment_status,
            'payment_method' => $order->payment_method,
            'gross_amount' => $order->total,
        ]);
    }
}