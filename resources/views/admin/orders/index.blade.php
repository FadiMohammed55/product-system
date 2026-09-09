@extends('layouts.admin')

@section('title', 'Orders')

@section('page-title', 'Orders')

@section('content')

    <div class="page-header">

        <div>
            <h1>Orders</h1>
            <p>Manage customer orders</p>
        </div>

    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($orders->isEmpty())

        <div class="empty-state">
            <h2>No Orders Found</h2>
            <p>There are currently no customer orders</p>
        </div>

    @else

        <div class="table-card">

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>
                            <th>Order #</th>
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
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td>{{ number_format($order->total, 2) }}</td>
                                <td>
                                    <span class="status-badge status-{{ $order->status }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn tbn-secondary">
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