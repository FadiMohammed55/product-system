@extends('layouts.admin')

@section('title', 'Add Category')

@section('page-title', 'Add Category')

@section('content')

    <div class="page-header">

        <div>
            <span class="section-eyebrow">CATALOG</span>
            <h1>Add Category</h1>
            <p>Create a new category for your products</p>
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
                    <h2>New Category</h2>
                    <p>Enter a name for your new category</p>
                </div>

            </div>

            <form action="{{ route('admin.categories.store') }}" method="post" class="product-form">

                @csrf

                <div class="form-group">

                    <label for="name">Category Name</label>

                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Enter category name"
                        required maxlength="255" autofocus class="@error('name') input-error @enderror">

                    <small class="field-help">
                        Choose a clear and descriptive category name
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
                        Add Category
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection