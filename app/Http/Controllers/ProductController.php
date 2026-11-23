<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Get available currencies from the database.
        $currencies = Product::query()
            ->select('currency')
            ->distinct()
            ->orderBy('currency')
            ->pluck('currency');

        // Validate search and filter inputs.
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'currency' => ['nullable', 'string', Rule::in($currencies->all())],
            'sort' => ['nullable', Rule::in(['price_asc', 'price_desc'])],
        ]);

        // Start building the products query.
        $query = Product::with('category');

        // Search by product name.
        if (!empty($validated['search'])) {
            $query->where('name', 'like', '%' . $validated['search'] . '%');
        }

        // Filter by category.
        if (!empty($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
        }

        // Filter by currency.
        if (!empty($validated['currency'])) {
            $query->where('currency', $validated['currency']);
        }

        // Sort products by price.
        if (($validated['sort'] ?? null) === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif (($validated['sort'] ?? null) === 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        // Paginate products.
        $products = $query->paginate(12)->withQueryString();

        // Get categories for the filter dropdown.
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact(
            'products',
            'categories',
            'currencies'
        ));
    }
}
