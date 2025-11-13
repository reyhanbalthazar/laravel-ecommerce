@extends('admin.layout')

@section('title', 'Products')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Products</h1>
        <div class="flex space-x-3">
            <a href="{{ route('admin.products.trashed') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                View Trash
            </a>
            <a href="{{ route('admin.products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                Add New Product
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif
    
    <!-- Filter Section -->
    <div class="flex flex-wrap items-center justify-between mb-6 p-4 bg-gray-50 rounded-lg">
        <div class="flex space-x-2 mb-2 sm:mb-0">
            <a href="{{ route('admin.products.index') }}" 
               class="px-3 py-1 text-sm rounded {{ request('filter') == null ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 border' }}">
                All Products
            </a>
            <a href="{{ route('admin.products.index', ['filter' => 'low-stock']) }}" 
               class="px-3 py-1 text-sm rounded {{ request('filter') == 'low-stock' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-700 border' }}">
                Low Stock (<10)
            </a>
            <a href="{{ route('admin.products.index', ['filter' => 'out-of-stock']) }}" 
               class="px-3 py-1 text-sm rounded {{ request('filter') == 'out-of-stock' ? 'bg-red-500 text-white' : 'bg-white text-gray-700 border' }}">
                Out of Stock
            </a>
        </div>
        
        <div class="text-sm text-gray-600">
            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                            <div class="text-sm text-gray-500">{{ $product->sku }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->category?->name ?? 'Uncategorized' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${{ number_format($product->price, 2) }}
                            @if($product->is_on_sale)
                                <span class="text-red-600 text-xs ml-1">(On Sale: ${{ number_format($product->sale_price, 2) }})</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($product->stock < 10)
                                <span class="font-bold {{ $product->stock == 0 ? 'text-red-600' : 'text-orange-500' }}">
                                    {{ $product->stock }}
                                    @if($product->stock == 0)
                                        <i class="fas fa-exclamation-circle ml-1 text-red-500" title="Out of Stock"></i>
                                    @else
                                        <i class="fas fa-exclamation-triangle ml-1 text-orange-500" title="Low Stock"></i>
                                    @endif
                                </span>
                            @else
                                <span class="text-gray-500">{{ $product->stock }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline-block" 
                                onsubmit="return confirm('Are you sure you want to delete this product?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            No products found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
</div>
@endsection