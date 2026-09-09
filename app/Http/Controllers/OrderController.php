<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function show(Request $request, $order)
    {
        $order = $request->user()
            ->orders()
            ->with('items.product.category')
            ->findOrFail($order);

        return view('orders.show', compact('order'));
    }
}
