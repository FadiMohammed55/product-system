@extends('layouts.admin')

@section('title', 'Categories')

@section('page-title', 'Categories')

@section('content')

    <div class="page-header">

        <div>
            <span class="section-eyebrow">CATALOG</span>
            <h1>Categories</h1>
            <p>Organize your products into categories</p>
        </div>

        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <span aria-hidden="true">+</span>
            <span>Add Category</span>
        </a>

    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($categories->count())

        <div class="table-card">

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($categories as $category)

                            <tr>
                                <td>
                                    <span class="product-id">
                                        #{{ $category->id }}
                                    </span>
                                </td>
                                <td>
                                    <div class="category-table-cell">

                                        <div class="category-icon" aria-hidden="true">
                                            ▤
                                        </div>

                                        <div class="category-info">
                                            <strong>{{ $category->name }}</strong>
                                        </div>

                                    </div>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('admin.categories.edit', ['category' => $category->id]) }}"
                                            class="btn btn-secondary">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', ['category' => $category->id]) }}"
                                            method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    @else

        <div class="table-card">

            <div class="empty-state">

                <div class="empty-state-icon" aria-hidden="true">
                    ▤
                </div>

                <h2>No Categories Found</h2>

                <p>Create your first category to organize your products</p>

                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    Add Category
                </a>

            </div>

        </div>

    @endif

@endsection