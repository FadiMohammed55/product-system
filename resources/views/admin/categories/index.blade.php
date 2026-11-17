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

    {{-- Search and categories Count --}}
    <div class="table-card">

        <div class="table-header">

            <div>
                <h2>All Categories</h2>
                <p>{{ $categories->total() }} categories found</p>
            </div>

            <form action="{{ route('admin.categories.index') }}" method="get" class="search-form">

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..."
                    aria-label="Search categories">

                <button type="submit" class="btn btn-primary">
                    Search
                </button>

                @if (request('search'))
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                        Clear
                    </a>
                @endif

            </form>

        </div>

        @if ($categories->count())

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Category</th>
                            <th>Products</th>
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
                                    <span class="badge">
                                        {{ $category->products_count }}
                                    </span>
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

            {{-- Pagination --}}
            <div class="pagination-wrapper">
                {{ $categories->links() }}
            </div>

        @else

            <div class="empty-state">

                <div class="empty-state-icon" aria-hidden="true">
                    ▤
                </div>

                @if (request('search'))

                    <h2>No Matching Categories</h2>

                    <p>No categories found matching
                        "{{ request('search') }}"
                    </p>

                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                        Clear Search
                    </a>

                @else

                    <h2>No Categories Found</h2>

                    <p>Create your first category to organize your products</p>

                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                        Add Category
                    </a>

                @endif

            </div>

        @endif

    </div>

@endsection