<?php

namespace App\Livewire\Spa\Cart;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $cart = session()->get('cart', []);
        $cartItems = collect($cart)->map(function ($item) {
            return (object) $item;
        })->values();

        $total = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return view('livewire.spa.cart.index', [
            'cartItems' => $cartItems,
            'total' => $total
        ]);
    }

    public function removeFromCart($productId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        $this->dispatch('cartUpdated');
    }

    public function updateQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }

        $this->dispatch('cartUpdated');
    }
}
