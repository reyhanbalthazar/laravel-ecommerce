<!-- resources/views/checkout/show.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Laravel Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

    <div class="max-w-6xl mx-auto py-8 px-4">
        <h1 class="text-3xl font-bold mb-8">Checkout</h1>

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Checkout Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold mb-6">Customer Information</h2>

                <form action="{{ route('checkout.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input type="text" id="first_name" name="first_name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('first_name') }}">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('last_name') }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" id="email" name="email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('email') }}">
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                        <input type="tel" id="phone" name="phone" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('phone') }}">
                    </div>

                    <div class="mb-4">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                        <input type="text" id="address" name="address" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('address') }}">
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                            <input type="text" id="city" name="city" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('city') }}">
                        </div>
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                            <input type="text" id="state" name="state" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('state') }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-1">ZIP Code *</label>
                            <input type="text" id="zip_code" name="zip_code" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('zip_code') }}">
                        </div>
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                            <input type="text" id="country" name="country" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('country') }}">
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                        <div class="space-y-3">
                            @foreach($availablePaymentMethods as $method)
                                @if($method['enabled'])
                                    <div class="flex items-center border rounded-lg p-4 hover:border-blue-400 cursor-pointer payment-option" data-method="{{ $method['type'] }}">
                                        <input type="radio" 
                                            id="payment_{{ $method['type'] }}" 
                                            name="payment_method" 
                                            value="{{ $method['type'] }}" 
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                                            {{ $method['type'] === 'credit_card' ? 'checked' : '' }}>
                                        <label for="payment_{{ $method['type'] }}" class="ml-3 flex items-center cursor-pointer">
                                            <i class="fas {{ $method['type'] === 'credit_card' ? 'fa-credit-card' : ($method['type'] === 'bank_transfer' ? 'fa-university' : ($method['type'] === 'gopay' ? 'fa-wallet' : ($method['type'] === 'shopeepay' ? 'fa-shopping-bag' : 'fa-qrcode'))) }} text-xl mr-3"></i>
                                            <span class="font-medium">{{ $method['name'] }}</span>
                                            @if($method['type'] === 'bank_transfer' && isset($method['options']['banks']))
                                                <select name="bank" id="bank_{{ $method['type'] }}" class="ml-4 border rounded-md px-2 py-1 text-xs hidden bank-select" style="display:none;">
                                                    @foreach($method['options']['banks'] as $bank)
                                                        <option value="{{ $bank }}">{{ strtoupper($bank) }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="customer_note" class="block text-sm font-medium text-gray-700 mb-1">Order Notes (Optional)</label>
                        <textarea id="customer_note" name="customer_note" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Any special instructions...">{{ old('customer_note') }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-green-500 text-white py-3 px-6 rounded-lg hover:bg-green-600 transition duration-300 font-semibold text-lg">
                        Place Order - ${{ number_format($total, 2) }}
                    </button>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow p-6 h-fit">
                <h2 class="text-2xl font-bold mb-6">Order Summary</h2>

                <div class="space-y-4 mb-6">
                    @foreach($cart as $item)
                    <div class="flex items-center justify-between border-b pb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                                @if($item['image'])
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="max-h-10 max-w-full object-contain">
                                @else
                                <span class="text-gray-400 text-xs">IMG</span>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-semibold">{{ $item['name'] }}</h3>
                                <p class="text-sm text-gray-600">Qty: {{ $item['quantity'] }}</p>
                            </div>
                        </div>
                        <span class="font-semibold">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="space-y-2 border-t pt-4">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tax (10%)</span>
                        <span>${{ number_format($tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Shipping</span>
                        <span>
                            @if($shipping == 0)
                            <span class="text-green-600">FREE</span>
                            @else
                            ${{ number_format($shipping, 2) }}
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>Total</span>
                        <span>${{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span class="text-sm text-blue-700">Secure checkout. Your information is safe with us.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentOptions = document.querySelectorAll('.payment-option');
            const bankSelects = document.querySelectorAll('.bank-select');
            
            // Add event listeners to payment options
            paymentOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const method = this.getAttribute('data-method');
                    
                    // Update radio button selection
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                    
                    // Hide all bank selects
                    bankSelects.forEach(select => {
                        select.style.display = 'none';
                    });
                    
                    // Show the bank select for bank transfer
                    if(method === 'bank_transfer') {
                        const bankSelect = document.getElementById('bank_' + method);
                        if(bankSelect) {
                            bankSelect.style.display = 'inline-block';
                        }
                    }
                });
            });
            
            // Set first payment method as selected by default
            if(paymentOptions.length > 0) {
                const firstRadio = paymentOptions[0].querySelector('input[type="radio"]');
                firstRadio.checked = true;
            }
        });
    </script>
</body>

</html>