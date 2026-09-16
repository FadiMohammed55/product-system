@extends('layouts.admin')

@section('title', 'Order #' . $order->id)

@section('page-title', 'Order #' . $order->id)

@section('content')

    <div class="page-header">

        <div>
            <span class="section-eyebrow">ORDER DETAILS</span>
            <h1>Order #{{ $order->id }}</h1>
            <p>Place on {{ $order->created_at->format('Y-m-d H:i') }}</p>
        </div>

        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            Back to Orders
        </a>

    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="order-info-card">

        <div>
            <span>Customer</span>
            <strong>{{ $order->user->name }}</strong>
        </div>

        <div>
            <span>Email</span>
            <strong>{{ $order->user->email }}</strong>
        </div>

        <div>
            <span>Total</span>
            <strong>{{ number_format($order->total, 2) }}</strong>
        </div>

        <div>
            <span>Status</span>
            <strong class="status-badge status-{{ $order->status }}">
                <span class="status-dot" aria-hidden="true"></span>
                {{ ucfirst($order->status) }}
            </strong>
        </div>

    </div>

    <section class="dashboard-section">

        <div class="section-header">

            <div>
                <span class="section-eyebrow">PURCHASE</span>
                <h3>Order Items</h3>
                <p>Products included in this order</p>
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
                                <td>
                                    <div class="product-cell">
                                        @if ($item->product->image)
                                            <img src="{{ asset($item->product->image) }}" alt="{{ $item->product->name }}"
                                                class="product-image">
                                        @else
                                            <div class="product-placeholder" aria-hidden="true">
                                                📦
                                            </div>
                                        @endif
                                        <div class="product-info">
                                            <strong>{{ $item->product->name }}</strong>
                                            <span>Product #{{ $item->product->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="category-badge">
                                        {{ $item->product->category->name }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="price">
                                        {{ number_format($item->price, 2) }}
                                    </strong>
                                </td>
                                <td>
                                    <span class="quantity-badge">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="price">
                                        {{ number_format($item->price * $item->quantity, 2) }}
                                    </strong>
                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </section>

    <section class="card order-status-card">

        <div class="card-header">

            <div>
                <span class="section-eyebrow">ORDER MANAGEMENT</span>
                <h2>Update Order Status</h2>
                <p>Change the current status of this order</p>
            </div>

        </div>

        <form action="{{ route('admin.orders.status', ['order' => $order->id]) }}" method="post" class="order-status-form">

            @csrf
            @method('PATCH')

            <div class="form-group">

                <label for="status">Status</label>

                <select name="status" id="status" class="form-control">
                    <option value="pending" @selected($order->status === 'pending')>
                        Pending
                    </option>
                    <option value="processing" @selected($order->status === 'processing')>
                        Processing
                    </option>
                    <option value="completed" @selected($order->status === 'completed')>
                        Completed
                    </option>
                    <option value="cancelled" @selected($order->status === 'cancelled')>
                        Cancelled
                    </option>
                </select>

            </div>

            <button type="submit" class="btn btn-primary">
                Update Status
            </button>

        </form>

    </section>

@endsection