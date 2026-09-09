@extends('layouts.admin')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')

    <div class="dashboard-header">

        <div>
            <h2>Welcome back, {{ Auth::user()->name }} 👋</h2>

            <p>
                Here's what's happening with your store today
            </p>
        </div>

    </div>

    <div class="stats-grid">

        <div class="stat-card">

            <div class="stat-icon">
                📦
            </div>

            <div>
                <p class="stat-label">
                    Products
                </p>

                <h3>
                    {{ $productsCount }}
                </h3>
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-icon">
                🗂️
            </div>

            <div>
                <p class="stat-label">
                    Categories
                </p>

                <h3>
                    {{ $categoriesCount }}
                </h3>
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-icon">
                👤
            </div>

            <div>
                <p class="stat-label">
                    Customers
                </p>

                <h3>
                    {{ $customersCount }}
                </h3>
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-icon">
                🛒
            </div>

            <div>
                <p class="stat-label">
                    Orders
                </p>

                <h3>
                    {{ $ordersCount }}
                </h3>
            </div>

        </div>

    </div>

    <div class="dashboard-section">

        <div class="section-header">

            <div>
                <h3>Recent Products</h3>
                <p>Your latest added products</p>
            </div>

            <a href="/admin/products" class="btn btn-primary">
                View All
            </a>

        </div>

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Rating</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse ($latestProducts as $product)

                        <tr>
                            <td>
                                <div class="product-cell">

                                    @if ($product->image)

                                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="product-image">

                                    @else

                                        <div class="product-placeholder">
                                            📦
                                        </div>

                                    @endif

                                    <span>{{ $product->name }}</span>

                                </div>
                            </td>
                            <td>{{ $product->category->name }}</td>
                            <td>{{ $product->price }}{{ $product->currency }}</td>
                            <td>⭐ {{ $product->rating }}</td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    No Products Found
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="card">

            <div class="card-header">

                <div>
                    <h2>Recent Orders</h2>
                    <p>Latest customer orders</p>
                </div>

                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                    View All
                </a>

            </div>

            @if ($latestOrders->isEmpty())

                <div class="empty-state">
                    <h3>No Orders Yet</h3>
                    <p>Customer orders will apper here</p>
                </div>

            @else

                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($latestOrders as $order)

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
                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>

@endsection