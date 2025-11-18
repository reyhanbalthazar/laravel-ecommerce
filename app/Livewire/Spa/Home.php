<?php

namespace App\Livewire\Spa;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

class Home extends Component
{
    public function navigateTo($view, $params = [])
    {
        $this->dispatch('navigateTo', view: $view, params: $params);
    }

    public function addToCart($productId)
    {
        $this->dispatch('addToCart', productId: $productId, quantity: 1);
    }

    public function render()
    {
        $featuredProducts = Product::where('is_featured', true)
            ->with(['images', 'category'])
            ->limit(8)
            ->get();

        $newArrivals = Product::with(['images', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        $categories = Category::withCount('products')
            ->get();

        return view('livewire.spa.home', [
            'featuredProducts' => $featuredProducts,
            'newArrivals' => $newArrivals,
            'categories' => $categories
        ]);
    }
}
