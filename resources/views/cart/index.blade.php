@extends('layouts.customer')

@section('title', 'Shopping Cart')

@section('content')

    <div class="customer-page-header">

        <div>
            <h1>Shopping Cart</h1>
            <p>Review your selected products before checkout</p>
        </div>

        <a href="{{ route('products.index') }}" class="store-btn store-btn-secondary">
            ← Continue Shopping
        </a>

    </div>

    @if (session('success'))
        <div class="store-alert store-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($products->isEmpty())

        <div class="customer-empty-state">

            <div class="customer-empty-icon" aria-hidden="true">
                🛒
            </div>

            <h2>Your Cart is Empty</h2>

            <p>You haven't added any products yet</p>

            <a href="{{ route('products.index') }}" class="store-btn store-btn-primary">
                Browse Products
            </a>

        </div>

    @else

        <div class="customer-table-card">

            <div class="customer-table-wrapper">

                <table class="customer-table">

                    <thead>

                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($products as $product)

                            @php
                                $quantity = $cart[$product->id];
                                $subtotal = $product->price * $quantity;
                            @endphp

                            <tr>
                                <td>
                                    <div class="cart-product">
                                        @if ($product->image)
                                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}"
                                                class="cart-product-image">
                                        @else
                                            <div class="customer-empty-icon">
                                                📦
                                            </div>
                                        @endif
                                        <div class="cart-product-info">
                                            <strong>{{ $product->name }}</strong>
                                            <span>Product #{{ $product->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="product-category">
                                        {{ $product->category->name }}
                                    </span>
                                </td>
                                <td>
                                    <strong>
                                        {{ number_format($product->price, 2) }}
                                    </strong>
                                    <span class="product-currency">
                                        {{ $product->currency }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('cart.update', ['product' => $product->id]) }}" method="post"
                                        class="quantity-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $quantity }}" min="1" max="99"
                                            class="store-input quantity-input" aria-label="Quantity for {{ $product->name }}">
                                        <button type="submit" class="store-btn store-btn-secondary">
                                            Update
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <strong>
                                        {{ number_format($subtotal, 2) }}
                                    </strong>
                                    <span class="product-currency">
                                        {{ $product->currency }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('cart.destroy', ['product' => $product->id]) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="store-btn store-btn-danger">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

        <div class="cart-summary">

            <div class="cart-total">
                <span>Cart Total</span>
                <strong>{{ number_format($total, 2) }}</strong>
            </div>

            <form action="{{ route('checkout.store') }}" method="post">

                @csrf

                <button type="submit" class="store-btn store-btn-primary">
                    Proceed to Checkout →
                </button>
                
            </form>

        </div>

    @endif

@endsection