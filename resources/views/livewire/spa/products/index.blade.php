<div class="max-w-6xl mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-8">All Products</h1>

    <!-- Search and Filter Bar -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form wire:submit.prevent class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-6">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Products</label>
                <input type="text" 
                       wire:model.live="search"
                       placeholder="Search by name or description..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Category Filter -->
            <div class="flex-1">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select wire:model.live="category" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->slug }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Price Range -->
            <div class="flex space-x-2">
                <div>
                    <label for="min_price" class="block text-sm font-medium text-gray-700 mb-1">Min Price</label>
                    <input type="number" 
                           wire:model.live="minPrice"
                           placeholder="0" min="0"
                           class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="max_price" class="block text-sm font-medium text-gray-700 mb-1">Max Price</label>
                    <input type="number" 
                           wire:model.live="maxPrice"
                           placeholder="1000" min="0"
                           class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Sort -->
            <div>
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                <select wire:model.live="sort" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="newest">Newest First</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                    <option value="name">Name: A to Z</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2">
                <button type="button" 
                        wire:click="clearFilters"
                        class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Clear
                </button>
            </div>
        </form>

        <!-- Active Filters -->
        @if($search || $category || $minPrice || $maxPrice || $sort !== 'newest')
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">Active filters:</span>
                    @if($search)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Search: "{{ $search }}"
                        <button wire:click="$set('search', '')" class="ml-1 hover:text-blue-600">
                            &times;
                        </button>
                    </span>
                    @endif
                    @if($category)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Category: {{ $categories->where('slug', $category)->first()->name ?? $category }}
                        <button wire:click="$set('category', '')" class="ml-1 hover:text-green-600">
                            &times;
                        </button>
                    </span>
                    @endif
                    @if($minPrice)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Min: ${{ $minPrice }}
                        <button wire:click="$set('minPrice', '')" class="ml-1 hover:text-yellow-600">
                            &times;
                        </button>
                    </span>
                    @endif
                    @if($maxPrice)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Max: ${{ $maxPrice }}
                        <button wire:click="$set('maxPrice', '')" class="ml-1 hover:text-yellow-600">
                            &times;
                        </button>
                    </span>
                    @endif
                    @if($sort !== 'newest')
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        Sort: 
                        @switch($sort)
                            @case('price_low')
                                Price: Low to High
                                @break
                            @case('price_high')
                                Price: High to Low
                                @break
                            @case('name')
                                Name: A to Z
                                @break
                        @endswitch
                        <button wire:click="$set('sort', 'newest')" class="ml-1 hover:text-purple-600">
                            &times;
                        </button>
                    </span>
                    @endif
                </div>
                <button wire:click="clearFilters" class="text-sm text-red-600 hover:text-red-800">
                    Clear All
                </button>
            </div>
        </div>
        @endif
    </div>

    <!-- Results Count -->
    <div class="mb-6 text-gray-600">
        Showing {{ $products->firstItem() }} - {{ $products->lastItem() }} of {{ $products->total() }} products
        @if($search || $category || $minPrice || $maxPrice)
        <span class="text-sm text-gray-500">(filtered)</span>
        @endif
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @foreach($products as $product)
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition duration-300">
            <!-- Product Image/Placeholder -->
            <div class="bg-gray-100 rounded-lg h-48 flex items-center justify-center mb-4 overflow-hidden">
                @if($product->images && $product->images->count() > 0)
                @php
                $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                @endphp
                <img src="{{ asset('storage/' . $primaryImage->image_path) }}"
                    alt="{{ $product->name }}"
                    class="w-full h-full object-cover hover:scale-105 transition duration-300">
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
            <button wire:click="navigateTo('products.show', {'slug': '{{ $product->slug }}'})" class="block mb-4 w-full text-left">
                <h3 class="font-semibold text-lg mb-2 hover:text-blue-600">{{ $product->name }}</h3>
                <p class="text-gray-600 mb-2">${{ number_format($product->price, 2) }}</p>
                <p class="text-sm text-gray-500 mb-2">{{ $product->category->name }}</p>
                <p class="text-sm text-gray-500">{{ Str::limit($product->description, 100) }}</p>
            </button>

            <!-- Action Buttons -->
            <div class="flex space-x-2">
                <!-- Add to Cart Button -->
                @auth
                <button wire:click="addToCart({{ $product->id }})" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition duration-300">
                    Add to Cart
                </button>
                @else
                <a href="{{ route('login') }}" class="block w-full bg-gray-500 text-white py-2 rounded hover:bg-gray-600 transition duration-300 text-center">
                    Login to Purchase
                </a>
                @endauth
            </div>
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
        <button wire:click="clearFilters" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-300">
            Clear Filters
        </button>
    </div>
    @endif

    <!-- Pagination -->
    @if($products->hasPages())
    <div class="flex justify-center mt-8">
        {{ $products->links() }}
    </div>
    @endif
</div>

<script>
    // Function to navigate to product detail
    window.addEventListener('livewire:init', () => {
        Livewire.on('navigateTo', (event) => {
            // Call the parent component's navigateTo method
            if (window.livewire_find) {
                const parent = window.livewire_find(component => component.name === 'spa.app');
                if (parent) {
                    parent.call('handleNavigateTo', event.detail.view, event.detail.params || {});
                }
            }
        });
        
        Livewire.on('cartUpdated', () => {
            // Update cart count in navigation
            if (window.updateCartCount) {
                updateCartCount();
            }
        });
    });
</script>