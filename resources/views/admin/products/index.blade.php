@extends('layouts.admin')

@section('title', 'Products')

@section('page-title', 'Products')

@section('content')

    <div class="page-header">

        <div>
            <span class="section-eyebrow">INVENTORY</span>
            <h1>Products</h1>
            <p>Manage your store products and inventory</p>
        </div>

        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <span aria-hidden="true">+</span>
            <span>Add Product</span>
        </a>

    </div>

    @if (session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif

    @if (session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

    @endif

    <div class="table-card product-filters-card">

        <form action="{{ route('admin.products.index') }}" method="get" class="product-filters-form">

            <div class="filter-field">

                <label for="search">Search product</label>
                <input type="search" name="search" id="search" value="{{ request('search') }}"
                    placeholder="Enter product name...">

            </div>

            <div class="filter-field">

                <label for="category_id">Category</label>
                <select name="category_id" id="category_id">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

            </div>

            <div class="filter-field">

                <label for="currency">Currency</label>
                <select name="currency" id="currency">
                    <option value="">All currencies</option>
                    @foreach ($currencies as $currency)
                        <option value="{{ $currency }}" @selected(request('currency') === $currency)>
                            {{ $currency }}
                        </option>
                    @endforeach
                </select>

            </div>

            <div class="filter-field">

                <label for="stock">Stock</label>
                <select name="stock" id="stock">
                    <option value="">All stock levels</option>
                    <option value="out" @selected(request('stock') === 'out')>
                        Out of Stock
                    </option>
                    <option value="low" @selected(request('stock') === 'low')>
                        Low Stock
                    </option>
                    <option value="in" @selected(request('stock') === 'in')>
                        In Stock
                    </option>
                </select>

            </div>

            <div class="product-filter-actions">

                <button type="submit" class="btn btn-primary">
                    Apply Filters
                </button>

                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    Reset
                </a>

            </div>

        </form>

    </div>

    <div class="table-card">

        <div class="product-results-header">

            <span>
                Showing {{ $products->firstItem() ?? 0 }}
                - {{ $products->lastItem() ?? 0 }}
                of {{ $products->total() }} products
            </span>

        </div>

        @if ($products->count())

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Rating</th>
                            <th>Actions</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($products as $product)

                            <tr>
                                <td>
                                    <span class="product-id">
                                        #{{ $product->id }}
                                    </span>
                                </td>
                                <td>
                                    <div class="product-cell">
                                        @if ($product->image)
                                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="product-image">
                                        @else
                                            <div class="product-placeholder" aria-hidden="true">
                                                📦
                                            </div>
                                        @endif
                                        <div class="product-info">
                                            <strong>{{ $product->name }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="category-badge">
                                        {{ $product->category?->name ?? 'Uncategorized' }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="price">
                                        {{ number_format($product->price, 2) }}
                                    </strong>
                                    <span class="currency">
                                        {{ $product->currency }}
                                    </span>
                                </td>
                                <td>
                                    @if ($product->stock === 0)
                                        <span class="status-badge status-stock-out">
                                            Out of Stock
                                        </span>
                                    @elseif ($product->stock <= 5)
                                        <span class="status-badge status-stock-low">
                                            {{ $product->stock }} left
                                        </span>
                                    @else
                                        <span class="status-badge status-stock-available">
                                            {{ $product->stock }} available
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="rating">
                                        ★ {{ number_format($product->rating, 1) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('admin.products.edit', ['product' => $product->id]) }}"
                                            class="btn btn-secondary">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.products.destroy', ['product' => $product->id]) }}"
                                            method="post"
                                            onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            @if ($products->hasPages())
                <div class="pagination-wrapper">
                    {{ $products->links() }}
                </div>
            @endif

        @else

            <div class="empty-state">

                <div class="empty-state-icon" aria-hidden="true">
                    📦
                </div>

                <h2>No Products Found</h2>

                <p>
                    No products match your search or selected filters.
                    Try changing your filters or resetting them.
                </p>

                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    Clear Filters
                </a>

            </div>

        @endif

    </div>

@endsection