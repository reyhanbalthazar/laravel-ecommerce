<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment Gateway - Laravel Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-lg w-full bg-white rounded-lg shadow-lg overflow-hidden mx-4">
        <!-- Header -->
        <div class="bg-blue-600 text-white p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-lock text-2xl mr-3"></i>
                    <h1 class="text-2xl font-bold">Secure Payment Gateway</h1>
                </div>
                <div class="text-right">
                    <div class="text-sm opacity-80">Secured by</div>
                    <div class="font-semibold">LaravelStore Pay</div>
                </div>
            </div>
        </div>

        <!-- Order Information -->
        <div class="p-6 border-b">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-lg font-semibold">Order #{{ $order->order_number }}</h2>
                    <p class="text-gray-600 text-sm">{{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-800">${{ number_format($order->total, 2) }}</div>
                    <div class="text-sm text-gray-500">Total Amount</div>
                </div>
            </div>

            <div class="mt-4">
                <h3 class="font-medium text-gray-700 mb-2">Payment Method</h3>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="font-medium capitalize">{{ $order->payment_method ? str_replace('_', ' ', $order->payment_method) : 'N/A' }}</span>
                        <div class="flex items-center text-green-600">
                            <i class="fas fa-shield-alt mr-2"></i>
                            <span>Secure</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="p-6 border-b">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-medium text-gray-700">Payment Status</h3>
                    <div class="mt-1">
                        <span id="payment-status" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            @if($order->isPaid()) bg-green-100 text-green-800
                            @elseif($order->isPending()) bg-yellow-100 text-yellow-800
                            @elseif($order->isCancelled()) bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($order->payment_status ?: 'N/A') }}
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Time Remaining</div>
                    <div id="countdown" class="text-xl font-bold text-red-600">10:00</div>
                </div>
            </div>
        </div>

        <!-- Payment Content -->
        <div class="p-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-credit-card text-2xl text-blue-600"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Complete Your Payment</h2>
                <p class="text-gray-600">Securely process your payment below</p>
            </div>

            @if($order->payment_method == 'gopay' || $order->payment_method == 'shopeepay' || $order->payment_method == 'qris')
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-yellow-800 mb-2 flex items-center">
                        <i class="fas fa-qrcode mr-2"></i>
                        Scan QR Code
                    </h4>
                    <p class="text-sm text-yellow-700 mb-3">
                        Open your e-wallet app and scan the QR code below to complete payment
                    </p>
                    
                    <!-- Placeholder for QR Code -->
                    <div class="flex justify-center">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 w-48 h-48 flex items-center justify-center bg-gray-50">
                            <div class="text-center">
                                <i class="fas fa-qrcode text-4xl text-gray-400 mb-2"></i>
                                <p class="text-xs text-gray-500">QR Code Placeholder</p>
                                <p class="text-xs text-gray-500 mt-1">ID: {{ $order->order_number }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($order->payment_method == 'bank_transfer')
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-blue-800 mb-2 flex items-center">
                        <i class="fas fa-university mr-2"></i>
                        Virtual Account
                    </h4>
                    <p class="text-sm text-blue-700 mb-3">
                        Transfer the amount to the virtual account below
                    </p>
                    
                    <div class="bg-white rounded p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Bank:</span>
                            <span class="font-semibold">Virtual Account</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Account Number:</span>
                            <span class="font-semibold font-mono">888{{ substr($order->order_number, -7) }}</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                        <i class="fas fa-credit-card mr-2"></i>
                        Secure Payment Form
                    </h4>
                    <p class="text-sm text-gray-600 mb-3">
                        Enter your card details to complete payment
                    </p>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm text-gray-600">Card Number</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="1234 5678 9012 3456" disabled>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-sm text-gray-600">Expiry Date</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="MM/YY" disabled>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">CVV</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="123" disabled>
                            </div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Full Name</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $order->customer_name }}" disabled>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Payment Status Update -->
            <div id="status-message" class="mb-6 p-4 rounded-lg bg-blue-50 border border-blue-200 hidden">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    <span class="text-blue-700">Payment status will update automatically when processed</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-3">
                <button id="mark-as-paid-btn" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg transition duration-300 font-semibold flex items-center justify-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    Mark as Paid
                </button>
                
                <button id="check-status-btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg transition duration-300 font-semibold flex items-center justify-center">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Check Payment Status
                </button>
                
                <a href="{{ route('orders.show', $order) }}" class="w-full bg-gray-500 hover:bg-gray-600 text-white py-3 px-6 rounded-lg transition duration-300 font-semibold text-center">
                    Back to Order
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 p-4 border-t text-center">
            <p class="text-xs text-gray-500">
                <i class="fas fa-shield-alt mr-1"></i>
                Your payment information is securely encrypted and processed
            </p>
        </div>
    </div>

    <script>
        // Countdown timer
        let timeLeft = 600; // 10 minutes in seconds
        const countdownElement = document.getElementById('countdown');
        const paymentStatusElement = document.getElementById('payment-status');
        const statusMessageElement = document.getElementById('status-message');

        // Update the countdown every second
        const countdownInterval = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;

            countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                countdownElement.textContent = "00:00";
                countdownElement.classList.remove('text-red-600');
                countdownElement.classList.add('text-red-800');
            }

            timeLeft--;
        }, 1000);

        // Function to update payment status display
        function updatePaymentStatus(status) {
            paymentStatusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);

            // Remove all status classes
            paymentStatusElement.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium';

            // Add appropriate status class
            if (status === 'paid' || status === 'settlement' || status === 'capture') {
                paymentStatusElement.classList.add('bg-green-100', 'text-green-800');
            } else if (status === 'pending') {
                paymentStatusElement.classList.add('bg-yellow-100', 'text-yellow-800');
            } else if (status === 'cancelled' || status === 'cancel' || status === 'expire') {
                paymentStatusElement.classList.add('bg-red-100', 'text-red-800');
            } else {
                paymentStatusElement.classList.add('bg-gray-100', 'text-gray-800');
            }
        }

        // Mark as paid button handler
        document.getElementById('mark-as-paid-btn').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';

            fetch('{{ route("payment.mark.paid", $order) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updatePaymentStatus(data.payment_status);
                    statusMessageElement.classList.remove('hidden');
                    statusMessageElement.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span class="text-green-700">${data.message}</span>
                        </div>
                    `;

                    // Change button to success state
                    document.getElementById('mark-as-paid-btn').innerHTML = '<i class="fas fa-check mr-2"></i> Payment Confirmed!';
                    document.getElementById('mark-as-paid-btn').disabled = true;
                } else {
                    alert('Error: ' + (data.message || 'Could not update payment status'));
                    document.getElementById('mark-as-paid-btn').disabled = false;
                    document.getElementById('mark-as-paid-btn').innerHTML = '<i class="fas fa-check-circle mr-2"></i> Mark as Paid';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: Could not communicate with server');
                document.getElementById('mark-as-paid-btn').disabled = false;
                document.getElementById('mark-as-paid-btn').innerHTML = '<i class="fas fa-check-circle mr-2"></i> Mark as Paid';
            });
        });

        // Check payment status button handler
        document.getElementById('check-status-btn').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Checking...';

            fetch('{{ route("payment.status", $order) }}')
                .then(response => response.json())
                .then(data => {
                    // Update payment status
                    updatePaymentStatus(data.payment_status);

                    // Show status message
                    statusMessageElement.classList.remove('hidden');
                    statusMessageElement.innerHTML = `
                        <div class="flex items-center">
                            <i class="${data.payment_status === 'paid' || data.payment_status === 'settlement' || data.payment_status === 'capture' ? 'fas fa-check-circle text-green-500' : 'fas fa-info-circle text-blue-500'} mr-2"></i>
                            <span class="${data.payment_status === 'paid' || data.payment_status === 'settlement' || data.payment_status === 'capture' ? 'text-green-700' : 'text-blue-700'}">Current status: ${data.payment_status}</span>
                        </div>
                    `;

                    // Reset button
                    document.getElementById('check-status-btn').disabled = false;
                    document.getElementById('check-status-btn').innerHTML = '<i class="fas fa-sync-alt mr-2"></i> Check Payment Status';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error: Could not check payment status');
                    document.getElementById('check-status-btn').disabled = false;
                    document.getElementById('check-status-btn').innerHTML = '<i class="fas fa-sync-alt mr-2"></i> Check Payment Status';
                });
        });

        // Add CSRF token meta tag if not already present
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';
            document.head.appendChild(meta);
        }
    </script>
</body>

</html>