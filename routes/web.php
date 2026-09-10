<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CheckoutController;

/*====================
    Public Routes
====================*/

// Home
Route::get('/', function () {
    return redirect('/login');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::middleware('customer')->group(function () {

    // Customer Products
    Route::get('/products', [ProductController::class, 'index'])
        ->name('products.index');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])
        ->name('cart.index');
    Route::post('/cart/{product}', [CartController::class, 'store'])
        ->name('cart.store');
    Route::patch('/cart/{product}', [CartController::class, 'update'])
        ->name('cart.update');
    Route::delete('/cart/{product}', [CartController::class, 'destroy'])
        ->name('cart.destroy');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])
        ->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->name('orders.show');

    // Checkout
    Route::post('/checkout', [CheckoutController::class, 'store'])
        ->name('checkout.store');
});

/*====================
    Admin Routes
====================*/

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])
        ->name('dashboard');

    // Products
    Route::resource('products', AdminProductController::class)
        ->except(['show']);

    // Categories
    Route::resource('categories', AdminCategoryController::class)
        ->except(['show']);

    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])
        ->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])
        ->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
        ->name('orders.status');

});