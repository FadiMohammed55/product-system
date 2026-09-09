<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $productsCount = Product::count();

        $categoriesCount = Category::count();

        $customersCount = User::where('role', 'customer')->count();

        $ordersCount = Order::count();

        $latestProducts = Product::with('category')
            ->latest()
            ->take(5)
            ->get();

        $latestOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'productsCount',
            'categoriesCount',
            'customersCount',
            'ordersCount',
            'latestProducts',
            'latestOrders',
        ));
    }
}