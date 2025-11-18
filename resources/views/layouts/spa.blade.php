<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Store - @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow py-4">
        <div class="max-w-6xl mx-auto px-4 flex justify-between items-center">
            <button wire:click="navigateTo('home')" class="text-xl font-bold text-blue-600 hover:text-blue-800 cursor-pointer">LaravelStore</button>
            <div class="flex items-center space-x-4">
                @auth
                <span class="text-gray-600">Welcome, {{ auth()->user()->name }}</span>
                <button wire:click="navigateTo('orders.index')" class="text-gray-600 hover:text-gray-900 cursor-pointer">My Orders</button>
                <button wire:click="navigateTo('wishlist.index')" class="text-gray-600 hover:text-gray-900 relative cursor-pointer">
                    <i class="fas fa-heart"></i>
                    <!-- <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 text-[8px] flex items-center justify-center wishlist-count font-bold" style="min-width: 1rem; line-height: 1;">
                        0
                    </span> -->
                </button>
                <button wire:click="navigateTo('products.index')" class="text-gray-600 hover:text-gray-900 cursor-pointer">Products</button>
                <button wire:click="navigateTo('cart.index')" class="text-gray-600 hover:text-gray-900 relative cursor-pointer">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="absolute -top-1 -right-1 bg-blue-500 text-white rounded-full w-4 h-4 text-[8px] flex items-center justify-center cart-count font-bold" style="min-width: 1rem; line-height: 1;">
                        {{ array_sum(array_column(session('cart', []), 'quantity')) }}
                    </span>
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-600 hover:text-gray-900">Logout</button>
                </form>
                @else
                <button wire:click="navigateTo('products.index')" class="text-gray-600 hover:text-gray-900 cursor-pointer">Products</button>
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Login</a>
                <a href="{{ route('register') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content - SPA content will be loaded here -->
    <main>
        @yield('content')
        <div id="main-content">
            <!-- Livewire SPA component will be loaded here -->
            @livewire('spa.app')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-4">LaravelStore</h3>
                    <p class="text-gray-400">Your one-stop shop for all your needs.</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('home') }}" class="hover:text-white" wire:navigate>Home</a></li>
                        <li><a href="{{ route('products.index') }}" class="hover:text-white" wire:navigate>Products</a></li>
                        <li><a href="#" class="hover:text-white" wire:navigate>About Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Customer Service</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white" wire:navigate>Contact Us</a></li>
                        <li><a href="#" class="hover:text-white" wire:navigate>Shipping Info</a></li>
                        <li><a href="#" class="hover:text-white" wire:navigate>Returns</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Connect</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white" wire:navigate><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white" wire:navigate><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white" wire:navigate><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 LaravelStore. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
    <script>
        // Update cart count (only for logged-in users)
        function updateCartCount() {
            fetch('{{ route("cart.count") }}')
                .then(response => response.json())
                .then(data => {
                    document.querySelectorAll('.cart-count').forEach(el => {
                        el.textContent = data.count;
                    });
                });
        }

        // Update wishlist count (only for logged-in users)
        async function updateWishlistCount() {
            try {
                const response = await fetch('{{ route("wishlist.count") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const contentType = response.headers.get('content-type');

                // Check if we're getting a redirect/html response instead of JSON
                if (!contentType || !contentType.includes('application/json')) {
                    // If HTML is returned, redirect to login
                    window.location.href = '/login';
                    return;
                }

                const data = await response.json();

                // Check if the response indicates an authentication issue
                if (!data.success && data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

                document.querySelectorAll('.wishlist-count').forEach(el => {
                    el.textContent = data.count;
                    el.style.display = data.count > 0 ? 'inline' : 'none';
                });
            } catch (error) {
                console.error('Error updating wishlist count:', error);
            }
        }

        // Initialize counts on page load (only for logged-in users)
        @auth
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            updateWishlistCount();
        });
        @endauth
    </script>
</body>
</html>