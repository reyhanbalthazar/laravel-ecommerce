<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-700 rounded-2xl text-white p-12 mb-12">
        <div class="max-w-2xl">
            <h1 class="text-5xl font-bold mb-4">Welcome to LaravelStore</h1>
            <p class="text-xl mb-8">Discover amazing products at great prices. Shop with confidence!</p>
            <button wire:click="navigateTo('products.index')" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold text-lg hover:bg-gray-100 transition duration-300">
                Shop Now
            </button>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="mb-16">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Featured Products</h2>
            <button wire:click="navigateTo('products.index')" class="text-blue-600 hover:text-blue-800 font-semibold">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <!-- Product Image - Clickable -->
                <button wire:click="navigateTo('products.show', {'slug': '{{ $product->slug }}'})" class="w-full">
                    <div class="w-full h-48 bg-gray-100 flex items-center justify-center overflow-hidden">
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
                </button>
                <div class="p-4">
                    <span class="text-sm text-gray-500">{{ $product->category->name }}</span>
                    <!-- Product Name - Clickable -->
                    <button wire:click="navigateTo('products.show', {'slug': '{{ $product->slug }}'})" class="w-full text-left">
                        <h3 class="font-semibold text-lg mb-2 hover:text-blue-600">{{ $product->name }}</h3>
                    </button>
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
                        <button wire:click="addToCart({{ $product->id }})" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition duration-300">
                            <i class="fas fa-cart-plus"></i>
                        </button>
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
            <button
                wire:click="navigateTo('categories.show', {'slug': '{{ $category->slug }}'})"
                class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition duration-300">
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
            </button>
            @endforeach
        </div>
    </section>

    <!-- New Arrivals -->
    <section>
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">New Arrivals</h2>
            <button wire:click="navigateTo('products.index', {'sort': 'newest'})" class="text-blue-600 hover:text-blue-800 font-semibold">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($newArrivals as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <!-- Product Image - Clickable -->
                <button wire:click="navigateTo('products.show', {'slug': '{{ $product->slug }}'})" class="w-full">
                    <div class="w-full h-48 bg-gray-100 flex items-center justify-center overflow-hidden">
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
                </button>
                <div class="p-4">
                    <span class="text-sm text-gray-500">{{ $product->category->name }}</span>
                    <!-- Product Name - Clickable -->
                    <button wire:click="navigateTo('products.show', {'slug': '{{ $product->slug }}'})" class="w-full text-left">
                        <h3 class="font-semibold text-lg mb-2 hover:text-blue-600">{{ $product->name }}</h3>
                    </button>
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-bold text-gray-800">${{ $product->price }}</span>
                        @auth
                        <button wire:click="addToCart({{ $product->id }})" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition duration-300">
                            <i class="fas fa-cart-plus"></i>
                        </button>
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
</div>

<script>
    // Function to add product to cart
    window.addEventListener('livewire:init', () => {
        Livewire.on('productAddedToCart', (event) => {
            // Show success message
            alert(event.message);
        });
    });
</script>
