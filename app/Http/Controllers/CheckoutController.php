<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CheckoutController extends Controller
{
    public function store(
        Request $request,
        CurrencyService $currencyService
    ) {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        /*
        |--------------------------------------------------------------------------
        | Create Order
        |--------------------------------------------------------------------------
        */
        try {

            $order = DB::transaction(function () use ($request, $cart, $currencyService) {


                $products = Product::whereIn('id', array_keys($cart))
                    ->lockForUpdate()
                    ->get();

                /*
                |--------------------------------------------------------------------------
                | Check for products that no longer exist
                |--------------------------------------------------------------------------
                */
                $cartProductIds = array_map(
                    'intval',
                    array_keys($cart)
                );

                $foundProductIds = $products
                    ->pluck('id')
                    ->map(fn($id) => (int) $id)
                    ->all();

                $missingProductIds = array_diff(
                    $cartProductIds,
                    $foundProductIds
                );

                if (!empty($missingProductIds)) {
                    throw new \RuntimeException(
                        'Some products in your cart are no longer available.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Validate currencies
                |--------------------------------------------------------------------------
                */
                foreach ($products as $product) {
                    $currencyService->rate($product->currency);
                }

                /*
                |--------------------------------------------------------------------------
                | Check Stock
                |--------------------------------------------------------------------------
                */
                foreach ($products as $product) {

                    $quantity = $cart[$product->id];

                    if ($quantity > $product->stock) {
                        throw new \RuntimeException(
                            "Insufficient stock for product: {$product->name}"
                        );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Calculate Total
                |--------------------------------------------------------------------------
                */
                $total = 0;

                foreach ($products as $product) {

                    $quantity = $cart[$product->id];

                    $convertedPrice = $currencyService->convertToBase(
                        (float) $product->price,
                        $product->currency
                    );

                    $total += $convertedPrice * $quantity;
                }

                /*
                |--------------------------------------------------------------------------
                | Create Order
                |--------------------------------------------------------------------------
                */
                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'total' => round($total, 2),
                    'currency' => $currencyService->baseCurrency(),
                    'status' => 'pending',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Create Order Items + Decrease Stock
                |--------------------------------------------------------------------------
                */
                foreach ($products as $product) {

                    $quantity = $cart[$product->id];

                    $convertedPrice = $currencyService->convertToBase(
                        (float) $product->price,
                        $product->currency
                    );

                    $order->items()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $quantity,
                        'price' => $product->price,
                        'currency' => $product->currency,
                        'converted_price' => $convertedPrice
                    ]);

                    $product->decrement('stock', $quantity);
                }

                return $order;
            });

        } catch (InvalidArgumentException $exception) {

            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'One or more products have an unsupported currency.'
                );

        } catch (\RuntimeException $exception) {

            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    $exception->getMessage()
                );

        }

        /*
        |--------------------------------------------------------------------------
        | Clear Cart
        |--------------------------------------------------------------------------
        */
        $request->session()->forget('cart');

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order placed successfully');

    }
}
