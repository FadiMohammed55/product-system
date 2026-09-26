@extends('layouts.admin')

@section('title', 'User Details')

@section('page-title', 'User Details')

@section('content')

    <div class="page-header">

        <div>
            <span class="section-eyebrow">USER MANAGEMENT</span>
            <h1>{{ $user->name }}</h1>
            <p>View account information and activity</p>
        </div>

        <div class="page-header-actions">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">Edit User</a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to Users</a>
        </div>

    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="user-details-grid">

        <div class="card">

            <div class="card-header">
                <div>
                    <span class="section-eyebrow">ACCOUNT</span>
                    <h2>Account Information</h2>
                </div>
            </div>

            <div class="user-profile-card">

                <div class="large-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>

                <div>
                    <h3>{{ $user->name }}</h3>
                    <p>{{ $user->email }}</p>
                    <span class="role-badge role-{{ $user->role }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>

            </div>

            <div class="user-info-list">
                <div>
                    <span>Joined</span>
                    <strong>{{ $user->created_at?->format('M d, Y H:i') }}</strong>
                </div>
                <div>
                    <span>Last Updated</span>
                    <strong>{{ $user->updated_at?->format('M d, Y H:i') }}</strong>
                </div>
                <div>
                    <span>Orders</span>
                    <strong>{{ $user->orders_count }}</strong>
                </div>
            </div>

        </div>

    </div>

@endsection
