{{-- resources/views/admin/orders/show.blade.php --}}
@extends('admin.layout')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Order #{{ $order->order_number }}</h1>
            <p class="text-gray-600 mt-2">Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
            Back to Orders
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Order Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Order Status Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Order Status</h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    @if($order->status == 'completed') bg-green-100 text-green-800
                    @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                    @else bg-yellow-100 text-yellow-800 @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="flex items-center space-x-4">
                @csrf
                <select name="status" class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors">
                    Update Status
                </button>
            </form>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Order Items</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                            @if($item->product->images && $item->product->images->count() > 0)
                            @php
                            $primaryImage = $item->product->images->where('is_primary', true)->first() ?? $item->product->images->first();
                            @endphp
                            <img src="{{ asset('storage/' . $primaryImage->image_path) }}"
                                alt="{{ $item->product->name }}"
                                class="w-12 h-12 object-cover rounded">
                            @else
                            <i class="fas fa-box text-gray-400"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800">{{ $item->product->name }}</h3>
                            <p class="text-sm text-gray-600">SKU: {{ $item->product->sku }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-800">${{ number_format($item->unit_price, 2) }}</p>
                            <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                            <p class="font-semibold text-gray-800 mt-1">${{ number_format($item->total, 2) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Order Totals -->
                <div class="mt-6 pt-6 border-t border-gray-200 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium">${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax:</span>
                        <span class="font-medium">${{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Shipping:</span>
                        <span class="font-medium">${{ number_format($order->shipping, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-semibold pt-2 border-t border-gray-200">
                        <span>Total:</span>
                        <span>${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer & Shipping Information -->
    <div class="space-y-6">
        <!-- Customer Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-600">Name</p>
                    <p class="text-gray-800">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Email</p>
                    <p class="text-gray-800">{{ $order->customer_email }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Phone</p>
                    <p class="text-gray-800">{{ $order->customer_phone }}</p>
                </div>
                @if($order->user)
                <div>
                    <p class="text-sm font-medium text-gray-600">Account</p>
                    <p class="text-gray-800">{{ $order->user->name }} (Registered User)</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Payment Information</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-600">Payment Status</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($order->isPaid()) bg-green-100 text-green-800
                        @elseif($order->payment_status == 'failed' || $order->isCancelled()) bg-red-100 text-red-800
                        @elseif($order->payment_status == 'refunded' || $order->payment_status == 'partial_refund') bg-purple-100 text-purple-800
                        @elseif($order->isPending()) bg-yellow-100 text-yellow-800
                        @elseif($order->isExpired()) bg-orange-100 text-orange-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($order->payment_status ?: 'N/A') }}
                    </span>
                </div>
                @if($order->payment_method)
                <div>
                    <p class="text-sm font-medium text-gray-600">Payment Method</p>
                    <p class="text-gray-800">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                </div>
                @endif
                @if($order->transaction_id)
                <div>
                    <p class="text-sm font-medium text-gray-600">Transaction ID</p>
                    <p class="text-gray-800 font-mono text-sm">{{ $order->transaction_id }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Shipping Address</h2>
            <div class="space-y-2 text-gray-800">
                <p>{{ $order->customer_name }}</p>
                <p>{{ $order->customer_address }}</p>
                <p>{{ $order->customer_city_state_zip }}</p>
                <p>{{ $order->customer_country }}</p>
            </div>
        </div>

        <!-- Order Notes -->
        @if($order->customer_note)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Customer Note</h2>
            <p class="text-gray-800 bg-yellow-50 p-3 rounded-lg">{{ $order->customer_note }}</p>
        </div>
        @endif
    </div>
</div>
@endsection