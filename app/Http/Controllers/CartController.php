<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class CartController extends Controller
{
    public function index(Request $request, CurrencyService $currencyService)
    {
        $cart = $request->session()->get('cart', []);

        $products = Product::whereIn('id', array_keys($cart))
            ->with('category')
            ->get();

        $foundProductIds = $products
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        $cartProductIds = array_map(
            'intval',
            array_keys($cart)
        );

        $missingProductIds = array_diff(
            $cartProductIds,
            $foundProductIds
        );

        if (!empty($missingProductIds)) {

            foreach ($missingProductIds as $productId) {
                unset($cart[$productId]);
            }

            $request->session()->put('cart', $cart);

            $request->session()->flash(
                'error',
                'Some products in your cart are no longer available.'
            );

        }

        $convertedPrices = [];
        $total = 0;

        try {

            foreach ($products as $product) {

                if (!isset($cart[$product->id])) {
                    continue;
                }

                $quantity = $cart[$product->id];

                $convertedPrice = $currencyService->convertToBase(
                    (float) $product->price,
                    $product->currency
                );

                $convertedPrices[$product->id] = $convertedPrice;

                $total += $convertedPrice * $quantity;
            }

        } catch (InvalidArgumentException $exception) {

            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'One or more products have an unsupported currency'
                );
        }

        $baseCurrency = $currencyService->baseCurrency();

        return view('cart.index', compact(
            'products',
            'cart',
            'convertedPrices',
            'total',
            'baseCurrency'
        ));
    }

    public function store(Request $request, Product $product, CurrencyService $currencyService)
    {
        $cart = $request->session()->get('cart', []);

        /**
         * Validate Product Currency
         */

        $currencyService->rate($product->currency);

        /**
         * Add Product To Cart
         */

        if (isset($cart[$product->id])) {
            $cart[$product->id]++;
        } else {
            $cart[$product->id] = 1;
        }

        $request->session()->put('cart', $cart);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product added to cart successfully');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $cart = $request->session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id] = $validated['quantity'];
        }

        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index');
    }

    public function destroy(Request $request, Product $product)
    {
        $cart = $request->session()->get('cart', []);

        unset($cart[$product->id]);

        $request->session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Product removed from cart');
    }
}