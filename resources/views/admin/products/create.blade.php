@extends('layouts.admin')

@section('title', 'Add Product')

@section('page-title', "Add Product")

@section('content')

    <h1>Add Product</h1>

    <form action="{{ route('admin.products.store') }}" method="post" enctype="multipart/form-data">

        @csrf

        <div>
            <label for="name">Product Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
        </div>

        <br>

        <div>
            <label for="price">Price</label>
            <input type="number" name="price" id="price" step="0.01" value="{{ old('price') }}" required>
        </div>

        <br>

        <div>
            <label for="currency">Currency</label>
            <select name="currency" id="currency">
                <option value="">Select Currency</option>
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
                <option value="ILS">ILS</option>
            </select>
        </div>

        <br>

        <div>
            <label for="rating">Rating</label>
            <input type="number" name="rating" id="rating" min="0" max="5" step="0.1" value="{{ old('rating', 0) }}">
        </div>

        <br>

        <div>
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id">
                <option value="">Select Category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <br>

        <div>
            <label for="image">Product Image</label>
            <input type="file" name="image" id="image" accept="image/*">
        </div>

        <br>

        <button type="submit">
            Add Product
        </button>

    </form>

    <br>

    <a href="{{ route('admin.products.index') }}">
        Back to Products
    </a>

@endsection