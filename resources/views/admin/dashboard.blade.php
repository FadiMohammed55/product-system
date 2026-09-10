@extends('layouts.admin')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')

    <div class="dashboard-header">

        <div class="dashboard-header-content">

            <span class="dashboard-eyebrow">ADMIN OVERVIEW</span>

            <h2>
                Welcome back, {{ Auth::user()->name }}
                <span class="welcome-icon">👋</span>
            </h2>

            <p>Here's what's happening with your store today</p>

        </div>

        <div class="dashboard-header-action">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <span>+</span>
                Add Product
            </a>
        </div>

    </div>

    <div class="stats-grid">

        <div class="stat-card">

            <div class="stat-card-top">

                <div class="stat-icon">
                    📦
                </div>

                <span class="stat-trend">
                    Store
                </span>

            </div>

            <div class="stat-content">

                <p class="stat-label">Products</p>

                <h3> {{ $productsCount }} </h3>

                <a href="{{ route('admin.products.index') }}" class="stat-link">
                    View products →
                </a>

            </div>

        </div>

        <div class="stat-card">

            <div class="stat-card-top">

                <div class="stat-icon">
                    🗂️
                </div>

                <span class="stat-trend">
                    Store
                </span>

            </div>

            <div class="stat-content">

                <p class="stat-label"> Categories </p>

                <h3> {{ $categoriesCount }} </h3>

                <a href="{{ route('admin.categories.index') }}" class="stat-link">
                    View categories →
                </a>

            </div>

        </div>

        <div class="stat-card">

            <div class="stat-card-top">

                <div class="stat-icon">
                    👤
                </div>

                <span class="stat-trend">
                    Users
                </span>

            </div>

            <div class="stat-content">

                <p class="stat-label">Customers</p>

                <h3> {{ $customersCount }} </h3>

                <span class="stat-link stat-link-disabled">
                    Registered customers
                </span>

            </div>

        </div>

        <div class="stat-card">

            <div class="stat-card-top">

                <div class="stat-icon">
                    🛒
                </div>

                <span class="stat-trend">
                    Sales
                </span>

            </div>

            <div class="stat-content">

                <p class="stat-label">Orders</p>

                <h3> {{ $ordersCount }} </h3>

                <a href="{{ route('admin.orders.index') }}" class="stat-link">
                    View orders →
                </a>

            </div>

        </div>

    </div>

    <div class="dashboard-section">

        <div class="section-header">

            <div>
                <span class="section-eyebrow">INVENTORY</span>
                <h3>Recent Products</h3>
                <p>Your latest added products</p>
            </div>

            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                View All
            </a>

        </div>


        <div class="card table-card">

            @if ($latestProducts->isEmpty())

                <div class="empty-state">

                    <div class="empty-state-icon">
                        📦
                    </div>

                    <h3>No Products Found</h3>

                    <p>Start by adding your first product</p>

                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                        Add Product
                    </a>

                </div>

            @else

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

                            @foreach ($latestProducts as $product)

                                <tr>

                                    <td>
                                        <div class="product-cell">

                                            @if ($product->image)
                                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}"
                                                    class="product-image">
                                            @else
                                                <div class="product-placeholder">
                                                    📦
                                                </div>
                                            @endif

                                            <div class="product-info">
                                                <strong>{{ $product->name }}</strong>
                                                <span>Product #{{ $product->id }}</span>
                                            </div>

                                        </div>
                                    </td>
                                    <td>
                                        <span class="category-badge">{{ $product->category->name }}</span>
                                    </td>
                                    <td>
                                        <strong class="price">{{ number_format($product->price, 2) }}</strong>
                                        <span class="currency">{{ $product->currency }}</span>
                                    </td>
                                    <td>
                                        <span class="rating">
                                            <span>★</span>
                                            {{ number_format($product->rating, 1) }}
                                        </span>
                                    </td>
                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>

    <div class="dashboard-section">

        <div class="section-header">

            <div>
                <span class="section-eyebrow">SALES</span>
                <h3>Recent Orders</h3>
                <p>Latest customer orders</p>
            </div>

            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                View All
            </a>

        </div>

        <div class="card table-card">

            @if ($latestOrders->isEmpty())

                <div class="empty-state">

                    <div class="empty-state-icon">
                        🛒
                    </div>

                    <h3>No Orders Yet</h3>
                    <p>Customer orders will appear here</p>

                </div>

            @else

                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($latestOrders as $order)

                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="order-id">
                                            #{{ $order->id }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="customer-cell">

                                            <div class="customer-avatar">
                                                {{ strtoupper(substr($order->user->name, 0, 1)) }}
                                            </div>

                                            <span>{{ $order->user->name }}</span>

                                        </div>
                                    </td>
                                    <td>
                                        <strong class="price">{{ number_format($order->total, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ $order->status }}">
                                            <span class="status-dot"></span>
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="date">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                                    </td>
                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>

@endsection