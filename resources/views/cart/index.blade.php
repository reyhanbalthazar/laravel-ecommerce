<!-- resources/views/cart/index.blade.php -->
@extends('layouts.app')

@section('title', 'Home - LaravelStore')

@section('content')

<!-- Rest of your cart page remains the same -->
<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-8">Shopping Cart</h1>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if(count($cart) > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Your existing cart items display -->
        @foreach($cart as $productId => $item)
        <div class="flex items-center justify-between p-6 border-b">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                    @if($item['image'])
                    <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="max-h-12 max-w-full object-contain">
                    @else
                    <span class="text-gray-500 text-xs">Image</span>
                    @endif
                </div>
                <div>
                    <h3 class="font-semibold text-lg">{{ $item['name'] }}</h3>
                    <p class="text-gray-600">${{ number_format($item['price'], 2) }} x {{ $item['quantity'] }}</p>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Quantity Update Form -->
                <form action="{{ route('cart.update', $productId) }}" method="POST" class="flex items-center">
                    @csrf
                    <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                        min="1" max="{{ $item['stock'] ?? 10 }}"
                        class="w-16 border rounded px-2 py-1 text-center">
                    <button type="submit" class="ml-2 bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                        Update
                    </button>
                </form>

                <span class="font-semibold text-lg">
                    ${{ number_format($item['price'] * $item['quantity'], 2) }}
                </span>

                <!-- Remove Form -->
                <form action="{{ route('cart.remove', $productId) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                        Remove
                    </button>
                </form>
            </div>
        </div>
        @endforeach

        <div class="p-6 bg-gray-50">
            <!-- In resources/views/cart/index.blade.php, update the checkout section: -->
            <div class="p-4 flex justify-between items-center">
                <span class="text-xl font-bold">Total: ${{ number_format($total, 2) }}</span>
                <a href="{{ route('checkout.show') }}" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition duration-300">
                    Proceed to Checkout
                </a>
            </div>

            <div class="flex space-x-4">
                <a href="/products" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                    Continue Shopping
                </a>
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600">
                        Clear Cart
                    </button>
                </form>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <h2 class="text-2xl font-bold text-gray-600 mb-4">Your cart is empty</h2>
        <p class="text-gray-500 mb-6">Add some products to your cart to see them here.</p>
        <a href="/products" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600">
            Browse Products
        </a>
    </div>
    @endif
</div>
@endsection