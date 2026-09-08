<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;

/*====================
    Public Routes
====================*/

// Home
Route::get('/', function () {
    return redirect('/login');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Customer Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

/*====================
    Admin Routes
====================*/

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Products
    Route::resource('products', AdminProductController::class)->except(['show']);

    // Categories
    Route::resource('categories', AdminCategoryController::class)->except(['show']);

});