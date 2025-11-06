<!-- resources/views/products/index.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Laravel Store</title>
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
        <h1 class="text-3xl font-bold mb-8">All Products</h1>

        <!-- Search and Filter Bar -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <form action="{{ route('products.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-6">
                <!-- Search -->
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Products</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Search by name or description..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Category Filter -->
                <div class="flex-1">
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" id="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Price Range -->
                <div class="flex space-x-2">
                    <div>
                        <label for="min_price" class="block text-sm font-medium text-gray-700 mb-1">Min Price</label>
                        <input type="number" name="min_price" id="min_price" value="{{ request('min_price') }}"
                            placeholder="0" min="0"
                            class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="max_price" class="block text-sm font-medium text-gray-700 mb-1">Max Price</label>
                        <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}"
                            placeholder="1000" min="0"
                            class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Sort -->
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select name="sort" id="sort" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name: A to Z</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-2">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Apply Filters
                    </button>
                    <a href="{{ route('products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Clear
                    </a>
                </div>
            </form>

            <!-- Active Filters -->
            @if(request()->anyFilled(['search', 'category', 'min_price', 'max_price', 'sort']))
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Active filters:</span>
                        @if(request('search'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Search: "{{ request('search') }}"
                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-1 hover:text-blue-600">
                                &times;
                            </a>
                        </span>
                        @endif
                        @if(request('category'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Category: {{ $categories->where('slug', request('category'))->first()->name ?? request('category') }}
                            <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="ml-1 hover:text-green-600">
                                &times;
                            </a>
                        </span>
                        @endif
                        @if(request('min_price'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Min: ${{ request('min_price') }}
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => null]) }}" class="ml-1 hover:text-yellow-600">
                                &times;
                            </a>
                        </span>
                        @endif
                        @if(request('max_price'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Max: ${{ request('max_price') }}
                            <a href="{{ request()->fullUrlWithQuery(['max_price' => null]) }}" class="ml-1 hover:text-yellow-600">
                                &times;
                            </a>
                        </span>
                        @endif
                    </div>
                    <a href="{{ route('products.index') }}" class="text-sm text-red-600 hover:text-red-800">
                        Clear All
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Results Count -->
        <div class="mb-6 text-gray-600">
            Showing {{ $products->firstItem() }} - {{ $products->lastItem() }} of {{ $products->total() }} products
            @if(request()->anyFilled(['search', 'category', 'min_price', 'max_price']))
            <span class="text-sm text-gray-500">(filtered)</span>
            @endif
        </div>

        <!-- Show success message if redirected with success -->
        @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('success-popup').classList.remove('hidden');
            });
        </script>
        @endif

        <!-- Products Grid -->
        @if($products->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @foreach($products as $product)
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition duration-300">
                <!-- Product Image/Placeholder -->
                <div class="bg-gray-100 rounded-lg h-48 flex items-center justify-center mb-4">
                    @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="max-h-40 max-w-full object-contain">
                    @else
                    <div class="text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm">No image</p>
                    </div>
                    @endif
                </div>

                <!-- Product Info - Clickable -->
                <a href="{{ route('products.show', $product) }}" class="block mb-4">
                    <h3 class="font-semibold text-lg mb-2 hover:text-blue-600">{{ $product->name }}</h3>
                    <p class="text-gray-600 mb-2">${{ number_format($product->price, 2) }}</p>
                    <p class="text-sm text-gray-500 mb-2">{{ $product->category->name }}</p>
                    <p class="text-sm text-gray-500">{{ Str::limit($product->description, 100) }}</p>
                </a>

                <!-- Add to Cart Button -->
                <form action="{{ route('cart.add', $product) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition duration-300">
                        Add to Cart
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8V4a1 1 0 00-1-1h-2a1 1 0 00-1 1v1M9 7h6"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No products found</h3>
            <p class="text-gray-500 mb-6">Try adjusting your search or filter criteria.</p>
            <a href="{{ route('products.index') }}" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-300">
                Clear Filters
            </a>
        </div>
        @endif

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="flex justify-center mt-8">
            {{ $products->withQueryString()->links() }}
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

        // Auto-submit sort when changed (optional)
        document.getElementById('sort').addEventListener('change', function() {
            this.form.submit();
        });

        // Auto-submit category when changed (optional)
        document.getElementById('category').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
</body>

</html>