<!-- resources/views/products/show.blade.php -->
@extends('layouts.app')

@section('title', 'Home - LaravelStore')

@section('content')

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

<div class="max-w-6xl mx-auto py-8 px-4">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm">
            <li>
                <a href="/" class="text-gray-500 hover:text-gray-700">Home</a>
            </li>
            <li>
                <span class="text-gray-400">/</span>
            </li>
            <li>
                <a href="/products" class="text-gray-500 hover:text-gray-700">Products</a>
            </li>
            <li>
                <span class="text-gray-400">/</span>
            </li>
            <li>
                <a href="/category/{{ $product->category->slug }}" class="text-gray-500 hover:text-gray-700">{{ $product->category->name }}</a>
            </li>
            <li>
                <span class="text-gray-400">/</span>
            </li>
            <li>
                <span class="text-gray-700 font-medium">{{ $product->name }}</span>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="md:flex">
            <!-- Product Images -->
            <div class="md:w-1/2 p-8">
                <!-- Main Image -->
                <div class="bg-gray-100 rounded-lg h-96 flex items-center justify-center mb-4 overflow-hidden">
                    @if($product->images && $product->images->count() > 0)
                    @php
                    $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                    @endphp
                    <img id="main-product-image"
                        src="{{ asset('storage/' . $primaryImage->image_path) }}"
                        alt="{{ $product->name }}"
                        class="max-h-80 max-w-full object-contain cursor-pointer hover:opacity-90 transition-opacity duration-200"
                        onclick="openModal(this.src)">
                    @else
                    <div class="text-center text-gray-400">
                        <svg class="w-24 h-24 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p>No image available</p>
                    </div>
                    @endif
                </div>

                <!-- Image Gallery (if multiple images exist) -->
                @if($product->images && $product->images->count() > 1)
                <div class="flex space-x-2 overflow-x-auto py-2">
                    @foreach($product->images as $image)
                    <button type="button"
                        onclick="changeMainImage('{{ asset('storage/' . $image->image_path) }}')"
                        class="flex-shrink-0 w-16 h-16 bg-gray-200 rounded border-2 {{ $image->is_primary ? 'border-blue-500' : 'border-gray-300' }} overflow-hidden hover:border-blue-400 transition-colors duration-200 relative">
                        <img src="{{ asset('storage/' . $image->image_path) }}"
                            alt="Thumbnail"
                            class="w-full h-full object-cover">
                        @if($image->is_primary)
                        <div class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                            ✓
                        </div>
                        @endif
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Simple Image Modal -->
            <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 hidden p-4">
                <div class="relative max-w-4xl max-h-full">
                    <!-- Close Button -->
                    <button onclick="closeModal()" class="absolute -top-12 right-0 text-white hover:text-gray-300 text-2xl z-10">
                        &times;
                    </button>

                    <!-- Modal Image -->
                    <img id="modalImage" src="" alt="Enlarged product image" class="max-w-full max-h-screen object-contain">
                </div>
            </div>

            <!-- Product Details -->
            <div class="md:w-1/2 p-8">
                <!-- Category Badge -->
                <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded mb-4">
                    {{ $product->category->name }}
                </span>

                <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $product->name }}</h1>

                <!-- Price -->
                <div class="mb-6">
                    @if($product->sale_price)
                    <div class="flex items-center space-x-2">
                        <span class="text-3xl font-bold text-gray-800">${{ number_format($product->sale_price, 2) }}</span>
                        <span class="text-xl text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                        <span class="bg-red-500 text-white text-sm font-semibold px-2 py-1 rounded">Save ${{ number_format($product->price - $product->sale_price, 2) }}</span>
                    </div>
                    @else
                    <span class="text-3xl font-bold text-gray-800">${{ number_format($product->price, 2) }}</span>
                    @endif
                </div>

                <!-- Stock Status -->
                <div class="mb-6">
                    @if($product->stock > 0)
                    <span class="inline-flex items-center text-sm text-green-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        In Stock ({{ $product->stock }} available)
                    </span>
                    @else
                    <span class="inline-flex items-center text-sm text-red-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Out of Stock
                    </span>
                    @endif
                </div>

                <!-- Add to Cart Form -->
                @if($product->stock > 0)
                <form action="{{ route('cart.add', $product) }}" method="POST" class="mb-6">
                    @csrf
                    <div class="flex space-x-4 mb-4">
                        <!-- Quantity Selector -->
                        <div class="flex items-center">
                            <label for="quantity" class="text-sm font-medium text-gray-700 mr-2">Quantity:</label>
                            <select name="quantity" id="quantity" class="border border-gray-300 rounded px-3 py-2">
                                @for($i = 1; $i <= min($product->stock, 10); $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-500 text-white py-3 px-6 rounded-lg hover:bg-blue-600 transition duration-300 font-semibold text-lg">
                        Add to Cart
                    </button>
                </form>
                @else
                <button disabled class="w-full bg-gray-400 text-white py-3 px-6 rounded-lg cursor-not-allowed font-semibold text-lg">
                    Out of Stock
                </button>
                @endif

                <!-- Add to Wishlist Button -->
                @auth
                <div class="mb-6">
                    <button id="wishlist-button"
                            class="w-full bg-{{ $isInWishlist ? 'red-100 text-red-600' : 'gray-200 text-gray-800' }} py-3 px-6 rounded-lg hover:bg-{{ $isInWishlist ? 'red-200' : 'gray-300' }} transition duration-300 font-semibold text-lg flex items-center justify-center"
                            onclick="toggleWishlist({{ $product->id }})">
                        <svg id="wishlist-icon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isInWishlist ? 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' : 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' }}"></path>
                        </svg>
                        <span id="wishlist-text">{{ $isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}</span>
                    </button>
                </div>
                @endauth

                <!-- Product Features -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Product Details</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Free shipping on orders over $100
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            30-day return policy
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Secure checkout
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <div class="border-t border-gray-200">
            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Description</h2>
                <div class="prose max-w-none text-gray-600">
                    <p>{{ $product->description }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Related Products</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
            <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition duration-300">
                <div class="bg-gray-100 rounded-lg h-32 flex items-center justify-center mb-3 overflow-hidden">
                    @if($relatedProduct->images && $relatedProduct->images->count() > 0)
                    @php
                    $relatedPrimaryImage = $relatedProduct->images->where('is_primary', true)->first() ?? $relatedProduct->images->first();
                    @endphp
                    <img src="{{ asset('storage/' . $relatedPrimaryImage->image_path) }}"
                        alt="{{ $relatedProduct->name }}"
                        class="w-full h-full object-cover hover:scale-105 transition duration-300">
                    @else
                    <div class="text-center text-gray-400">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    @endif
                </div>
                <h3 class="font-semibold text-sm mb-1">{{ Str::limit($relatedProduct->name, 40) }}</h3>
                <p class="text-gray-600 text-sm mb-2">${{ number_format($relatedProduct->price, 2) }}</p>
                <a href="{{ route('products.show', $relatedProduct) }}" class="block w-full bg-gray-100 text-gray-700 text-center py-1 rounded text-sm hover:bg-gray-200 transition">
                    View Details
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
    // Simple modal functions
    function openModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');

        modalImage.src = imageSrc;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    }

    function closeModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
        document.body.style.overflow = ''; // Restore scrolling
    }

    // Your existing functions - UPDATED
    function changeMainImage(imageUrl) {
        const mainImage = document.getElementById('main-product-image');
        mainImage.src = imageUrl;

        // Update the onclick event to use the new image URL
        mainImage.setAttribute('onclick', `openModal('${imageUrl}')`);

        // Update active thumbnail borders
        const thumbnails = document.querySelectorAll('[onclick^="changeMainImage"]');
        thumbnails.forEach(thumb => {
            if (thumb.querySelector('img').src === imageUrl) {
                thumb.classList.remove('border-gray-300');
                thumb.classList.add('border-blue-500');
            } else {
                thumb.classList.remove('border-blue-500');
                thumb.classList.add('border-gray-300');
            }
        });
    }

    function hidePopup() {
        document.getElementById('success-popup').classList.add('hidden');
    }

    // Close modal when clicking outside the image
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    // Close success popup when clicking outside
    document.getElementById('success-popup').addEventListener('click', function(e) {
        if (e.target === this) {
            hidePopup();
        }
    });
    // Add this to resources/views/products/show.blade.php
    function changeMainImage(imageUrl) {
        document.getElementById('main-product-image').src = imageUrl;

        // Update active thumbnail borders
        const thumbnails = document.querySelectorAll('[onclick^="changeMainImage"]');
        thumbnails.forEach(thumb => {
            if (thumb.querySelector('img').src === imageUrl) {
                thumb.classList.remove('border-gray-300');
                thumb.classList.add('border-blue-500');
            } else {
                thumb.classList.remove('border-blue-500');
                thumb.classList.add('border-gray-300');
            }
        });
    }

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

    // Wishlist AJAX functions
    async function toggleWishlist(productId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        try {
            const response = await fetch(`/wishlist/toggle/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const contentType = response.headers.get('content-type');
            
            // Check if we're getting a redirect/html response instead of JSON
            if (!contentType || !contentType.includes('application/json')) {
                // If HTML is returned, it's likely a redirect to login
                if (response.redirected || response.status === 302) {
                    window.location.href = '/login';
                } else {
                    // If we got HTML but no redirect, try to read it for debugging
                    const responseText = await response.text();
                    console.error('Expected JSON but got HTML response:', responseText.substring(0, 200));
                    window.location.href = '/login';
                }
                return;
            }
            
            const result = await response.json();

            // Check if the response indicates an authentication issue
            if (!result.success && result.redirect) {
                window.location.href = result.redirect;
                return;
            }

            if (result.success) {
                updateWishlistButton(result.in_wishlist);
                
                // Show a temporary message
                const wishlistText = document.getElementById('wishlist-text');
                wishlistText.textContent = result.message;
                
                // Reset the text after 2 seconds
                setTimeout(() => {
                    wishlistText.textContent = result.in_wishlist ? 'Remove from Wishlist' : 'Add to Wishlist';
                }, 2000);
                
                // Update wishlist count in header if exists
                updateWishlistCount();
            }
        } catch (error) {
            console.error('Error toggling wishlist:', error);
        }
    }

    function updateWishlistButton(inWishlist) {
        const wishlistIcon = document.getElementById('wishlist-icon');
        const wishlistText = document.getElementById('wishlist-text');
        const wishlistButton = document.getElementById('wishlist-button');
        
        if (inWishlist) {
            // Product is in wishlist - change to filled heart
            wishlistIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>';
            wishlistIcon.parentElement.classList.remove('bg-gray-200', 'text-gray-800');
            wishlistIcon.parentElement.classList.add('bg-red-100', 'text-red-600');
            wishlistText.textContent = 'Remove from Wishlist';
        } else {
            // Product is not in wishlist - change to outline heart
            wishlistIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>';
            wishlistIcon.parentElement.classList.remove('bg-red-100', 'text-red-600');
            wishlistIcon.parentElement.classList.add('bg-gray-200', 'text-gray-800');
            wishlistText.textContent = 'Add to Wishlist';
        }
    }

    async function updateWishlistCount() {
        try {
            const response = await fetch('/wishlist/count');
            const result = await response.json();
            
            // Update the wishlist count in the header if exists
            const wishlistCountElement = document.querySelector('.wishlist-count');
            if (wishlistCountElement) {
                wishlistCountElement.textContent = result.count;
                wishlistCountElement.style.display = result.count > 0 ? 'inline' : 'none';
            }
        } catch (error) {
            console.error('Error updating wishlist count:', error);
        }
    }

    // Initialize button state based on server-rendered content
    document.addEventListener('DOMContentLoaded', function() {
        // Update the wishlist count
        updateWishlistCount();
    });
</script>
@if (session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('success-popup').classList.remove('hidden');
    });
</script>
@endif

@endsection