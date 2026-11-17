<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

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
            $imagePath = $request->file('image')->store('products', 'public');

            if (!$imagePath) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'image' => 'The image could not be uploaded. Please try again.',
                    ]);
            }

            $validated['image'] = $imagePath;

        }

        Product::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
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

        $oldImage = $product->image;
        $newImagePath = null;

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('products', 'public');

            if (!$newImagePath) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'image' => 'The new image could not be uploaded. The current image was kept.',
                    ]);
            }

            $validated['image'] = $newImagePath;

        }

        $product->update($validated);

        // Delete the old image only after the product was updated successfully.
        if ($newImagePath && $oldImage && !str_starts_with($oldImage, 'uploads/')) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $imagePath = $product->image;

        $product->delete();

        // Keep compatibility with images uploaded using the old public/uploads method.
        if ($imagePath && !str_starts_with($imagePath, 'uploads/')) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Products deleted successfully.');
    }
}
