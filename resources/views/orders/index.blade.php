<!-- resources/views/orders/index.blade.php -->
@extends('layouts.app')

@section('title', 'Home - LaravelStore')

@section('content')
    <div class="max-w-6xl mx-auto py-8 px-4">
        <h1 class="text-3xl font-bold mb-2">My Orders</h1>
        <p class="text-gray-600 mb-8">View your order history and track your purchases.</p>

        @if($orders->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @foreach($orders as $order)
            <div class="border-b last:border-b-0">
                <div class="p-6 hover:bg-gray-50 transition duration-300">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="font-semibold text-lg">Order #{{ $order->order_number }}</h3>
                            <p class="text-gray-600 text-sm">Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-lg">${{ number_format($order->total, 2) }}</p>
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($order->status == 'completed') bg-green-100 text-green-800
                                    @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">Items</h4>
                            <div class="space-y-2">
                                @foreach($order->items as $item)
                                <div class="flex justify-between text-sm">
                                    <span>{{ $item->product->name }} × {{ $item->quantity }}</span>
                                    <span>${{ number_format($item->total, 2) }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">Shipping To</h4>
                            <div class="text-sm text-gray-600 whitespace-pre-line">
                                {{ $order->shipping_address }}
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            {{ $order->items->count() }} item(s) •
                            @if($order->payment_status == 'paid')
                            <span class="text-green-600">Payment Completed</span>
                            @else
                            <span class="text-yellow-600">Payment Pending</span>
                            @endif
                        </div>
                        <a href="{{ route('orders.show', $order) }}"
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-300 text-sm">
                            View Order Details
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
        @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No orders yet</h3>
            <p class="text-gray-500 mb-6">You haven't placed any orders yet.</p>
            <a href="{{ route('products.index') }}" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition duration-300 font-semibold">
                Start Shopping
            </a>
        </div>
        @endif
    </div>
@endsection