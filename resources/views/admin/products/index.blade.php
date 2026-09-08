@extends('layouts.admin')

@section('title', 'Products')

@section('page-title', 'Products')

@section('content')

    <h2>Products Management</h2>

    <p>Here you can manage all products</p>

    <a href="{{ route('admin.products.create') }}">
        Add Product
    </a>

    <hr>

    @if ($products->count())

        <table>

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Currency</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>

            </thead>

            <tbody>

                @foreach ($products as $product)

                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
                            @if ($product->image)
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" width="60">
                            @else
                                No Image
                            @endif
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td>{{ $product->price }}</td>
                        <td>{{ $product->currency }}</td>
                        <td>{{ $product->rating }}</td>
                        <td>
                            <a href="{{ route('admin.products.edit', $product) }}">
                                Edit
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="post" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>

                @endforeach

            </tbody>

        </table>

    @else

        <p>No Products Found</p>

    @endif

@endsection