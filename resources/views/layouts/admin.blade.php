<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    @vite(['resources/css/admin.css'])
</head>

<body>

    <div class="admin-layout">

        {{-- Mobile Overlay --}}
        <div class="sidebar-overlay"></div>

        {{-- Sidebar --}}
        <aside class="sidebar">

            <div class="sidebar-header">

                <div class="brand-icon">
                    PS
                </div>

                <div class="brand-text">
                    <h2>Product System</h2>
                    <span>Admin Panel</span>
                </div>

            </div>

            <nav class="sidebar-nav">

                <p class="nav-section-title">MAIN MENU</p>

                <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">⌂</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.products.index') }}"
                    class="{{ request()->is('admin/products*') ? 'active' : '' }}">
                    <span class="nav-icon">▣</span>
                    <span>Products</span>
                </a>

                <a href="{{ route('admin.categories.index') }}"
                    class="{{ request()->is('admin/categories*') ? 'active' : '' }}">
                    <span class="nav-icon">▤</span>
                    <span>Categories</span>
                </a>

                <p class="nav-section-title">STORE</p>

                <a href="{{ route('products.index') }}" target="_blank">
                    <span class="nav-icon">◈</span>
                    <span>Customer Products</span>
                </a>

            </nav>

            {{-- Sidebar Bottom --}}
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

        {{-- Main Content --}}
        <main class="main-content">

            {{-- Topbar --}}
            <header class="topbar">

                <div class="topbar-left">

                    <button type="button" class="sidebar-toggle" aria-label="Toggle sidebar">
                        ☰
                    </button>

                    <div>
                        <h1>@yield('page-title', 'Dashboard')</h1>
                        <p class="topbar-subtitle">Manage your store</p>
                    </div>

                </div>

                <div class="user-info">

                    <div class="topbar-user">

                        <div class="topbar-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>

                        <div>
                            <strong>{{ Auth::user()->name }}</strong>
                            <span>Admin</span>
                        </div>

                    </div>

                </div>

            </header>

            {{-- Page Content --}}
            <section class="content">
                @yield('content')
            </section>

        </main>

    </div>

    {{-- Sidebar JavaScript --}}
    <script>
        const sidebar = document.querySelector('.sidebar');
        const toggleButton = document.querySelector('.sidebar-toggle');
        const overlay = document.querySelector('.sidebar-overlay');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });
    </script>

</body>

</html>