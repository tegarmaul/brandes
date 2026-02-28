<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Brandes') — Sistem Monitoring Brankas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>

        /* ── Reset ── */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* ── Variabel Global ── */
        :root {
            --green:       #22C55E;
            --green-dark:  #16A34A;
            --green-light: #DCFCE7;
            --red:         #EF4444;
            --red-light:   #FEF2F2;
            --sidebar-w:   218px;
            --sidebar-min: 64px;
            --body-bg:     #F8FAFC;
            --border:      #E5E7EB;
            --text:        #111827;
            --text-muted:  #6B7280;
            --radius:      12px;
            --shadow:      0 1px 4px rgba(0,0,0,0.07);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--body-bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        /* ══════════════════════════════════════
           SIDEBAR
        ══════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: #fff;
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            overflow: hidden;
            transition: width 0.25s ease;
        }

        .sidebar.collapsed {
            width: var(--sidebar-min);
        }

        /* ── Brand ── */
        .sidebar-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            height: 72px;
            padding: 0 12px;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }

        .brand-icon img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .brand-info {
            overflow: hidden;
            white-space: nowrap;
            flex: 1;
        }

        .brand-info strong {
            display: block;
            font-size: 13.5px;
            font-weight: 800;
        }

        .brand-info span {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* ── Toggle Button ── */
        .sidebar-toggle {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            padding: 4px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: background 0.15s;
        }

        .sidebar-toggle:hover {
            background: var(--body-bg);
        }

        .sidebar-toggle svg {
            width: 20px;
            height: 20px;
            transition: transform 0.25s;
        }

        /* ── Collapsed State ── */
        .sidebar.collapsed .sidebar-toggle svg {
            transform: rotate(180deg);
        }

        .sidebar.collapsed .brand-icon,
        .sidebar.collapsed .brand-info {
            display: none;
        }

        .sidebar.collapsed .sidebar-brand {
            justify-content: center;
            padding: 0;
        }

        /* ── Nav ── */
        .sidebar-nav {
            flex: 1;
            padding: 12px 12px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            overflow: hidden;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border-radius: 9px;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            position: relative;
            transition: background 0.15s, color 0.15s;
        }

        .nav-item svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .nav-item:hover {
            background: var(--green-light);
            color: var(--green-dark);
        }

        .nav-item.active {
            background: var(--green);
            color: #fff;
            font-weight: 600;
        }

        .sidebar.collapsed .nav-item {
            justify-content: center;
            padding: 10px;
        }

        .sidebar.collapsed .nav-item .nav-label {
            display: none;
        }

        /* Tooltip saat collapsed */
        .sidebar.collapsed .nav-item::after {
            content: attr(data-title);
            position: absolute;
            left: calc(var(--sidebar-min) - 4px);
            top: 50%;
            transform: translateY(-50%);
            background: #1F2937;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 7px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            z-index: 200;
            transition: opacity 0.15s;
        }

        .sidebar.collapsed .nav-item:hover::after {
            opacity: 1;
        }

        /* ── Footer / Logout ── */
        .sidebar-footer {
            padding: 12px 10px;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }

        .nav-logout {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            width: 100%;
            border-radius: 9px;
            border: none;
            background: none;
            color: var(--red);
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 600;
            white-space: nowrap;
            cursor: pointer;
            position: relative;
            transition: background 0.15s;
        }

        .nav-logout svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .nav-logout:hover {
            background: var(--red-light);
        }

        .sidebar.collapsed .nav-logout {
            justify-content: center;
            padding: 10px;
        }

        .sidebar.collapsed .nav-logout .nav-label {
            display: none;
        }

        .sidebar.collapsed .nav-logout::after {
            content: 'Logout';
            position: absolute;
            left: calc(var(--sidebar-min) - 4px);
            top: 50%;
            transform: translateY(-50%);
            background: #1F2937;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 7px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            z-index: 200;
            transition: opacity 0.15s;
        }

        .sidebar.collapsed .nav-logout:hover::after {
            opacity: 1;
        }

        /* ══════════════════════════════════════
           MAIN WRAP
        ══════════════════════════════════════ */
        .main-wrap {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            transition: margin-left 0.25s ease;
        }

        .main-wrap.collapsed {
            margin-left: var(--sidebar-min);
        }

        /* ── Topbar ── */
        .topbar {
            height: 72px;
            background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-left h1 {
            font-size: 16px;
            font-weight: 700;
        }

        .topbar-left p {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        /* ── User Badge ── */
        .user-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--body-bg);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 6px 14px 6px 6px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: border-color 0.15s;
        }

        .user-badge:hover {
            border-color: var(--green);
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            background: var(--green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
        }

        .user-badge svg {
            width: 14px;
            height: 14px;
            color: var(--text-muted);
        }

        /* ── Page Content ── */
        .page-content {
            padding: 24px 28px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ══════════════════════════════════════
           MODAL LOGOUT
        ══════════════════════════════════════ */
        .logout-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.35);
            z-index: 300;
            align-items: center;
            justify-content: center;
        }

        .logout-overlay.show {
            display: flex;
        }

        .logout-modal {
            background: #fff;
            border-radius: 20px;
            padding: 28px 28px 24px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 12px 48px rgba(0,0,0,0.18);
            animation: fadeUp 0.25s ease;
        }

        .logout-modal-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }

        .logout-icon-wrap {
            width: 44px;
            height: 44px;
            background: #FEF2F2;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .logout-icon-wrap svg {
            width: 22px;
            height: 22px;
        }

        .logout-modal-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--text);
        }

        .logout-modal-desc {
            font-size: 15px;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .logout-modal-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .btn-batal {
            padding: 14px;
            border-radius: 12px;
            border: none;
            background: #F3F4F6;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-batal:hover {
            background: var(--border);
        }

        .btn-logout-confirm {
            padding: 14px;
            border-radius: 12px;
            border: none;
            background: var(--red);
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
        }

        .btn-logout-confirm:hover {
            background: #DC2626;
            box-shadow: 0 4px 16px rgba(239,68,68,0.35);
        }

        /* ── Responsive ── */
        @media (max-width: 640px) {
            .page-content {
                padding: 16px;
            }

            .logout-modal {
                margin: 16px;
            }
        }

    </style>

    @stack('styles')
</head>
<body>

    <!-- ════════════════════════════════════
         SIDEBAR
    ════════════════════════════════════ -->
    <aside class="sidebar" id="sidebar">

        <div class="sidebar-brand">
            <div class="brand-icon">
                <img src="{{ asset('images/logo_brandes.png') }}" alt="Brandes">
            </div>
            <div class="brand-info">
                <strong>BRANDES</strong>
                <span>Monitoring Brankas</span>
            </div>
            <button class="sidebar-toggle" onclick="toggleSidebar()" title="Toggle Sidebar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 17L13 12L18 7"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 17L6 12L11 7"/>
                </svg>
            </button>
        </div>

        <nav class="sidebar-nav">

            @if(session('user.role') === 'admin')

                <a href="{{ route('dashboard.admin') }}"
                   class="nav-item {{ request()->routeIs('dashboard.admin') ? 'active' : '' }}"
                   data-title="Dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 3H4C3.44772 3 3 3.44772 3 4V11C3 11.5523 3.44772 12 4 12H9C9.55228 12 10 11.5523 10 11V4C10 3.44772 9.55228 3 9 3Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 3H15C14.4477 3 14 3.44772 14 4V7C14 7.55228 14.4477 8 15 8H20C20.5523 8 21 7.55228 21 7V4C21 3.44772 20.5523 3 20 3Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H15C14.4477 12 14 12.4477 14 13V20C14 20.5523 14.4477 21 15 21H20C20.5523 21 21 20.5523 21 20V13C21 12.4477 20.5523 12 20 12Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 16H4C3.44772 16 3 16.4477 3 17V20C3 20.5523 3.44772 21 4 21H9C9.55228 21 10 20.5523 10 20V17C10 16.4477 9.55228 16 9 16Z"/>
                    </svg>
                    <span class="nav-label">Dashboard</span>
                </a>

                <a href="{{ route('admin.list') }}"
                   class="nav-item {{ request()->routeIs('admin.list') ? 'active' : '' }}"
                   data-title="List Admin">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 21V19C16 17.9391 15.5786 16.9217 14.8284 16.1716C14.0783 15.4214 13.0609 15 12 15H6C4.93913 15 3.92172 15.4214 3.17157 16.1716C2.42143 16.9217 2 17.9391 2 19V21"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89317 18.7122 8.75608 18.1676 9.45768C17.623 10.1593 16.8604 10.6597 16 10.88"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M22 21V19C21.9993 18.1137 21.7044 17.2528 21.1614 16.5523C20.6184 15.8519 19.8581 15.3516 19 15.13"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 11C11.2091 11 13 9.20914 13 7C13 4.79086 11.2091 3 9 3C6.79086 3 5 4.79086 5 7C5 9.20914 6.79086 11 9 11Z"/>
                    </svg>
                    <span class="nav-label">List Admin</span>
                </a>

                <a href="{{ route('user.list') }}"
                   class="nav-item {{ request()->routeIs('user.list') ? 'active' : '' }}"
                   data-title="List User">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0119.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 004.5 10.5a7.464 7.464 0 01-1.15 3.993m1.989 3.559A11.209 11.209 0 008.25 10.5a3.75 3.75 0 117.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 01-3.6 9.75m6.633-4.596a18.666 18.666 0 01-2.485 5.33"/>
                    </svg>
                    <span class="nav-label">List User</span>
                </a>

            @else

                <a href="{{ route('dashboard.user') }}"
                   class="nav-item {{ request()->routeIs('dashboard.user') ? 'active' : '' }}"
                   data-title="Dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/>
                    </svg>
                    <span class="nav-label">Dashboard</span>
                </a>

            @endif

            <a href="{{ route('history.akses') }}"
               class="nav-item {{ request()->routeIs('history.akses') ? 'active' : '' }}"
               data-title="History Akses">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="nav-label">History Akses</span>
            </a>

            <a href="{{ route('notifikasi.keamanan') }}"
               class="nav-item {{ request()->routeIs('notifikasi.keamanan') ? 'active' : '' }}"
               data-title="Notifikasi Keamanan">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                </svg>
                <span class="nav-label">Notifikasi Keamanan</span>
            </a>

            <a href="{{ route('lokasi.brankas') }}"
               class="nav-item {{ request()->routeIs('lokasi.brankas') ? 'active' : '' }}"
               data-title="Lokasi Brankas">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0115 0z"/>
                </svg>
                <span class="nav-label">Lokasi Brankas</span>
            </a>

        </nav>

        <div class="sidebar-footer">
            <button type="button" class="nav-logout" onclick="openLogoutModal()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 17L21 12L16 7"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12H9"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9"/>
                </svg>
                <span class="nav-label">Logout</span>
            </button>
        </div>

    </aside>

    <!-- ════════════════════════════════════
         MODAL LOGOUT
    ════════════════════════════════════ -->
    <div class="logout-overlay" id="logoutOverlay" onclick="closeLogoutOutside(event)">
        <div class="logout-modal">
            <div class="logout-modal-header">
                <div class="logout-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 17L21 12L16 7"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12H9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9"/>
                    </svg>
                </div>
                <span class="logout-modal-title">Konfirmasi Logout</span>
            </div>
            <p class="logout-modal-desc">
                Apakah Anda yakin ingin keluar dari sistem? Anda perlu login kembali untuk mengakses dashboard.
            </p>
            <div class="logout-modal-actions">
                <button type="button" class="btn-batal" onclick="closeLogoutModal()">Batal</button>
                <form action="{{ route('logout') }}" method="POST" style="display:contents;">
                    @csrf
                    <button type="submit" class="btn-logout-confirm">Logout</button>
                </form>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════
         MAIN WRAP
    ════════════════════════════════════ -->
    <div class="main-wrap" id="mainWrap">

        <header class="topbar">
            <div class="topbar-left">
                <h1>@yield('page_title', 'Dashboard')</h1>
                <p>@yield('page_subtitle', 'Sistem Keamanan Brankas BRANDES.')</p>
            </div>
            <div class="user-badge">
                <div class="user-avatar">
                    {{ strtoupper(substr(session('user.nama', 'A'), 0, 1)) }}
                </div>
                {{ session('user.nama', 'Admin') }}
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                </svg>
            </div>
        </header>

        <main class="page-content">
            @yield('content')
        </main>

    </div>

    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')

</body>
</html>