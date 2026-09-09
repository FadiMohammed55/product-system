@extends('layouts.customer')

@section('title', 'Products')

@section('page-title', 'Products')

@section('content')

    <div class="page-header">

        <div>
            <h1>Products</h1>
            <p>Browse our available products</p>
        </div>

        <a href="{{ route('cart.index') }}" class="btn btn-primary">
            🛒 Cart
        </a>

    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($products->isEmpty())

        <div class="empty-state">
            <h2>No Products available</h2>
            <p>There are currently no products to display</p>
        </div>

    @else

        <div class="product-grid">

            @foreach ($products as $product)

                <div class="product-card">

                    <div class="product-image">

                        @if ($product->image)
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}">
                        @else
                            <div class="no-image">
                                No Image
                            </div>
                        @endif

                    </div>

                    <div class="product-info">

                        <span class="product-category">
                            {{ $product->category->name }}
                        </span>

                        <h3>{{ $product->name }}</h3>

                        <div class="product-rating">
                            ★ {{ number_format($product->rating, 1) }}/5
                        </div>

                        <div class="product-price">
                            {{ number_format($product->price, 2) }}
                            {{ $product->currency }}
                        </div>

                        <form action="{{ route('cart.store', $product) }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-block">
                                Add to Cart
                            </button>
                        </form>

                    </div>

                </div>

            @endforeach

        </div>

    @endif

@endsection