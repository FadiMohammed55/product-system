<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Store')</title>
    @vite(['resources/css/admin.css', 'resources/js/app.js'])
</head>

<body>

    <div class="app-layout customer-layout">

        <aside class="sidebar">

            <div class="sidebar-header">
                <h2>Product Store</h2>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('products.index') }}" class="{{ request()->is('products.index') ? 'active' : '' }}">
                    🛍️ Products </a>
                <a href="{{ route('cart.index') }}" class="{{ request()->is('cart.*') ? 'active' : '' }}">
                    🛒 Cart</a>
                <a href="{{ route('orders.index') }}" class="{{ request()->is('orders.*') ? 'active' : '' }}">
                    📦 My Orders </a>
            </nav>

            <div class="sidebar-footer">

                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button type="submit" class="logout-btn">
                        🚪 Logout
                    </button>
                </form>

            </div>

        </aside>

        <main class="main-content">

            <header class="topbar">

                <div>
                    <h3> Welcome, {{ auth()->user()->name }} </h3>
                </div>

            </header>

            <section class="content">
                @yield('content')
            </section>

        </main>

    </div>

</body>

</html>