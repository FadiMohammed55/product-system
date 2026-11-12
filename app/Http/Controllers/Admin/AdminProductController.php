<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $currencies = array_keys(config('currency.rates'));

        $products = Product::with('category')
            // Search by product name
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));
                $query->where('name', 'like', "%{$search}%");
            })
            // Filter by category
            ->when(
                $request->filled('category_id') &&
                Category::whereKey($request->input('category_id'))->exists(),
                function ($query) use ($request) {
                    $query->where('category_id', $request->input('category_id'));
                }
            )
            // Filter by currency
            ->when(
                in_array($request->input('currency'), $currencies, true),
                function ($query) use ($request) {
                    $query->where('currency', $request->input('currency'));
                }
            )
            // Filter by stock status
            ->when(
                in_array($request->input('stock'), ['out', 'low', 'in'], true),
                function ($query) use ($request) {
                    if ($request->input('stock') === 'out') {
                        $query->where('stock', 0);
                    }
                    if ($request->input('stock') === 'low') {
                        $query->whereBetween('stock', [1, 5]);
                    }
                    if ($request->input('stock') === 'in') {
                        $query->where('stock', '>', 5);
                    }
                }
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'categories', 'currencies'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => [
                'required',
                'string',
                'size:3',
                Rule::in(array_keys(config('currency.rates'))),
            ],
            'rating' => 'required|numeric|min:0|max:5',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'stock' => 'required|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $imageName = time() . '_' . $image->getClientOriginalName();

            $image->move(public_path('uploads/products'), $imageName);

            $validated['image'] = 'uploads/products/' . $imageName;
        }

        Product::create($validated);

        return redirect('/admin/products');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => [
                'required',
                'string',
                'size:3',
                Rule::in(array_keys(config('currency.rates'))),
            ],
            'rating' => 'required|numeric|min:0|max:5',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'stock' => 'required|integer|min:0',
        ]);

        if ($request->hasFile('image')) {

            if ($product->image) {
                $oldImage = public_path($product->image);

                if (file_exists($oldImage)) {
                    unlink($oldImage);
                }
            }

            $image = $request->file('image');

            $imageName = time() . '_' . $image->getClientOriginalName();

            $image->move(public_path('uploads/products'), $imageName);

            $validated['image'] = 'uploads/products/' . $imageName;

        }

        $product->update($validated);

        return redirect('/admin/products');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            $imagePath = public_path($product->image);

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $product->delete();

        return redirect('/admin/products');
    }
}
