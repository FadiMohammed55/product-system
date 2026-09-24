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

    @if ($products->isEmpty())

        <div class="customer-empty-state">

            <div class="customer-empty-icon" aria-hidden="true">
                📦
            </div>

            <h2>No Products Available</h2>

            <p>There are currently no products to display</p>

        </div>

    @else

        <div class="product-grid">

            @foreach ($products as $product)

                <article class="customer-product-card">

                    <div class="customer-product-image">

                        @if ($product->image)
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}">
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

    @endif

@endsection