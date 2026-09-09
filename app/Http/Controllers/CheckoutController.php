<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        $products = Product::whereIn('id', array_keys($cart))
            ->get();

        DB::transaction(function () use ($request, $cart, $products, &$order) {


            $total = 0;

            foreach ($products as $product) {
                $quantity = $cart[$product->id];

                $total += $product->price * $quantity;
            }

            $order = Order::create([
                'user_id' => $request->user()->id,
                'total' => $total,
                'status' => 'pending',
            ]);

            foreach ($products as $product) {

                $quantity = $cart[$product->id];

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);

            }

        });

        $request->session()->forget('cart');

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order placed successfully');
    }
    
}
