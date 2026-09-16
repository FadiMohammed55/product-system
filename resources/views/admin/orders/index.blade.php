@extends('layouts.admin')

@section('title', 'Orders')

@section('page-title', 'Orders')

@section('content')

    <div class="page-header">

        <div>
            <span class="section-eyebrow">SALES</span>
            <h1>Orders</h1>
            <p>Manage and monitor customer orders</p>
        </div>

    </div>

    @if (session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif

    @if ($orders->isEmpty())

        <div class="table-card">

            <div class="empty-state">

                <div class="empty-state-icon" aria-hidden="true">
                    🛒
                </div>

                <h2>No Orders Found</h2>

                <p>There are currently no customer orders</p>

            </div>

        </div>

    @else

        <div class="table-card">

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($orders as $order)

                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', ['order' => $order->id]) }}" class="order-id">
                                        #{{ $order->id }}
                                    </a>
                                </td>
                                <td>
                                    <div class="customer-cell">
                                        <div class="customer-avatar">
                                            {{ strtoupper(substr($order->user->name, 0, 1)) }}
                                        </div>
                                        <div class="customer-info">
                                            <strong>{{ $order->user->name }}</strong>
                                            <span>{{ $order->user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong class="price">
                                        {{ number_format($order->total, 2) }}
                                    </strong>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $order->status }}">
                                        <span class="status-dot" aria-hidden="true"></span>
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="date">
                                        {{ $order->created_at->format('Y-m-d H:i') }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', ['order' => $order->id]) }}">
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