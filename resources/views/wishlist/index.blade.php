<!-- resources/views/wishlist/index.blade.php -->
@extends('layouts.app')

@section('title', 'My Wishlist - LaravelStore')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-8">My Wishlist</h1>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        {{ session('error') }}
    </div>
    @endif

    @if(session('info'))
    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
        {{ session('info') }}
    </div>
    @endif

    @if($products->count() > 0)
        <!-- Wishlist Actions -->
        <div class="flex justify-between items-center mb-6">
            <p class="text-gray-600">{{ $products->count() }} item(s) in your wishlist</p>
            <form action="{{ route('wishlist.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear your entire wishlist?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-500 hover:text-red-700 font-medium">
                    Clear Wishlist
                </button>
            </form>
        </div>

        <!-- Wishlist Items Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
            <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition duration-300">
                <!-- Product Image -->
                <div class="bg-gray-100 rounded-lg h-48 flex items-center justify-center mb-4 overflow-hidden">
                    @if($product->images && $product->images->count() > 0)
                        @php
                        $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                        @endphp
                        <a href="{{ route('products.show', $product) }}">
                            <img src="{{ asset('storage/' . $primaryImage->image_path) }}"
                                alt="{{ $product->name }}"
                                class="w-full h-full object-cover hover:scale-105 transition duration-300">
                        </a>
                    @else
                        <div class="text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm">No image</p>
                        </div>
                    @endif
                </div>

                <!-- Product Info -->
                <a href="{{ route('products.show', $product) }}" class="block mb-2">
                    <h3 class="font-semibold text-lg hover:text-blue-600">{{ $product->name }}</h3>
                    <p class="text-gray-600 mb-2">${{ number_format($product->price, 2) }}</p>
                    <p class="text-sm text-gray-500 mb-2">{{ $product->category->name }}</p>
                </a>

                <!-- Actions -->
                <div class="flex justify-between space-x-2">
                    <!-- Add to Cart Button -->
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition duration-300 text-sm">
                            Add to Cart
                        </button>
                    </form>

                    <!-- Remove from Wishlist -->
                    <form action="{{ route('wishlist.remove', $product->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('POST')
                        <button type="submit" class="w-full bg-red-500 text-white py-2 rounded hover:bg-red-600 transition duration-300 text-sm" title="Remove from wishlist">
                            Remove
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="flex justify-center mt-8">
            {{ $products->links() }}
        </div>
        @endif
    @else
        <!-- Empty Wishlist -->
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Your wishlist is empty</h3>
            <p class="text-gray-500 mb-6">Add items to your wishlist to save them for later.</p>
            <a href="{{ route('products.index') }}" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-300">
                Browse Products
            </a>
        </div>
    @endif
</div>
@endsection