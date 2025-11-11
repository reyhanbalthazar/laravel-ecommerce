<?php

namespace App\Services;

use Illuminate\Support\Str;
use Carbon\Carbon;

class MockPaymentService
{
    const PAYMENT_STATUSES = [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'capture' => 'Capture',
        'cancel' => 'Cancel',
        'expire' => 'Expire',
        'refund' => 'Refund',
        'partial_refund' => 'Partial Refund'
    ];

    /**
     * Create a payment transaction
     *
     * @param array $data
     * @return array
     */
    public function createTransaction(array $data): array
    {
        $order = $data['order'];
        $customerInfo = $data['customer_info'] ?? [];
        $items = $data['items'] ?? [];

        // Generate unique transaction ID
        $transactionId = 'mock-' . time() . '-' . Str::random(8);
        
        // Determine payment type
        $paymentType = $data['payment_type'] ?? 'credit_card';
        
        // Calculate gross amount
        $grossAmount = $order->total;

        // Create the payment response
        $response = [
            'status_code' => '200',
            'status_message' => 'Success, transaction is found',
            'transaction_id' => $transactionId,
            'order_id' => $order->order_number,
            'gross_amount' => $grossAmount,
            'transaction_status' => 'pending',
            'fraud_status' => 'accept',
            'payment_type' => $paymentType,
            'transaction_time' => Carbon::now()->format('Y-m-d H:i:s'),
            'expiry_time' => Carbon::now()->addHour()->format('Y-m-d H:i:s'),
            'customer_info' => $customerInfo,
            'items' => $items,
        ];

        // Simulate different payment types and their responses
        switch ($paymentType) {
            case 'credit_card':
                $response['payment_type'] = 'credit_card';
                $response['masked_card'] = '4811-11XX-XXXX-1001';
                $response['bank'] = 'bni';
                $response['approval_code'] = '1234567890';
                break;
            
            case 'bank_transfer':
                $response['payment_type'] = 'bank_transfer';
                switch ($data['bank'] ?? 'bca') {
                    case 'bca':
                        $response['va_numbers'] = [
                            ['bank' => 'bca', 'va_number' => '1234567890']
                        ];
                        break;
                    case 'bni':
                        $response['va_numbers'] = [
                            ['bank' => 'bni', 'va_number' => '0987654321']
                        ];
                        break;
                    default:
                        $response['va_numbers'] = [
                            ['bank' => 'bca', 'va_number' => '1234567890']
                        ];
                }
                break;
            
            case 'gopay':
                $response['payment_type'] = 'gopay';
                $response['qr_code_url'] = 'https://placehold.co/200x200.png?text=QR+CODE';
                $response['payment_url'] = 'https://mock-payment-gateway.com/pay/' . $transactionId;
                break;
            
            case 'shopeepay':
                $response['payment_type'] = 'shopeepay';
                $response['qr_code_url'] = 'https://placehold.co/200x200.png?text=SHOPEEPAY+QR';
                break;
        }

        return $response;
    }

    /**
     * Get transaction status
     *
     * @param string $orderId
     * @return array
     */
    public function getTransactionStatus(string $orderId): array
    {
        // In a real implementation, this would query the database or payment provider
        // For mock purposes, we'll simulate different statuses based on time
        $simulatedStatus = $this->simulateTransactionStatus($orderId);
        
        return [
            'status_code' => '200',
            'status_message' => 'Success, transaction is found',
            'transaction_id' => 'mock-' . $orderId . '-' . time(),
            'order_id' => $orderId,
            'gross_amount' => rand(10000, 1000000),
            'transaction_status' => $simulatedStatus,
            'fraud_status' => 'accept',
            'payment_type' => 'credit_card',
            'transaction_time' => Carbon::now()->subMinutes(rand(1, 60))->format('Y-m-d H:i:s'),
            'settlement_time' => $simulatedStatus === 'settlement' ? Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Capture a pending transaction
     *
     * @param string $orderId
     * @return array
     */
    public function captureTransaction(string $orderId): array
    {
        return [
            'status_code' => '200',
            'status_message' => 'Success, transaction is captured',
            'transaction_id' => 'mock-' . $orderId . '-' . time(),
            'order_id' => $orderId,
            'gross_amount' => rand(10000, 1000000),
            'transaction_status' => 'capture',
            'fraud_status' => 'accept',
            'payment_type' => 'credit_card',
            'transaction_time' => Carbon::now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Cancel a transaction
     *
     * @param string $orderId
     * @return array
     */
    public function cancelTransaction(string $orderId): array
    {
        return [
            'status_code' => '200',
            'status_message' => 'Success, transaction is canceled',
            'transaction_id' => 'mock-' . $orderId . '-' . time(),
            'order_id' => $orderId,
            'gross_amount' => rand(10000, 1000000),
            'transaction_status' => 'cancel',
            'fraud_status' => 'accept',
            'payment_type' => 'credit_card',
            'transaction_time' => Carbon::now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Approve a transaction
     *
     * @param string $orderId
     * @return array
     */
    public function approveTransaction(string $orderId): array
    {
        return [
            'status_code' => '200',
            'status_message' => 'Success, transaction is approved',
            'transaction_id' => 'mock-' . $orderId . '-' . time(),
            'order_id' => $orderId,
            'gross_amount' => rand(10000, 1000000),
            'transaction_status' => 'paid',
            'fraud_status' => 'accept',
            'payment_type' => 'credit_card',
            'transaction_time' => Carbon::now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Expire a transaction
     *
     * @param string $orderId
     * @return array
     */
    public function expireTransaction(string $orderId): array
    {
        return [
            'status_code' => '200',
            'status_message' => 'Success, transaction is expired',
            'transaction_id' => 'mock-' . $orderId . '-' . time(),
            'order_id' => $orderId,
            'gross_amount' => rand(10000, 1000000),
            'transaction_status' => 'expire',
            'fraud_status' => 'accept',
            'payment_type' => 'credit_card',
            'transaction_time' => Carbon::now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Simulate transaction status based on time
     *
     * @param string $orderId
     * @return string
     */
    private function simulateTransactionStatus(string $orderId): string
    {
        // For demo purposes, we'll randomly simulate different statuses
        $statuses = array_keys(self::PAYMENT_STATUSES);
        // More likely to be pending or settlement
        $distribution = array_merge(
            array_fill(0, 70, 'pending'), // 70% chance of pending
            array_fill(0, 25, 'paid'), // 25% chance of paid
            array_fill(0, 3, 'cancel'), // 3% chance of cancel
            array_fill(0, 2, 'expire') // 2% chance of expire
        );
        
        return $distribution[array_rand($distribution)];
    }

    /**
     * Get available payment methods
     *
     * @return array
     */
    public function getAvailablePaymentMethods(): array
    {
        return [
            [
                'type' => 'credit_card',
                'name' => 'Credit Card',
                'enabled' => true,
                'options' => [
                    'secure' => true,
                    'bank' => ['bni', 'bca', 'mandiri'],
                ]
            ],
            [
                'type' => 'bank_transfer',
                'name' => 'Bank Transfer',
                'enabled' => true,
                'options' => [
                    'banks' => ['bca', 'bni', 'mandiri', 'bri']
                ]
            ],
            [
                'type' => 'gopay',
                'name' => 'GoPay',
                'enabled' => true
            ],
            [
                'type' => 'shopeepay',
                'name' => 'ShopeePay',
                'enabled' => true
            ],
            [
                'type' => 'qris',
                'name' => 'QRIS',
                'enabled' => true
            ]
        ];
    }

    /**
     * Process webhook notification
     *
     * @param array $payload
     * @return array
     */
    public function handleWebhook(array $payload): array
    {
        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? null;
        
        // Validate the webhook payload
        if (!$orderId || !$transactionStatus) {
            return [
                'status_code' => '400',
                'status_message' => 'Bad request, missing required fields'
            ];
        }

        // Respond with success to acknowledge the webhook
        return [
            'status_code' => '200',
            'status_message' => 'Webhook processed successfully',
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
        ];
    }
}