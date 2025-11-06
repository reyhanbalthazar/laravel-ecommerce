<!-- resources/views/products/show.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - Laravel Store</title>
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

    <!-- Success Popup -->
    <div id="success-popup" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="bg-black bg-opacity-50 absolute inset-0"></div>
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm mx-auto z-10">
            <div class="text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Success!</h3>
                <p class="text-gray-600 mb-6">Product added to cart successfully!</p>
                <div class="flex space-x-3">
                    <button onclick="hidePopup()" class="flex-1 bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">
                        Continue Shopping
                    </button>
                    <a href="/cart" class="flex-1 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 text-center">
                        Go to Cart
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto py-8 px-4">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm">
                <li>
                    <a href="/" class="text-gray-500 hover:text-gray-700">Home</a>
                </li>
                <li>
                    <span class="text-gray-400">/</span>
                </li>
                <li>
                    <a href="/products" class="text-gray-500 hover:text-gray-700">Products</a>
                </li>
                <li>
                    <span class="text-gray-400">/</span>
                </li>
                <li>
                    <a href="/category/{{ $product->category->slug }}" class="text-gray-500 hover:text-gray-700">{{ $product->category->name }}</a>
                </li>
                <li>
                    <span class="text-gray-400">/</span>
                </li>
                <li>
                    <span class="text-gray-700 font-medium">{{ $product->name }}</span>
                </li>
            </ol>
        </nav>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="md:flex">
                <!-- Product Images -->
                <div class="md:w-1/2 p-8">
                    <div class="bg-gray-100 rounded-lg h-96 flex items-center justify-center mb-4">
                        @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="max-h-80 max-w-full object-contain">
                        @else
                        <div class="text-center text-gray-400">
                            <svg class="w-24 h-24 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p>No image available</p>
                        </div>
                        @endif
                    </div>

                    <!-- Image Gallery (if we had multiple images) -->
                    <div class="flex space-x-2">
                        <div class="w-16 h-16 bg-gray-200 rounded border-2 border-blue-500"></div>
                        <div class="w-16 h-16 bg-gray-200 rounded border border-gray-300"></div>
                        <div class="w-16 h-16 bg-gray-200 rounded border border-gray-300"></div>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="md:w-1/2 p-8">
                    <!-- Category Badge -->
                    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded mb-4">
                        {{ $product->category->name }}
                    </span>

                    <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $product->name }}</h1>

                    <!-- Price -->
                    <div class="mb-6">
                        @if($product->sale_price)
                        <div class="flex items-center space-x-2">
                            <span class="text-3xl font-bold text-gray-800">${{ number_format($product->sale_price, 2) }}</span>
                            <span class="text-xl text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                            <span class="bg-red-500 text-white text-sm font-semibold px-2 py-1 rounded">Save ${{ number_format($product->price - $product->sale_price, 2) }}</span>
                        </div>
                        @else
                        <span class="text-3xl font-bold text-gray-800">${{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>

                    <!-- Stock Status -->
                    <div class="mb-6">
                        @if($product->stock > 0)
                        <span class="inline-flex items-center text-sm text-green-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            In Stock ({{ $product->stock }} available)
                        </span>
                        @else
                        <span class="inline-flex items-center text-sm text-red-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Out of Stock
                        </span>
                        @endif
                    </div>

                    <!-- Add to Cart Form -->
                    @if($product->stock > 0)
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="mb-6">
                        @csrf
                        <div class="flex space-x-4 mb-4">
                            <!-- Quantity Selector -->
                            <div class="flex items-center">
                                <label for="quantity" class="text-sm font-medium text-gray-700 mr-2">Quantity:</label>
                                <select name="quantity" id="quantity" class="border border-gray-300 rounded px-3 py-2">
                                    @for($i = 1; $i <= min($product->stock, 10); $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-blue-500 text-white py-3 px-6 rounded-lg hover:bg-blue-600 transition duration-300 font-semibold text-lg">
                            Add to Cart
                        </button>
                    </form>
                    @else
                    <button disabled class="w-full bg-gray-400 text-white py-3 px-6 rounded-lg cursor-not-allowed font-semibold text-lg">
                        Out of Stock
                    </button>
                    @endif

                    <!-- Product Features -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Product Details</h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Free shipping on orders over $100
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                30-day return policy
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Secure checkout
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Product Description -->
            <div class="border-t border-gray-200">
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Description</h2>
                    <div class="prose max-w-none text-gray-600">
                        <p>{{ $product->description }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Related Products</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition duration-300">
                    <div class="bg-gray-100 rounded-lg h-32 flex items-center justify-center mb-3">
                        @if($relatedProduct->image)
                        <img src="{{ asset('storage/' . $relatedProduct->image) }}" alt="{{ $relatedProduct->name }}" class="max-h-24 max-w-full object-contain">
                        @else
                        <div class="text-center text-gray-400">
                            <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        @endif
                    </div>
                    <h3 class="font-semibold text-sm mb-1">{{ Str::limit($relatedProduct->name, 40) }}</h3>
                    <p class="text-gray-600 text-sm mb-2">${{ number_format($relatedProduct->price, 2) }}</p>
                    <a href="{{ route('products.show', $relatedProduct) }}" class="block w-full bg-gray-100 text-gray-700 text-center py-1 rounded text-sm hover:bg-gray-200 transition">
                        View Details
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <script>
        function hidePopup() {
            document.getElementById('success-popup').classList.add('hidden');
        }

        

        // Close popup when clicking outside
        document.getElementById('success-popup').addEventListener('click', function(e) {
            if (e.target === this) {
                hidePopup();
            }
        });

        // Close popup with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hidePopup();
            }
        });
    </script>
    @if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('success-popup').classList.remove('hidden');
        });
    </script>
    @endif

</body>

</html>