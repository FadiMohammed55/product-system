@extends('layouts.customer')

@section('title', 'Order #' . $order->id)

@section('page-title', 'Order #' . $order->id)

@section('content')

    <div class="page-header">

        <div>
            <h1>Order #{{ $order->id }}</h1>
            <p>Placed on {{ $order->created_at->format('Y-m-d H:i') }}</p>
        </div>

        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
            Back to Orders
        </a>

    </div>

    <div class="order-info-card">

        <div>
            <span>Order Status</span>
            <strong class="status-badge status-{{ $order->status }}">
                {{ ucfirst($order->status) }}
            </strong>
        </div>

        <div>
            <span>Order Total</span>
            <strong>{{ number_format($order->total, 2) }}</strong>
        </div>

    </div>

    <div class="table-card">

        <div class="table-wrapper">

            <table>

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
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->product->category->name }}</td>
                            <td>{{ number_format($item->price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

@endsection