<!-- resources/views/products/index.blade.php -->
@extends('layouts.app')

@section('title', 'Products - LaravelStore')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h3 class="font-bold text-lg mb-4">Filters</h3>

                <!-- Search -->
                <div class="mb-6">
                    <form action="{{ route('products.index') }}" method="GET">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search products..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </form>
                </div>

                <!-- Categories -->
                <div class="mb-6">
                    <h4 class="font-semibold mb-3">Categories</h4>
                    <div class="space-y-2">
                        <a href="{{ route('products.index') }}"
                            class="block text-sm {{ !request('category') ? 'text-blue-600 font-semibold' : 'text-gray-600' }}">
                            All Categories
                        </a>
                        @foreach($categories as $category)
                        <a href="{{ route('products.index') }}?category={{ $category->slug }}"
                            class="block text-sm {{ request('category') == $category->slug ? 'text-blue-600 font-semibold' : 'text-gray-600' }}">
                            {{ $category->name }}
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Price Range -->
                <div class="mb-6">
                    <h4 class="font-semibold mb-3">Price Range</h4>
                    <form action="{{ route('products.index') }}" method="GET">
                        <div class="flex gap-2 mb-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}"
                                placeholder="Min" class="w-1/2 px-2 py-1 border border-gray-300 rounded text-sm">
                            <input type="number" name="max_price" value="{{ request('max_price') }}"
                                placeholder="Max" class="w-1/2 px-2 py-1 border border-gray-300 rounded text-sm">
                        </div>
                        <button type="submit" class="w-full bg-blue-500 text-white py-1 rounded text-sm hover:bg-blue-600">
                            Apply
                        </button>
                    </form>
                </div>

                <!-- Clear Filters -->
                @if(request()->anyFilled(['search', 'category', 'min_price', 'max_price']))
                <a href="{{ route('products.index') }}" class="text-red-500 text-sm hover:text-red-700">
                    Clear All Filters
                </a>
                @endif
            </div>
        </div>

        <!-- Products Grid -->
        <div class="lg:w-3/4">
            <!-- Sorting and Results Count -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <p class="text-gray-600">Showing {{ $products->firstItem() }} - {{ $products->lastItem() }} of {{ $products->total() }} products</p>
                </div>
                <div>
                    <form action="{{ route('products.index') }}" method="GET">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        <select name="sort" onchange="this.form.submit()" class="border border-gray-300 rounded px-3 py-2">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Products -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
        </div>
    </div>
</div>
@endsection