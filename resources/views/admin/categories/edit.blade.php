@extends('layouts.admin')

@section('title', 'Edit Category')

@section('page-title', 'Edit Category')

@section('content')

    <div class="page-header">

        <div>
            <span class="section-eyebrow">CATALOG</span>
            <h1>Edit Category</h1>
            <p>Update the name of this category</p>
        </div>

        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            Back to Categories
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

    <div class="form-page-grid category-form-grid">

        <div class="card product-form-card">

            <div class="card-header">

                <div>
                    <span class="section-eyebrow">CATEGORY DETAILS</span>
                    <h2>Edit Category</h2>
                    <p>Update the information for this category</p>
                </div>

            </div>

            <form action="{{ route('admin.categories.update', ['category' => $category->id]) }}" method="post"
                class="product-form">

                @csrf
                @method('PUT')

                <div class="form-group">

                    <label for="name">Category Name</label>

                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}"
                        placeholder="Enter category name" required maxlength="255" autofocus
                        class="@error('name') input-error @enderror">

                    <small class="field-help">
                        Use a clear and descriptive name for this category
                    </small>

                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror

                </div>

                <div class="form-actions">

                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Update Category
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection