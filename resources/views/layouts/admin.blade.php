<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Product System</title>
    @vite(['resources/css/admin.css'])
</head>

<body>

    <div class="admin-layout">

        <div class="sidebar-overlay"></div>

        <aside class="sidebar" id="adminSidebar">

            <div class="sidebar-header">

                <a href="{{ route('admin.dashboard') }}" class="brand">
                    <span class="brand-icon">
                        PS
                    </span>
                    <span class="brand-text">
                        <strong>Product System</strong>
                        <small>Admin Panel</small>
                    </span>
                </a>

            </div>

            <nav class="sidebar-nav">

                <p class="nav-section-title">
                    MAIN MENU
                </p>

                <a href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon" aria-hidden="true">⌂</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.products.index') }}"
                    class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <span class="nav-icon" aria-hidden="true">▣</span>
                    <span>Products</span>
                </a>

                <a href="{{ route('admin.categories.index') }}"
                    class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <span class="nav-icon" aria-hidden="true">▤</span>
                    <span>Categories</span>
                </a>

                <a href="{{ route('admin.orders.index') }}"
                    class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <span class="nav-icon" aria-hidden="true">◫</span>
                    <span>Orders</span>
                </a>

                <a href="{{ route('admin.users.index') }}"
                    class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <span class="nav-icon" aria-hidden="true">◎</span>
                    <span>Users</span>
                </a>

            </nav>

            <div class="sidebar-bottom">

                <div class="admin-profile">

                    <div class="profile-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>

                    <div class="profile-info">
                        <strong>{{ Auth::user()->name }}</strong>
                        <span>Administrator</span>
                    </div>

                </div>

                <form action="{{ route('logout') }}" method="post">
                    @csrf

                    <button type="submit" class="sidebar-logout">
                        <span class="nav-icon">↪</span>
                        <span>Logout</span>
                    </button>

                </form>

            </div>

        </aside>

        <main class="main-content">

            <header class="topbar">

                <div class="topbar-left">

                    <button type="button" class="sidebar-toggle" aria-label="Toggle sidebar"
                        aria-controls="adminSidebar" aria-expanded="false">
                        ☰
                    </button>

                    <div class="page-heading">
                        <h1>@yield('page-title', 'Dashboard')</h1>
                        <p class="topbar-subtitle">Manage Your Store</p>
                    </div>

                </div>

                <div class="topbar-user">

                    <div class="topbar-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>

                    <div class="topbar-user-details">
                        <strong>{{ Auth::user()->name }}</strong>
                        <span>Administrator</span>
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

            const sidebar = document.querySelector('.sidebar');
            const toggleButton = document.querySelector('.sidebar-toggle');
            const overlay = document.querySelector('.sidebar-overlay');

            if (!sidebar || !toggleButton || !overlay) {
                return;
            }

            const closeSidebar = () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
                toggleButton.setAttribute('aria-expanded', 'false');
            };

            toggleButton.addEventListener('click', () => {
                const isOpen = sidebar.classList.toggle('open');
                overlay.classList.toggle('show', isOpen);
                toggleButton.setAttribute('aria-expanded', String(isOpen));
            });

            overlay.addEventListener('click', closeSidebar);

            window.addEventListener('resize', () => {
                if (window.innerWidth > 900) {
                    closeSidebar();
                }
            });

        });
    </script>

</body>

</html>