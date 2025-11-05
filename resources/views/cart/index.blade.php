<!-- resources/views/cart/index.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Laravel Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <nav class="bg-white shadow py-4">
        <div class="max-w-6xl mx-auto px-4 flex justify-between items-center">
            <a href="/" class="text-xl font-bold">LaravelStore</a>
            <div class="flex space-x-4">
                <a href="/products" class="text-gray-600 hover:text-gray-900">Products</a>
                <a href="/cart" class="text-gray-600 hover:text-gray-900">Cart
                    <span class="bg-blue-500 text-white rounded-full px-2 py-1 text-xs">
                        {{ array_sum(array_column(session('cart', []), 'quantity')) }}
                    </span>
                </a>
            </div>
        </div>
    </nav>

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
            @foreach($cart as $id => $item)
            <div class="flex items-center justify-between p-6 border-b">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                        <span class="text-gray-500">Image</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">{{ $item['name'] }}</h3>
                        <p class="text-gray-600">${{ number_format($item['price'], 2) }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <form action="{{ route('cart.update', $id) }}" method="POST" class="flex items-center">
                        @csrf
                        <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                            min="1" class="w-16 border rounded px-2 py-1 text-center">
                        <button type="submit" class="ml-2 bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                            Update
                        </button>
                    </form>

                    <span class="font-semibold text-lg">
                        ${{ number_format($item['price'] * $item['quantity'], 2) }}
                    </span>

                    <form action="{{ route('cart.remove', $id) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-700">
                            Remove
                        </button>
                    </form>
                </div>
            </div>
            @endforeach

            <div class="p-6 bg-gray-50">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-xl font-bold">Total:</span>
                    <span class="text-xl font-bold">${{ number_format($total, 2) }}</span>
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
</body>

</html>