<!-- resources/views/categories/show.blade.php -->
@extends('layouts.app')

@section('title', $category->name . ' - LaravelStore')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Category Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li>
                    <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700">Home</a>
                </li>
                <li>
                    <span class="text-gray-400">/</span>
                </li>
                <li>
                    <a href="{{ route('categories.index') }}" class="text-gray-500 hover:text-gray-700">Categories</a>
                </li>
                <li>
                    <span class="text-gray-400">/</span>
                </li>
                <li>
                    <span class="text-gray-700 font-semibold">{{ $category->name }}</span>
                </li>
            </ol>
        </nav>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $category->name }}</h1>
                <p class="text-gray-600">{{ $category->description }}</p>
            </div>
            <div class="text-right">
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                    {{ $products->total() }} products
                </span>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($products as $product)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
            <a href="{{ route('products.show', $product) }}">
                <img src="https://via.placeholder.com/300x200?text=Product+Image" alt="{{ $product->name }}" class="w-full h-48 object-cover">
            </a>
            <div class="p-4">
                <span class="text-sm text-gray-500">{{ $product->category->name }}</span>
                <a href="{{ route('products.show', $product) }}">
                    <h3 class="font-semibold text-lg mb-2 hover:text-blue-600">{{ $product->name }}</h3>
                </a>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ Str::limit($product->description, 80) }}</p>
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
                @if(!$product->in_stock)
                <div class="mt-2">
                    <span class="text-red-500 text-sm">Out of Stock</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $products->links() }}
    </div>
    @else
    <div class="text-center py-12 bg-white rounded-lg shadow-md">
        <i class="fas fa-box-open text-gray-400 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">No Products Found</h3>
        <p class="text-gray-500 mb-4">There are no products in this category yet.</p>
        <a href="{{ route('products.index') }}" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-300">
            Browse All Products
        </a>
    </div>
    @endif
</div>
@endsection