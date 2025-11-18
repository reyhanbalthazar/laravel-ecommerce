<?php

namespace App\Livewire\Spa;

use App\Models\Product;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class App extends Component
{
    public $currentView = 'home';
    public $routeParams = [];

    protected $listeners = [
        'navigateTo' => 'handleNavigateTo',
        'addToCart' => 'handleAddToCart',
    ];

    public function mount()
    {
        $currentPath = request()->path();

        if ($currentPath === '') {
            $this->currentView = 'home';
        } elseif ($currentPath === 'products') {
            $this->currentView = 'products.index';
        } elseif (str_starts_with($currentPath, 'products/')) {
            $slug = str_replace('products/', '', $currentPath);
            $this->currentView = 'products.show';
            $this->routeParams = ['slug' => $slug];
        } elseif (str_starts_with($currentPath, 'category/')) {
            $slug = str_replace('category/', '', $currentPath);
            $this->currentView = 'categories.show';
            $this->routeParams = ['slug' => $slug];
        } elseif ($currentPath === 'cart') {
            $this->currentView = 'cart.index';
        } elseif ($currentPath === 'wishlist') {
            $this->currentView = 'wishlist.index';
        } elseif ($currentPath === 'orders') {
            $this->currentView = 'orders.index';
        } else {
            $this->currentView = 'home'; // Default to home
        }
    }

    public function handleNavigateTo($view, $params = [])
    {
        $this->currentView = $view;
        $this->routeParams = $params;

        // Update the browser URL without page refresh
        $url = $this->getViewUrl($view, $params);
        $this->js("window.history.pushState({}, '', '$url')");

        // Dispatch an event to notify other components of URL change
        $this->dispatch('urlChanged', view: $view, params: $params);
    }

    public function handleAddToCart($productId, $quantity = 1)
    {
        $product = Product::findOrFail($productId);

        // Get the current cart from session
        $cart = session()->get('cart', []);

        // If the product is already in the cart, increment the quantity
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'image' => $product->images->first()?->image_path ?? null,
            ];
        }

        // Update the session
        session()->put('cart', $cart);

        // Dispatch an event to notify the cart count needs updating
        $this->dispatch('cartUpdated');

        // Show a success message
        $this->dispatch('productAddedToCart', message: 'Product added to cart successfully!');
    }

    private function getViewUrl($view, $params = [])
    {
        switch ($view) {
            case 'home':
                return '/';
            case 'products.index':
                return '/products';
            case 'products.show':
                return '/products/' . ($params['slug'] ?? '');
            case 'categories.show':
                return '/category/' . ($params['slug'] ?? '');
            case 'cart.index':
                return '/cart';
            case 'wishlist.index':
                return '/wishlist';
            case 'orders.index':
                return '/orders';
            default:
                return '/';
        }
    }

    public function render()
    {
        return view('livewire.spa.app');
    }
}
