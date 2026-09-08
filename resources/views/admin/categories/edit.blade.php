@extends('layouts.admin')

@section('title', 'Edit Category')

@section('page-title', 'Edit Category')

@section('content')

    <h1>Edit Category</h1>

    <form action="{{ route('admin.categories.update', $category) }}" method="post">

        @csrf
        @method('PUT')

        <div>
            <label for="name">Category Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required>
        </div>

        <br>

        @error('name')
            <p>{{ $message }}</p>
        @enderror

        <button type="submit">
            Update Category
        </button>

    </form>

    <br>

    <a href="{{ route('admin.categories.index') }}">
        Back to Categories
    </a>

@endsection