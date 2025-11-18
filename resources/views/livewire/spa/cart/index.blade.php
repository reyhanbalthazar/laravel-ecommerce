<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-8">Your Cart</h1>

    @if(count($cartItems) > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($cartItems as $item)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($item->image)
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-md object-cover" src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                            </div>
                            @endif
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${{ number_format($item->price, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex items-center">
                            <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" 
                                    class="px-2 py-1 bg-gray-200 rounded-l">-</button>
                            <span class="px-3 py-1 border-t border-b border-gray-200">{{ $item->quantity }}</span>
                            <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" 
                                    class="px-2 py-1 bg-gray-200 rounded-r">+</button>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${{ number_format($item->price * $item->quantity, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <button wire:click="removeFromCart({{ $item->id }})" 
                                class="text-red-600 hover:text-red-900">Remove</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-8 flex justify-end">
        <div class="text-right">
            <div class="text-lg font-semibold">Total: ${{ number_format($total, 2) }}</div>
            <button class="mt-4 bg-blue-500 text-white px-6 py-3 rounded hover:bg-blue-600">
                Proceed to Checkout
            </button>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Your cart is empty</h3>
        <p class="text-gray-500 mb-6">Start adding some products to your cart.</p>
        <button wire:click="navigateTo('products.index')" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
            Shop Now
        </button>
    </div>
    @endif
</div>

<script>
    window.addEventListener('livewire:init', () => {
        Livewire.on('navigateTo', (event) => {
            if (window.livewire_find) {
                const parent = window.livewire_find(component => component.name === 'spa.app');
                if (parent) {
                    parent.call('handleNavigateTo', event.detail.view, event.detail.params || {});
                }
            }
        });
    });
</script>