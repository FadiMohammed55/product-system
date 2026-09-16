@extends('layouts.admin')

@section('title', 'Edit Product')

@section('page-title', 'Edit Product')

@section('content')

    <div class="page-header">

        <div>
            <span class="section-eyebrow">INVENTORY</span>
            <h1>Edit Product</h1>
            <p>Update product information and image</p>
        </div>

        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            Back to Products
        </a>

    </div>

    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>Please check the form</strong>

            <ul class="form-error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif

    <div class="form-page-grid">

        <div class="card product-form-card">

            <div class="card-header">

                <div>
                    <span class="section-eyebrow">PRODUCT DETAILS</span>
                    <h2>Edit Information</h2>
                    <p>Update the information for this product</p>
                </div>

            </div>

            <form action="{{ route('admin.products.update', ['product' => $product->id]) }}" method="post"
                enctype="multipart/form-data" class="product-form">

                @csrf
                @method('PUT')

                <div class="form-group">

                    <label for="name">Product Name</label>

                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                        placeholder="Enter product name" required class="@error('name') input-error @enderror">

                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror

                </div>

                <div class="form-group">

                    <label for="category_id">Category</label>

                    <select name="category_id" id="category_id" required
                        class="@error('category_id') input-error @enderror">

                        @foreach ($categories as $category)

                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id))>
                                {{ $category->name }}
                            </option>

                        @endforeach

                    </select>

                    @error('category_id')
                        <p class="field-error">{{ $message }}</p>
                    @enderror

                </div>

                <div class="form-row">

                    <div class="form-group">

                        <label for="price">Price</label>

                        <input type="number" name="price" id="price" step="0.01" min="0"
                            value="{{ old('price', $product->price) }}" placeholder="0.00" required
                            class="@error('price') input-error @enderror">

                        @error('price')
                            <p class="field-error">{{ $message }}</p>
                        @enderror

                    </div>

                    <div class="form-group">

                        <label for="currency">Currency</label>

                        <select name="currency" id="currency" required class="@error('currency') input-error @enderror">

                            <option value="USD" @selected(old('currency', $product->currency) === 'USD')> USD </option>
                            <option value="EUR" @selected(old('currency', $product->currency) === 'EUR')> EUR </option>
                            <option value="ILS" @selected(old('currency', $product->currency) === 'ILS')> ILS </option>

                        </select>

                        @error('currency')
                            <p class="field-error">{{ $message }}</p>
                        @enderror

                    </div>

                </div>

                <div class="form-group">

                    <label for="rating">Rating</label>

                    <input type="number" name="rating" id="rating" min="0" max="5" step="0.1"
                        value="{{ old('rating', $product->rating) }}" placeholder="0.0"
                        class="@error('rating') input-error @enderror">

                    <small class="field-help">
                        Rating must be between 0 and 5
                    </small>

                    @error('rating')
                        <p class="field-error">{{ $message }}</p>
                    @enderror

                </div>

                <div class="form-group">

                    <label>Current Image</label>

                    <div class="current-image-box">

                        @if ($product->image)

                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="current-product-image">

                            <div class="current-image-info">
                                <strong>Current product image</strong>
                                <span>Upload a new image below to replace it</span>
                            </div>

                        @else

                            <div class="product-placeholder current-image-placeholder">
                                📦
                            </div>

                            <div class="current-image-info">
                                <strong>No image uploaded</strong>
                                <span>You can upload one below</span>
                            </div>

                        @endif

                    </div>

                </div>

                <div class="form-group">

                    <label for="image">Replace Image</label>

                    <div class="file-input-wrapper">

                        <input type="file" name="image" id="image" accept="image/*"
                            class="@error('image') input-error @enderror">

                    </div>

                    <small class="field-help">
                        Leave empty to keep the current image
                    </small>

                    @error('image')
                        <p class="field-error">{{ $message }}</p>
                    @enderror

                </div>

                <div class="form-actions">

                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Update Product
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection