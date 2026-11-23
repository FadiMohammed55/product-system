@extends('layouts.customer')

@section('title', 'Products')

@section('content')

    <div class="customer-page-header">

        <div>
            <h1>Products</h1>
            <p>Browse our available products</p>
        </div>

        <a href="{{ route('cart.index') }}" class="store-btn store-btn-primary">
            <span aria-hidden="true">🛒</span>
            <span>Cart</span>
        </a>

    </div>

    @if (session('success'))
        <div class="store-alert store-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="store-alert store-alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- Search and Filters --}}
    <form action="{{ route('products.index') }}" method="get" class="product-filters">

        <div class="filter-group">
            <label for="search">Search Products</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}"
                placeholder="Search by product name...">
        </div>

        <div class="filter-group">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label for="currency">Currency</label>
            <select name="currency" id="currency">
                <option value="">All Currencies</option>
                @foreach ($currencies as $currency)
                    <option value="{{ $currency }}" @selected(request('currency') == $currency)>
                        {{ $currency }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label for="sort">Sort By Price</label>
            <select name="sort" id="sort">
                <option value="">Default</option>
                <option value="price_asc" @selected(request('sort') === 'price_asc')>
                    Price: Low to High
                </option>
                <option value="price_desc" @selected(request('sort') === 'price_desc')>
                    Price: High to Low
                </option>
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="store-btn store-btn-primary">
                Apply Filter
            </button>
            <a href="{{ route('products.index') }}" class="store-btn store-btn-secondary">
                Clear Filters
            </a>
        </div>

    </form>

    {{-- Products --}}
    @if ($products->isEmpty())

        <div class="customer-empty-state">

            <div class="customer-empty-icon" aria-hidden="true">
                📦
            </div>

            <h2>No Products Found</h2>

            <p>Try changing your search or filter options</p>

        </div>

    @else

        <div class="product-grid">

            @foreach ($products as $product)

                <article class="customer-product-card">

                    <div class="customer-product-image">

                        @if ($product->image)
                            <img src="{{ str_starts_with($product->image, 'uploads/') ? asset($product->image) : asset('storage/' . $product->image) }}"
                                alt="{{ $product->name }}">
                        @else
                            <div class="customer-no-image">
                                No Image
                            </div>
                        @endif

                    </div>

                    <div class="customer-product-info">

                        <span class="product-category">
                            {{ $product->category->name }}
                        </span>

                        <h3>{{ $product->name }}</h3>

                        <div class="product-rating">
                            ★ {{ number_format($product->rating, 1) }}/5
                        </div>

                        <div class="product-price">
                            {{ number_format($product->price, 2) }}
                            <span class="product-currency">
                                {{ $product->currency }}
                            </span>
                        </div>

                        <div class="product-stock">

                            @if ($product->stock === 0)
                                <span class="customer-stock customer-stock-out">
                                    Out of Stock
                                </span>
                            @elseif ($product->stock <= 5)
                                <span class="customer-stock customer-stock-low">
                                    Only {{ $product->stock }} left
                                </span>
                            @else
                                <span class="customer-stock customer-stock-available">
                                    {{ $product->stock }} available
                                </span>
                            @endif

                        </div>

                        @if ($product->stock > 0)

                            <form action="{{ route('cart.store', ['product' => $product->id]) }}" method="post">

                                @csrf

                                <button type="submit" class="store-btn store-btn-primary store-btn-block">
                                    Add to Cart
                                </button>

                            </form>

                        @else

                            <button type="button" class="store-btn store-btn-secondary store-btn-block" disabled>
                                Out of Stock
                            </button>

                        @endif

                    </div>

                </article>

            @endforeach

        </div>

        {{-- Pagination --}}
        <div class="product-pagination">
            {{ $products->links() }}
        </div>

    @endif

@endsection