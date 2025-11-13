<!-- resources/views/admin/dashboard.blade.php -->
@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg">
                <i class="fas fa-box text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Products</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $stats['total_products'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
                <i class="fas fa-shopping-bag text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Orders</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $stats['total_orders'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-lg">
                <i class="fas fa-users text-purple-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Users</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $stats['total_users'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-lg">
                <i class="fas fa-dollar-sign text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                <p class="text-2xl font-semibold text-gray-800">${{ number_format($stats['total_revenue'], 2) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Inventory Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-red-100 rounded-lg">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Low Stock Items</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $stats['low_stock_products'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-orange-100 rounded-lg">
                <i class="fas fa-times-circle text-orange-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Out of Stock</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $stats['out_of_stock_products'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-teal-100 rounded-lg">
                <i class="fas fa-wallet text-teal-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Inventory Value</p>
                <p class="text-2xl font-semibold text-gray-800">${{ number_format($stats['total_inventory_value'], 2) }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Recent Orders</h2>
        </div>
        <div class="p-6">
            @if($recentOrders->count() > 0)
            <div class="space-y-4">
                @foreach($recentOrders as $order)
                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                    <div>
                        <p class="font-semibold">#{{ $order->order_number }}</p>
                        <p class="text-sm text-gray-600">{{ $order->user->name ?? 'Guest' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">${{ number_format($order->total, 2) }}</p>
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full 
                                @if($order->status == 'completed') bg-green-100 text-green-800
                                @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                    View All Orders
                </a>
            </div>
            @else
            <p class="text-gray-500 text-center py-4">No orders yet.</p>
            @endif
        </div>
    </div>

    <!-- Recent Products -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Recent Products</h2>
        </div>
        <div class="p-6">
            @if($recentProducts->count() > 0)
            <div class="space-y-4">
                @foreach($recentProducts as $product)
                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                    <div>
                        <p class="font-semibold">{{ $product->name }}</p>
                        <p class="text-sm text-gray-600">{{ $product->category->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">${{ number_format($product->price, 2) }}</p>
                        <span class="text-sm text-gray-600">Stock: {{ $product->stock }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                    View All Products
                </a>
            </div>
            @else
            <p class="text-gray-500 text-center py-4">No products yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection