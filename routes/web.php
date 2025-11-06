<?php
// routes/web.php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Public Routes
Route::get('/', [ProductController::class, 'home'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/category/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// Protected Routes (Require Authentication)
Route::middleware(['auth'])->group(function () {
    // Cart Routes (protected)
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{productId}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove/{productId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Checkout and Order Routes (protected)
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
});

// Public routes that don't require auth
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

// Add this temporarily to routes/web.php to debug
Route::get('/debug/cart-items', function () {
    $cart = session()->get('cart', []);
    $results = [];

    foreach ($cart as $productId => $item) {
        $product = \App\Models\Product::find($productId);
        $results[] = [
            'cart_product_id' => $productId,
            'cart_product_name' => $item['name'],
            'product_exists' => $product ? 'YES' : 'NO',
            'product_in_db' => $product ? $product->name : 'NOT FOUND'
        ];
    }

    return response()->json($results);
});
