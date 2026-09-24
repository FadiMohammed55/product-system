<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Product System</title>
    @vite(['resources/css/auth.css'])
</head>

<body class="auth-page">

    <main class="auth-container">

        <div class="auth-card">

            <div class="auth-brand">

                <div class="auth-logo">
                    PS
                </div>

                <h1>Product System</h1>

                <p>Create your customer account</p>

            </div>

            @if ($errors->any())
                <div class="auth-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register.store') }}" method="post" class="auth-form">

                @csrf

                <div class="form-group">

                    <label for="name">Name</label>

                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Enter your name"
                        autocomplete="name" required>

                </div>

                <div class="form-group">

                    <label for="email">Email</label>

                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        placeholder="Enter your email" autocomplete="email" required>

                </div>

                <div class="form-group">

                    <label for="password">Password</label>

                    <input type="password" name="password" id="password" placeholder="Create a password"
                        autocomplete="new-password" required>

                </div>

                <div class="form-group">

                    <label for="password_confirmation">Confirm Password</label>

                    <input type="password" name="password_confirmation" id="password_confirmation"
                        placeholder="Confirm your password" autocomplete="new-password" required>

                </div>

                <button type="submit" class="auth-button">
                    Create Account
                </button>

            </form>

            <div class="auth-register">
                <span>Already have an account?</span>
                <a href="{{ route('login') }}">Login</a>
            </div>

            <div class="auth-footer">
                <span>Product System</span>
            </div>

        </div>

    </main>

</body>

</html>