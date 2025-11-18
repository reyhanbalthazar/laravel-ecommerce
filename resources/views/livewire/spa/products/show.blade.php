<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-white rounded-lg shadow p-6">
        @if($product)
        <!-- Product Images -->
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Main Image -->
            <div class="md:w-1/2">
                <div class="bg-gray-100 rounded-lg h-96 flex items-center justify-center overflow-hidden">
                    @if($selectedImage)
                    <img src="{{ asset('storage/' . $selectedImage) }}" 
                         alt="{{ $product->name }}"
                         class="w-full h-full object-contain">
                    @else
                    <div class="text-center text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm">No image</p>
                    </div>
                    @endif
                </div>
                
                <!-- Thumbnail Images -->
                @if($product->images && $product->images->count() > 1)
                <div class="flex gap-2 mt-4">
                    @foreach($product->images as $image)
                    <div class="cursor-pointer" wire:click="selectedImage = '{{ $image->image_path }}'">
                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                             alt="{{ $product->name }}"
                             class="w-20 h-20 object-cover rounded border-2 {{ $selectedImage === $image->image_path ? 'border-blue-500' : 'border-transparent' }}">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            
            <!-- Product Details -->
            <div class="md:w-1/2">
                <h1 class="text-3xl font-bold mb-2">{{ $product->name }}</h1>
                <div class="flex items-center mb-4">
                    <span class="text-2xl font-bold text-gray-800">${{ $product->price }}</span>
                    @if($product->isOnSale)
                    <span class="ml-2 text-sm text-gray-500 line-through">${{ $product->original_price }}</span>
                    @endif
                </div>
                
                <div class="mb-4">
                    <span class="text-gray-600">Category: </span>
                    <span class="font-medium">{{ $product->category->name }}</span>
                </div>
                
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Description</h3>
                    <p class="text-gray-700">{{ $product->description }}</p>
                </div>
                
                <!-- Quantity and Add to Cart -->
                <div class="mb-6">
                    <div class="flex items-center">
                        <label class="mr-3">Quantity:</label>
                        <div class="flex items-center border rounded">
                            <button wire:click="quantity = quantity > 1 ? quantity - 1 : quantity" 
                                    class="px-3 py-1 bg-gray-200 hover:bg-gray-300">-</button>
                            <span class="px-4 py-1">{{ $quantity }}</span>
                            <button wire:click="quantity++" 
                                    class="px-3 py-1 bg-gray-200 hover:bg-gray-300">+</button>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-4">
                    @auth
                    <button wire:click="addToCart" 
                            class="flex-1 bg-blue-500 text-white py-3 rounded hover:bg-blue-600 transition duration-300">
                        Add to Cart
                    </button>
                    @else
                    <a href="{{ route('login') }}" 
                       class="flex-1 bg-blue-500 text-white py-3 rounded hover:bg-blue-600 transition duration-300 text-center block flex items-center justify-center">
                        Login to Add to Cart
                    </a>
                    @endauth
                </div>
            </div>
        </div>
        
        <!-- Product Details Tabs -->
        <div class="mt-12">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Details
                    </button>
                    <button class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Reviews
                    </button>
                </nav>
            </div>
            
            <div class="py-4">
                <p>{{ $product->description }}</p>
            </div>
        </div>
        @else
        <div class="text-center py-8">
            <h2 class="text-xl font-semibold">Product not found</h2>
            <p class="text-gray-600 mt-2">The product you're looking for doesn't exist.</p>
        </div>
        @endif
    </div>
</div>

<script>
    // Handle navigation and cart updates
    window.addEventListener('livewire:init', () => {
        Livewire.on('productAddedToCart', (event) => {
            alert(event.detail.message);
        });
        
        Livewire.on('cartUpdated', () => {
            // Update cart count in navigation
            if (window.updateCartCount) {
                updateCartCount();
            }
        });
    });
</script>