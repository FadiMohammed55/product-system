@extends('layouts.admin')

@section('title', 'Add Category')

@section('page-title', 'Add Category')

@section('content')

    <h1>Add Category</h1>

    <form action="{{ route('admin.categories.store') }}" method="post">

        @csrf

        <div>
            <label for="name">Category Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
        </div>

        <br>

        @error('name')
            <p>{{ $message }}</p>
        @enderror

        <button type="submit">
            Add Category
        </button>

    </form>

    <br>

    <a href="{{ route('admin.categories.index') }}">
        Back to Categories
    </a>

@endsection