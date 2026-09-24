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

            <div class="customer-empty-icon" aria-hidden="true">
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
                                    <strong class="order-number">
                                        #{{ $order->id }}
                                    </strong>
                                </td>
                                <td>
                                    <span class="order-date">
                                        {{ $order->created_at->format('Y-m-d') }}
                                        <small>
                                            {{ $order->created_at->format('H:i') }}
                                        </small>
                                    </span>
                                </td>
                                <td>
                                    <strong class="order-total">
                                        {{ number_format($order->total, 2) }}
                                        <small>{{ $order->currency }}</small>
                                    </strong>
                                </td>
                                <td>
                                    <span class="customer-status customer-status-{{ $order->status }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('orders.show', ['order' => $order->id]) }}"
                                        class="store-btn store-btn-secondary order-view-button">
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