<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class AdminController extends Controller
{
    public function dashboard()
    {
        $productsCount = Product::count();

        $categoriesCount = Category::count();

        $latesProducts = Product::with('category')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'productsCount',
            'categoriesCount',
            'latesProducts'
        ));
    }
}