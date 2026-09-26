@extends('layouts.admin')

@section('title', 'Users')

@section('page-title', 'Users')

@section('content')

    <div class="page-header">

        <div>
            <span class="section-eyebrow">MANAGEMENT</span>
            <h1>Users</h1>
            <p>Manage administrators and customers</p>
        </div>

    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="table-card">

        <div class="filter-bar">

            <form action="{{ route('admin.users.index') }}" method="get" class="filter-form">

                <div class="filter-field">
                    <label for="search">Search</label>
                    <input type="text" name="search" id="search"
                        value="{{ request('search') }}"
                        placeholder="Name or email">
                </div>

                <div class="filter-field">
                    <label for="role">Role</label>
                    <select name="role" id="role">
                        <option value="">All roles</option>
                        <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                        <option value="customer" @selected(request('role') === 'customer')>Customer</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
                </div>

            </form>

        </div>

        @if ($users->count())

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Orders</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($users as $user)

                            <tr>

                                <td>
                                    <div class="customer-cell">
                                        <div class="customer-avatar">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>

                                        <div class="customer-info">
                                            <strong>{{ $user->name }}</strong>
                                            <span>{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="role-badge role-{{ $user->role }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>

                                <td>{{ $user->orders_count }}</td>

                                <td class="date">
                                    {{ $user->created_at?->format('M d, Y') }}
                                </td>

                                <td>
                                    <div class="table-actions">

                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">
                                            View
                                        </a>

                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary">
                                            Edit
                                        </a>

                                        @if (!auth()->user()->is($user))
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="post"
                                                onsubmit="return confirm('Delete this user? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            @if ($users->hasPages())
                <div class="pagination-wrapper">
                    {{ $users->links() }}
                </div>
            @endif

        @else

            <div class="empty-state">
                <div class="empty-state-icon" aria-hidden="true">◎</div>
                <h2>No Users Found</h2>
                <p>Try changing your search or role filter.</p>
            </div>

        @endif

    </div>

@endsection
