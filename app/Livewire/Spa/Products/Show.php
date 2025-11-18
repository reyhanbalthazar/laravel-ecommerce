<?php

namespace App\Livewire\Spa\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Show extends Component
{
    public $product;
    public $quantity = 1;
    public $selectedImage;

    protected $queryString = ['slug'];

    public function mount($slug)
    {
        $this->product = Product::where('slug', $slug)
            ->with(['images', 'category', 'reviews.user'])
            ->firstOrFail();

        if ($this->product->images->count() > 0) {
            $this->selectedImage = $this->product->images->first()->image_path;
        }
    }

    public function updatedSelectedImage()
    {
        $this->selectedImage = $this->selectedImage;
    }

    public function addToCart()
    {
        $this->dispatch('addToCart', productId: $this->product->id, quantity: $this->quantity);
    }

    public function render()
    {
        return view('livewire.spa.products.show');
    }
}
