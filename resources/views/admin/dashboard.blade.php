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
                0
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
                0
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

                @forelse ($latesProducts as $product)

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

</div>

@endsection