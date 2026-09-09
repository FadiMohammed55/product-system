@extends('layouts.customer')

@section('title', 'My Orders')

@section('page-title', 'My Orders')

@section('content')

    <div class="page-header">

        <div>
            <h1>My Orders</h1>
            <p>View your order history</p>
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

    @if ($orders->isEmpty())

        <div class="empty-state">

            <h2>No Orders Yet</h2>

            <p>You have not placed any orders yet</p>

            <a href="{{ route('products.index') }}" class="btn btn-primary">
                Browse Products
            </a>

        </div>

    @else

        <div class="table-card">

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($orders as $order)

                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ number_format($order->total, 2) }}</td>
                                <td>
                                    <span class="status-badge status-{{ $order->status }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
                                        View
                                    </a>
                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    @endif

@endsection