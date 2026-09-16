@extends('layouts.customer')

@section('title', 'My Orders')

@section('content')

    <div class="customer-page-header">

        <div>
            <h1>My Orders</h1>
            <p>View your orders history and track your purchases</p>
        </div>

        <a href="{{ route('products.index') }}" class="store-btn store-btn-primary">
            Continue Shopping
        </a>

    </div>

    @if (session('success'))
        <div class="store-alert store-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($orders->isEmpty())

        <div class="customer-empty-state">

            <div class="customer-empty-icon">
                📦
            </div>

            <h2>No Orders Yet</h2>

            <p>You have not placed any orders yet</p>

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
                            <th>Order</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($orders as $order)

                            <tr>
                                <td>
                                    <strong>
                                        #{{ $order->id }}
                                    </strong>
                                </td>
                                <td>
                                    <span>
                                        {{ $order->created_at->format('Y-m-d H:i') }}
                                    </span>
                                </td>
                                <td>
                                    <strong>
                                        {{ number_format($order->total, 2) }}
                                    </strong>
                                </td>
                                <td>
                                    <span class="customer-status customer-status-{{ $order->status }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('orders.show', ['order' => $order->id]) }}"
                                        class="store-btn store-btn-secondary">
                                        View Order
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