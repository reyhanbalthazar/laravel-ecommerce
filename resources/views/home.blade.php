<!-- resources/views/home.blade.php -->
@extends('layouts.app')

@section('title', 'Home - LaravelStore')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-700 rounded-2xl text-white p-12 mb-12">
        <div class="max-w-2xl">
            <h1 class="text-5xl font-bold mb-4">Welcome to LaravelStore</h1>
            <p class="text-xl mb-8">Discover amazing products at great prices. Shop with confidence!</p>
            <a href="{{ route('products.index') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold text-lg hover:bg-gray-100 transition duration-300">
                Shop Now
            </a>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="mb-16">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Featured Products</h2>
            <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <!-- Product Image - Clickable -->
                <a href="{{ route('products.show', $product) }}">
                    <div class="w-full h-48 bg-gray-100 flex items-center justify-center overflow-hidden">
                        @if($product->images && $product->images->count() > 0)
                        @php
                        $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                        @endphp
                        <img src="{{ asset('storage/' . $primaryImage->image_path) }}"
                            alt="{{ $product->name }}"
                            class="w-full h-full object-cover hover:scale-105 transition duration-300"
                            loading="lazy">
                        @else
                        <div class="text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm">No image</p>
                        </div>
                        @endif
                    </div>
                </a>
                <div class="p-4">
                    <span class="text-sm text-gray-500">{{ $product->category->name }}</span>
                    <!-- Product Name - Clickable -->
                    <a href="{{ route('products.show', $product) }}">
                        <h3 class="font-semibold text-lg mb-2 hover:text-blue-600">{{ $product->name }}</h3>
                    </a>
                    <div class="flex items-center justify-between">
                        <div>
                            @if($product->isOnSale)
                            <span class="text-lg font-bold text-gray-800">${{ $product->sale_price }}</span>
                            <span class="text-sm text-gray-500 line-through ml-2">${{ $product->price }}</span>
                            @else
                            <span class="text-lg font-bold text-gray-800">${{ $product->price }}</span>
                            @endif
                        </div>
                        @auth
                        <form action="{{ route('cart.add', $product) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition duration-300">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </form>
                        @else
                        <a href="{{ route('login') }}" class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600 transition duration-300 text-center">
                            <i class="fas fa-cart-plus"></i>
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Categories -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Shop by Category</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($categories as $category)
            <a href="{{ route('categories.show', $category) }}" class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition duration-300 block">
                <div class="flex justify-center mb-3">
                    @if($category->image)
                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-12 h-12 rounded-full object-cover">
                    @else
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-folder text-blue-600"></i>
                        </div>
                    @endif
                </div>
                <h3 class="font-semibold text-gray-800">{{ $category->name }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $category->products_count }} products</p>
            </a>
            @endforeach
        </div>
    </section>

    <!-- New Arrivals -->
    <section>
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">New Arrivals</h2>
            <a href="{{ route('products.index') }}?sort=newest" class="text-blue-600 hover:text-blue-800 font-semibold">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($newArrivals as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <!-- Product Image - Clickable -->
                <a href="{{ route('products.show', $product) }}">
                    <div class="w-full h-48 bg-gray-100 flex items-center justify-center overflow-hidden">
                        @if($product->images && $product->images->count() > 0)
                        @php
                        $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                        @endphp
                        <img src="{{ asset('storage/' . $primaryImage->image_path) }}"
                            alt="{{ $product->name }}"
                            class="w-full h-full object-cover hover:scale-105 transition duration-300"
                            loading="lazy">
                        @else
                        <div class="text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm">No image</p>
                        </div>
                        @endif
                    </div>
                </a>
                <div class="p-4">
                    <span class="text-sm text-gray-500">{{ $product->category->name }}</span>
                    <!-- Product Name - Clickable -->
                    <a href="{{ route('products.show', $product) }}">
                        <h3 class="font-semibold text-lg mb-2 hover:text-blue-600">{{ $product->name }}</h3>
                    </a>
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-bold text-gray-800">${{ $product->price }}</span>
                        <form action="{{ route('cart.add', $product) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition duration-300">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
</div>

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
    // Show popup if redirected with success
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('success-popup').classList.remove('hidden');
    });
</script>
@endif
@endsection