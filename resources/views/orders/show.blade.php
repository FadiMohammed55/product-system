@extends('layouts.customer')

@section('title', 'Order #' . $order->id)

@section('content')

    <div class="customer-page-header">

        <div>
            <h1>Order #{{ $order->id }}</h1>
            <p>
                Placed on {{ $order->created_at->format('Y-m-d') }}
                at {{ $order->created_at->format('H:i') }}
            </p>
        </div>

        <a href="{{ route('orders.index') }}" class="store-btn store-btn-secondary">
            ← Back to Orders
        </a>

    </div>

    <div class="customer-order-info">

        <div class="customer-order-stat">
            <span>Order Status</span>

            <strong>
                <span class="customer-status customer-status-{{ $order->status }}">
                    {{ ucfirst($order->status) }}
                </span>
            </strong>
        </div>

        <div class="customer-order-stat">
            <span>Order Total</span>

            <strong class="order-detail-total">
                {{ number_format($order->total, 2) }}
                <span>{{ $order->currency }}</span>
            </strong>
        </div>

    </div>

    <div class="customer-page-section">

        <div class="customer-section-heading">

            <div>
                <span>PURCHASE</span>
                <h2>Order Items</h2>
                <p>Products included in this order</p>
            </div>

        </div>

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
                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($order->items as $item)

                            <tr>
                                <td>
                                    <div class="cart-product">
                                        @if ($item->product)
                                            @if ($item->product->image)
                                                <img src="{{ asset($item->product->image) }}" alt="{{ $item->product->name }}"
                                                    class="cart-product-image">
                                            @else
                                                <div class="cart-product-placeholder" aria-hidden="true">
                                                    📦
                                                </div>
                                            @endif
                                            <div class="cart-product-info">
                                                <strong class="order-item-name">
                                                    {{ $item->product->name }}
                                                </strong>
                                                <span>
                                                    Product #{{ $item->product->id }}
                                                </span>
                                            </div>
                                        @else
                                            <div class="cart-product-placeholder" aria-hidden="true">
                                                📦
                                            </div>
                                            <div class="cart-product-info">
                                                <strong class="order-item-name">
                                                    {{ $item->product_name }}
                                                </strong>
                                                <span>
                                                    Product no longer available
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if ($item->product && $item->product->category)
                                        <span class="product-category">
                                            {{ $item->product->category->name }}
                                        </span>
                                    @else
                                        <span class="product-category">
                                            Not available
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <strong>
                                        {{ number_format($item->price, 2) }}
                                    </strong>
                                    <span class="product-currency">
                                        {{ $item->currency }}
                                    </span>
                                    <br>
                                    <small>
                                        ≈ {{ number_format($item->converted_price, 2) }}
                                        {{ $order->currency }}
                                    </small>
                                </td>
                                <td>
                                    <span class="order-item-quantity">
                                        ×{{ $item->quantity }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="order-item-subtotal">
                                        {{ number_format($item->converted_price * $item->quantity, 2) }}
                                        <span>{{ $order->currency }}</span>
                                    </strong>
                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection