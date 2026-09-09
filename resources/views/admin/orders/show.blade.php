@extends('layouts.admin')

@section('title', 'Order #' . $order->id)

@section('page-title', 'Order #' . $order->id)

@section('content')

    <div class="page-header">

        <div>
            <h1>Order #{{ $order->id }}</h1>
            <p>{{ $order->created_at->format('Y-m-d H:i') }}</p>
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
                {{ ucfirst($order->status) }}
            </strong>
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
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->product->category->name }}</td>
                            <td>{{ number_format($item->price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

    <div class="card">

        <div class="card-header">

            <div>
                <h2>Update Order Status</h2>
                <p>Change the current status of this order</p>
            </div>

        </div>

        <form action="{{ route('admin.orders.status', $order) }}" method="post">

            @csrf
            @method('PATCH')

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="pending" @selected($order->status === 'pending')>Pending</option>
                    <option value="processing" @selected($order->status === 'processing')>Processing</option>
                    <option value="completed" @selected($order->status === 'completed')>Completed</option>
                    <option value="cancelled" @selected($order->status === 'cancelled')>Cancelled</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                Update Status
            </button>

        </form>

    </div>

@endsection