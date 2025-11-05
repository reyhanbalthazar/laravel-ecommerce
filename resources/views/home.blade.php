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
                <img src="https://via.placeholder.com/300x200?text=Product+Image" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                <div class="p-4">
                    <span class="text-sm text-gray-500">{{ $product->category->name }}</span>
                    <h3 class="font-semibold text-lg mb-2">{{ $product->name }}</h3>
                    <div class="flex items-center justify-between">
                        <div>
                            @if($product->isOnSale)
                            <span class="text-lg font-bold text-gray-800">${{ $product->sale_price }}</span>
                            <span class="text-sm text-gray-500 line-through ml-2">${{ $product->price }}</span>
                            @else
                            <span class="text-lg font-bold text-gray-800">${{ $product->price }}</span>
                            @endif
                        </div>
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

    <!-- Categories -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Shop by Category</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($categories as $category)
            <a href="{{ route('categories.show', $category) }}" class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition duration-300">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-folder text-blue-600"></i>
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
                <img src="https://via.placeholder.com/300x200?text=Product+Image" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                <div class="p-4">
                    <span class="text-sm text-gray-500">{{ $product->category->name }}</span>
                    <h3 class="font-semibold text-lg mb-2">{{ $product->name }}</h3>
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
@endsection