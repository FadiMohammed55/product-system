<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Product System</title>
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

                <p>Sign in to your account</p>

            </div>

            @if ($errors->any())
                <div class="auth-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="/login" method="post" class="auth-form">

                @csrf

                <div class="form-group">

                    <label for="email">Email</label>

                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        placeholder="Enter your email" autocomplete="email" required>

                </div>

                <div class="form-group">

                    <label for="password">Password</label>

                    <input type="password" name="password" id="password" placeholder="Enter your password"
                        autocomplete="current-password" required>

                </div>

                <button type="submit" class="auth-button">
                    Login
                </button>

            </form>

            <div class="auth-footer">
                <span>Product System</span>
            </div>

        </div>

    </main>

</body>

</html>