@extends('layouts.admin')

@section('title', 'Edit Product')

@section('page-title', 'Edit Product')

@section('content')

    <h1>Edit Product</h1>

    <form action="{{ route('admin.products.update', $product) }}" method="post" enctype="multipart/form-data">

        @csrf

        @method('PUT')

        <div>
            <label for="name">Product Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}">
        </div>

        <br>

        <div>
            <label for="price">Price</label>
            <input type="number" name="price" id="price" step="0.01" value="{{ old('price', $product->price) }}">
        </div>

        <br>

        <div>
            <label for="currency">Currency</label>
            <select name="currency" id="currency">
                <option value="USD" {{ old('currency', $product->currency) === 'USD' ? 'selected' : '' }}>USD</option>
                <option value="EUR" {{ old('currency', $product->currency) === 'EUR' ? 'selected' : '' }}>EUR</option>
                <option value="ILS" {{ old('currency', $product->currency) === 'ILS' ? 'selected' : '' }}>ILS</option>
            </select>
        </div>

        <br>

        <div>
            <label for="rating">Rating</label>
            <input type="number" name="rating" id="rating" min="0" max="5" step="0.1"
                value="{{ old('rating', $product->rating) }}">
        </div>

        <br>

        <div>
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <br>

        <div>
            <label>Current Image</label>
            @if ($product->image)
                <br>
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" width="150">
            @else
                <p>No Image</p>
            @endif
        </div>

        <br>

        <div>
            <label for="image">Replace Image</label>
            <input type="file" name="image" id="image" accept="image/*">
        </div>

        <br>

        <button type="submit">
            Update Product
        </button>

    </form>

    <br>

    <a href="{{ route('admin.products.index') }}">
        Back to Products
    </a>

@endsection