@extends('layouts.admin')

@section('title', 'Products')

@section('page-title', 'Products')

@section('content')

    <div class="page-header">

        <div>
            <span class="section-eyebrow">INVENTORY</span>
            <h1>Products</h1>
            <p>Manage your store products and inventory</p>
        </div>

        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <span aria-hidden="true">+</span>
            <span>Add Product</span>
        </a>

    </div>

    @if (session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif

    @if ($products->count())

        <div class="table-card">

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Rating</th>
                            <th>Actions</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($products as $product)

                            <tr>
                                <td>
                                    <span class="product-id">
                                        #{{ $product->id }}
                                    </span>
                                </td>
                                <td>
                                    <div class="product-cell">
                                        @if ($product->image)
                                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="product-image">
                                        @else
                                            <div class="product-placeholder" aria-hidden="true">
                                                📦
                                            </div>
                                        @endif
                                        <div class="product-info">
                                            <strong>{{ $product->name }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="category-badge">
                                        {{ $product->category->name }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="price">
                                        {{ number_format($product->price, 2) }}
                                    </strong>
                                    <span class="currency">
                                        {{ $product->currency }}
                                    </span>
                                </td>
                                <td>
                                    @if ($product->stock === 0)
                                        <span class="status-badge status-stock-out">
                                            Out of Stock
                                        </span>
                                    @elseif ($product->stock <= 5)
                                        <span class="status-badge status-stock-low">
                                            {{ $product->stock }} left
                                        </span>
                                    @else
                                        <span class="status-badge status-stock-available">
                                            {{ $product->stock }} available
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="rating">
                                        ★ {{ number_format($product->rating, 1) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('admin.products.edit', ['product' => $product->id]) }}"
                                            class="btn btn-secondary">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.products.destroy', ['product' => $product->id]) }}"
                                            method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    @else

        <div class="table-card">

            <div class="empty-state">

                <div class="empty-state-icon" aria-hidden="true">
                    📦
                </div>

                <h2>No Products Found</h2>

                <p>You haven't added any products yet</p>

                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    Add Your First Product
                </a>

            </div>

        </div>

    @endif

@endsection