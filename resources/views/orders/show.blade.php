<!-- resources/views/orders/show.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Laravel Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <nav class="bg-white shadow py-4">
        <div class="max-w-6xl mx-auto px-4 flex justify-between items-center">
            <a href="/" class="text-xl font-bold">LaravelStore</a>
            <div class="flex space-x-4">
                <a href="/products" class="text-gray-600 hover:text-gray-900">Products</a>
                <a href="/cart" class="text-gray-600 hover:text-gray-900">Cart</a>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto py-8 px-4">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
        @endif

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Order Confirmed!</h1>
            <p class="text-gray-600">Thank you for your purchase. Your order has been received.</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <!-- Order Header -->
            <div class="bg-gray-50 px-6 py-4 border-b">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Order #{{ $order->order_number }}</h2>
                        <p class="text-gray-600">Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full 
                            @if($order->status == 'completed') bg-green-100 text-green-800
                            @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Payment Information Section -->
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-credit-card text-blue-500 mr-2"></i>
                    Payment Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Payment Status and Method -->
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Method:</span>
                            <span class="font-medium capitalize">{{ $order->payment_method ? str_replace('_', ' ', $order->payment_method) : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Status:</span>
                            <span class="font-medium capitalize {{ $order->isPaid() ? 'text-green-600' : ($order->isPending() ? 'text-yellow-600' : ($order->isCancelled() ? 'text-red-600' : 'text-gray-600')) }}">
                                {{ $order->payment_status ?: 'N/A' }}
                            </span>
                        </div>
                        @if($order->transaction_id)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Transaction ID:</span>
                            <span class="font-mono text-sm">{{ $order->transaction_id }}</span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Payment Instructions if needed -->
                    <div>
                        @if($order->payment_method == 'gopay' || $order->payment_method == 'shopeepay' || $order->payment_method == 'qris')
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <h4 class="font-semibold text-yellow-800 mb-2">Payment Instructions</h4>
                                <p class="text-sm text-yellow-700 mb-3">
                                    Please complete your payment using the e-wallet application. Scan the QR code or follow the application instructions.
                                </p>
                                
                                <!-- Placeholder for QR Code -->
                                <div class="flex justify-center">
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 w-48 h-48 flex items-center justify-center bg-gray-50">
                                        <div class="text-center">
                                            <i class="fas fa-qrcode text-4xl text-gray-400 mb-2"></i>
                                            <p class="text-xs text-gray-500">QR Code will appear here</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($order->payment_method == 'bank_transfer')
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="font-semibold text-blue-800 mb-2">Bank Transfer Instructions</h4>
                                <p class="text-sm text-blue-700 mb-3">
                                    Please transfer the amount to the following virtual account:
                                </p>
                                
                                <div class="bg-white rounded p-3">
                                    <div class="flex justify-between items-center">
                                        <span class="font-mono text-sm">Virtual Account Number:</span>
                                        <span class="font-semibold">1234567890</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Payment must be completed within 24 hours</p>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle text-3xl text-green-500 mb-2"></i>
                                <p class="text-sm text-gray-600">Payment method processed automatically</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="p-6 border-b">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Customer Information -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Shipping Address</h3>
                        <div class="space-y-2 text-gray-600 whitespace-pre-line">
                            {{ $order->shipping_address }}
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Order Summary</h3>
                        <div class="space-y-2 text-gray-600">
                            @if($order->customer_note)
                            <div>
                                <strong>Order Note:</strong>
                                <p class="mt-1">{{ $order->customer_note }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="border-t">
                <div class="px-6 py-4">
                    <h3 class="text-lg font-semibold mb-4">Order Items</h3>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                        <div class="flex items-center justify-between border-b pb-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center">
                                    @if($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="max-h-12 max-w-full object-contain">
                                    @else
                                    <span class="text-gray-400 text-xs">IMG</span>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-semibold">{{ $item->product->name }}</h4>
                                    <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                                    <p class="text-sm text-gray-600">${{ number_format($item->unit_price, 2) }} each</p>
                                </div>
                            </div>
                            <span class="font-semibold">${{ number_format($item->total, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Totals -->
                <div class="px-6 py-4 bg-gray-50">
                    <div class="max-w-md ml-auto space-y-2">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tax:</span>
                            <span>${{ number_format($order->tax, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping:</span>
                            <span>
                                @if($order->shipping == 0)
                                <span class="text-green-600">FREE</span>
                                @else
                                ${{ number_format($order->shipping, 2) }}
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t pt-2">
                            <span>Total:</span>
                            <span>${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auto-refresh payment status script -->
        <script>
            // Function to check payment status
            function checkPaymentStatus() {
                fetch('/orders/{{ $order->order_number }}/payment-status')
                    .then(response => response.json())
                    .then(data => {
                        if (data.transaction_status) {
                            const statusElement = document.querySelector('.font-medium.capitalize');
                            if (statusElement) {
                                statusElement.textContent = data.transaction_status;
                                
                                // Update status badge color
                                const badgeElement = document.querySelector('.rounded-full');
                                if (badgeElement) {
                                    badgeElement.className = 'inline-block px-3 py-1 text-sm font-semibold rounded-full';
                                    
                                    if (data.transaction_status === 'paid' || data.transaction_status === 'capture') {
                                        badgeElement.classList.add('bg-green-100', 'text-green-800');
                                    } else if (data.transaction_status === 'pending') {
                                        badgeElement.classList.add('bg-yellow-100', 'text-yellow-800');
                                    } else if (data.transaction_status === 'cancelled' || data.transaction_status === 'cancel' || data.transaction_status === 'expire') {
                                        badgeElement.classList.add('bg-red-100', 'text-red-800');
                                    } else {
                                        badgeElement.classList.add('bg-blue-100', 'text-blue-800');
                                    }
                                    
                                    // Update the text
                                    badgeElement.querySelector('span')?.textContent = data.transaction_status.charAt(0).toUpperCase() + data.transaction_status.slice(1);
                                }
                            }
                        }
                    })
                    .catch(error => console.error('Error checking payment status:', error));
            }

            // Check payment status every 10 seconds
            setInterval(checkPaymentStatus, 10000);
        </script>

        <div class="flex justify-center gap-4">
            <a href="{{ route('products.index') }}" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition duration-300 font-semibold">
                Continue Shopping
            </a>
            @if($order->payment_status !== 'paid' && $order->payment_status !== 'capture')
            <a href="{{ route('payment.mock', $order) }}" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition duration-300 font-semibold">
                Complete Payment
            </a>
            @endif
        </div>
    </div>
</body>

</html>