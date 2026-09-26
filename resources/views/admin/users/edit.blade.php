@extends('layouts.admin')

@section('title', 'Edit User')

@section('page-title', 'Edit User')

@section('content')

    <div class="page-header">

        <div>
            <span class="section-eyebrow">USER MANAGEMENT</span>
            <h1>Edit User</h1>
            <p>Update account information and access level</p>
        </div>

        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">
            Back to User
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
                    <span class="section-eyebrow">ACCOUNT DETAILS</span>
                    <h2>{{ $user->name }}</h2>
                    <p>Update the information for this account</p>
                </div>
            </div>

            <form action="{{ route('admin.users.update', $user) }}" method="post" class="product-form">

                @csrf
                @method('PUT')

                <div class="form-group">

                    <label for="name">Name</label>

                    <input type="text" name="name" id="name"
                        value="{{ old('name', $user->name) }}" required maxlength="255"
                        class="@error('name') input-error @enderror">

                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror

                </div>

                <div class="form-group">

                    <label for="email">Email</label>

                    <input type="email" name="email" id="email"
                        value="{{ old('email', $user->email) }}" required maxlength="255"
                        class="@error('email') input-error @enderror">

                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror

                </div>

                <div class="form-group">

                    <label for="role">Role</label>

                    <select name="role" id="role" required class="@error('role') input-error @enderror">
                        <option value="customer" @selected(old('role', $user->role) === 'customer')>Customer</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                    </select>

                    <small class="field-help">
                        Admin accounts can access the control panel. Customer accounts cannot.
                    </small>

                    @error('role')
                        <p class="field-error">{{ $message }}</p>
                    @enderror

                </div>

                <div class="form-group">

                    <label for="password">New Password</label>

                    <input type="password" name="password" id="password"
                        placeholder="Leave blank to keep the current password"
                        minlength="8" autocomplete="new-password"
                        class="@error('password') input-error @enderror">

                    <small class="field-help">
                        Leave blank if you do not want to change the password.
                    </small>

                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror

                </div>

                <div class="form-group">

                    <label for="password_confirmation">Confirm New Password</label>

                    <input type="password" name="password_confirmation" id="password_confirmation"
                        minlength="8" autocomplete="new-password">

                </div>

                <div class="form-actions">

                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Save Changes
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection
