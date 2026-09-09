@extends('layouts.customer')

@section('title', 'Shopping Cart')

@section('page-title', 'Shopping Cart')

@section('content')

    <div class="page-header">

        <div>
            <h1>Shopping Cart</h1>
            <p>Review your selected products</p>
        </div>

        <a href="{{ route('products.index') }}" class="btn btn-primary">
            Continue Shopping
        </a>

    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($products->isEmpty())

        <div class="empty-state">

            <h2>Your cart is empty</h2>

            <p>You haven't added any products yet</p>

            <a href="{{ route('products.index') }}" class="btn btn-primary">
                Browse Products
            </a>

        </div>

    @else

        <div class="table-wrapper">

            <table class="data-table">

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
                                <div class="product-cell">
                                    @if ($product->image)
                                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="product-thumb">
                                    @endif
                                    <span>{{ $product->name }}</span>
                                </div>
                            </td>
                            <td>{{ $product->category->name }}</td>
                            <td>
                                {{ number_format($product->price, 2) }}
                                {{ $product->currency }}
                            </td>
                            <td>
                                <form action="{{ route('cart.update', $product) }}" method="post" class="quantity-form">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" id="quantity" value="{{ $quantity }}" min="1" max="99"
                                        class="form-control quantity-input">
                                    <button type="submit" class="btn btn-primary">
                                        Update
                                    </button>
                                </form>
                            </td>
                            <td>
                                {{ number_format($subtotal, 2) }}
                                {{ $product->currency }}
                            </td>
                            <td>
                                <form action="{{ route('cart.destroy', $product) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        Remove
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        <div class="cart-summary">

            <div>
                <span>Total</span>
                <strong>{{ number_format($total, 2) }}</strong>
            </div>

            <form action="{{ route('checkout.store') }}" method="post">
                @csrf
                <button type="submit" class="btn tbn-primary">
                    Checkout
                </button>
            </form>

        </div>

    @endif

@endsection