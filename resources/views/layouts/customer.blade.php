<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Product Store')</title>
    @vite(['resources/css/customer.css', 'resources/js/app.js'])
</head>

<body>

    <div class="customer-layout">

        <div class="customer-sidebar-overlay"></div>

        <aside id="customer-sidebar" class="sidebar">

            <a href="{{ route('products.index') }}" class="customer-brand">

                <span class="customer-brand-icon">PS</span>

                <span class="customer-brand-text">
                    <strong>Product Store</strong>
                    <span>Customer Area</span>
                </span>

            </a>

            <nav class="sidebar-nav">

                <p class="customer-nav-title">STORE</p>

                <a href="{{ route('products.index') }}"
                    class="{{ request()->routeIs('products.index') ? 'active' : '' }}">
                    <span class="customer-nav-icon" aria-hidden="true">◈</span>
                    <span>Products</span>
                </a>

                <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'active' : '' }}">
                    <span class="customer-nav-icon" aria-hidden="true">🛒</span>
                    <span>Cart</span>
                </a>

                <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
                    <span class="customer-nav-icon" aria-hidden="true">📦</span>
                    <span>My Orders</span>
                </a>

            </nav>

            <div class="customer-sidebar-footer">

                <div class="customer-user">

                    <div class="customer-user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <div class="customer-user-info">
                        <strong>{{ auth()->user()->name }}</strong>
                        <span>Customer</span>
                    </div>

                </div>

                <form action="{{ route('logout') }}" method="post">

                    @csrf

                    <button type="submit" class="customer-logout">
                        <span>↪</span>
                        <span>Logout</span>
                    </button>

                </form>

            </div>

        </aside>

        <main class="main-content">

            <header class="topbar">

                <div class="customer-topbar-left">

                    <button type="button" class="customer-sidebar-toggle" aria-label="Toggle navigation"
                        aria-expanded="false" aria-controls="customer-sidebar">
                        ☰
                    </button>

                    <div class="customer-welcome">
                        <strong>Welcome, {{ auth()->user()->name }}</strong>
                        <span>Discover products and manage your orders</span>
                    </div>

                </div>

            </header>

            <section class="content">
                @yield('content')
            </section>

        </main>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const sidebar = document.querySelector('.customer-layout .sidebar');
            const overlay = document.querySelector('.customer-sidebar-overlay');
            const toggleButton = document.querySelector('.customer-sidebar-toggle');

            if (!sidebar || !overlay || !toggleButton) {
                return;
            }

            const closeSidebar = () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
                toggleButton.setAttribute('aria-expanded', 'false')
            };

            toggleButton.addEventListener('click', () => {
                const isOpen = sidebar.classList.toggle('open');

                overlay.classList.toggle('show', isOpen);
                toggleButton.setAttribute('aria-expanded', String(isOpen));
            });

            overlay.addEventListener('click', closeSidebar);
        });
    </script>

</body>

</html>