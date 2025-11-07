<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'low_stock_products' => Product::where('stock', '<', 10)->count(),
        ];

        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $recentProducts = Product::with('category')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentProducts'));
    }
}
