<?php
// routes/web.php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'home'])->name('home');

// Product routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Category routes
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index'); // ADD THIS LINE
Route::get('/category/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{product}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout and orders (protected)
Route::middleware(['auth'])->group(function () {
    // Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    // Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    // Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    // Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

// Admin routes (protected and admin only)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    // Route::resource('/products', AdminController::class);
    // Route::resource('/categories', AdminController::class);
    // Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
});

// Authentication routes (from Breeze)
require __DIR__.'/auth.php';