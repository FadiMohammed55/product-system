@extends('layouts.admin')

@section('title', 'Categories')

@section('page-title', 'Categories')

@section('content')

    <h2>Categories Management</h2>

    <p>Here you can manage all categories</p>

    <a href="{{ route('admin.categories.create') }}">
        Add Category
    </a>

    <hr>

    @if ($categories->count())

        <table>

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>

            </thead>

            <tbody>

                @foreach ($categories as $category)

                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $category) }}">
                                Edit
                            </a>

                            <form action="{{ route('admin.categories.destroy', $category) }}" method="post"
                                style="display: inline;">

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

        <p>No Categories Found</p>

    @endif

@endsection