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
    
    <!-- Search, Filter and Sort Section -->
    <div class="bg-white p-4 mb-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <form method="GET" class="flex">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search products by name, SKU, or description..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-md hover:bg-blue-600">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            
            <!-- Filter -->
            <div>
                <select name="filter" onchange="updateFilter(this.value)" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Products</option>
                    <option value="low-stock" {{ request('filter') == 'low-stock' ? 'selected' : '' }}>Low Stock (<10)</option>
                    <option value="out-of-stock" {{ request('filter') == 'out-of-stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            
            <!-- Sort -->
            <div>
                <select name="sort" onchange="updateSort(this.value)" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                    <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Price Low-High</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price High-Low</option>
                    <option value="stock" {{ request('sort') == 'stock' ? 'selected' : '' }}>Stock Low-High</option>
                    <option value="stock_desc" {{ request('sort') == 'stock_desc' ? 'selected' : '' }}>Stock High-Low</option>
                    <option value="category" {{ request('sort') == 'category' ? 'selected' : '' }}>By Category</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Results Info and Pagination Controls -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="text-sm text-gray-600 mb-2 sm:mb-0">
            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
        </div>
        
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">Per page:</span>
            <select onchange="updatePerPage(this.value)" class="text-sm border border-gray-300 rounded px-2 py-1">
                <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5</option>
                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>
    </div>
    
    <!-- Active Filters -->
    @if(request()->anyFilled(['search', 'filter']))
    <div class="mb-4 p-3 bg-blue-50 rounded-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="text-sm text-blue-800">Active filters:</span>
                @if(request('search'))
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Search: "{{ request('search') }}"
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-1 hover:text-blue-600">
                        &times;
                    </a>
                </span>
                @endif
                @if(request('filter'))
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    @if(request('filter') == 'low-stock')
                        Low Stock (<10)
                    @elseif(request('filter') == 'out-of-stock')
                        Out of Stock
                    @endif
                    <a href="{{ request()->fullUrlWithQuery(['filter' => null]) }}" class="ml-1 hover:text-yellow-600">
                        &times;
                    </a>
                </span>
                @endif
            </div>
            <a href="{{ route('admin.products.index') }}" class="text-sm text-red-600 hover:text-red-800">
                Clear All
            </a>
        </div>
    </div>
    @endif

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

<script>
    function updateFilter(filterValue) {
        const url = new URL(window.location);
        if (filterValue) {
            url.searchParams.set('filter', filterValue);
        } else {
            url.searchParams.delete('filter');
        }
        url.searchParams.delete('page'); // Reset to first page when filter changes
        window.location = url.toString();
    }
    
    function updateSort(sortValue) {
        const url = new URL(window.location);
        if (sortValue) {
            url.searchParams.set('sort', sortValue);
        } else {
            url.searchParams.delete('sort');
        }
        url.searchParams.delete('page'); // Reset to first page when sort changes
        window.location = url.toString();
    }
    
    function updatePerPage(perPage) {
        const url = new URL(window.location);
        if (perPage) {
            url.searchParams.set('per_page', perPage);
        } else {
            url.searchParams.delete('per_page');
        }
        url.searchParams.delete('page'); // Reset to first page when per_page changes
        window.location = url.toString();
    }
</script>
@endsection