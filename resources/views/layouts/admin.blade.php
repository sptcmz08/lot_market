<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'แผงควบคุมผู้ดูแลระบบ') - ระบบจองเต็นท์ตลาด</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700;800&family=Prompt:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FF8FB1">
    
    <!-- Admin Styling -->
    <style>
        :root {
            --primary: #FF8FB1;
            --primary-hover: #ff769d;
            --secondary: #8BD3DD;
            --accent: #FFD166;
            --bg-page: #FAF5F7;
            --bg-card: #FFFFFF;
            --text-dark: #2F2F37;
            --text-muted: #7A7A85;
            --border-cute: #F1DDE5;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Noto Sans Thai', 'Prompt', 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: var(--bg-page);
            color: var(--text-dark);
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Container */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--bg-card);
            border-right: 2px solid var(--border-cute);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 25px 20px;
            border-bottom: 2px solid var(--border-cute);
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 800;
            font-size: 18px;
        }

        .sidebar-brand i {
            color: var(--primary);
            font-size: 24px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 10px;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 600;
            border-radius: 14px;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover, .sidebar-link.active {
            background-color: var(--border-cute);
            color: var(--primary-hover);
        }

        .sidebar-link i {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 2px solid var(--border-cute);
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Main Content Panel */
        .main-panel {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            box-sizing: border-box;
        }

        *, *::before, *::after { box-sizing: border-box; }
        button, input, select, textarea { font: inherit; }
        .btn-primary, .btn-secondary { min-height: 44px; line-height: 1.25; white-space: normal; }
        .admin-action-list { display: flex; flex-wrap: wrap; align-items: stretch; gap: 8px; }
        .admin-action-list > a,
        .admin-action-list > form,
        .admin-action-list > label,
        .admin-action-list > button { min-height: 44px; }
        .admin-action-list > form { display: flex; }
        .admin-action-list > form > button,
        .admin-action-list > form > label,
        .admin-action-list > a,
        .admin-action-list > button { display: inline-flex; align-items: center; justify-content: center; }

        /* Action buttons inside tables - Single Horizontal Row */
        .cute-table .booking-action-list,
        .cute-table .admin-action-list {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-wrap: nowrap;
            white-space: nowrap;
        }

        .cute-table .booking-action-list > a,
        .cute-table .booking-action-list > form,
        .cute-table .booking-action-list > button,
        .cute-table .booking-action-list > form > label,
        .cute-table .booking-action-list > form > button,
        .cute-table .admin-action-list > a,
        .cute-table .admin-action-list > form,
        .cute-table .admin-action-list > button,
        .cute-table .admin-action-list > form > label,
        .cute-table .admin-action-list > form > button {
            min-height: 34px;
            height: 34px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 9px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            margin: 0;
            flex: 0 0 auto;
        }

        .admin-filter-form { min-width: 0; }
        .admin-form-actions { display: flex; gap: 12px; margin-top: 30px; }
        .admin-form-actions > * { min-height: 44px; }

        .top-navbar {
            background-color: var(--bg-card);
            border-bottom: 2px solid var(--border-cute);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(255, 143, 177, 0.04);
        }

        .page-title {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-badge {
            background-color: var(--border-cute);
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            color: var(--primary-hover);
        }

        .logout-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background-color: #F8D7DA;
            color: #721C24;
        }

        .content-body {
            padding: 30px;
            flex: 1;
        }

        /* Styling details */
        .cute-card {
            background-color: var(--bg-card);
            border-radius: 24px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(47, 47, 55, 0.05);
            border: 1px solid var(--border-cute);
            margin-bottom: 25px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            border-radius: 16px;
            padding: 12px 20px;
            font-size: 15px;
            font-weight: 700;
            color: white;
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            box-shadow: 0 6px 16px rgba(255, 143, 177, 0.25);
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 143, 177, 0.35);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 2px solid var(--border-cute);
            border-radius: 16px;
            padding: 10px 18px;
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
            background-color: var(--bg-card);
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background-color: var(--bg-page);
        }

        /* Tables style */
        .cute-table-container {
            background-color: var(--bg-card);
            border-radius: 20px;
            border: 1px solid var(--border-cute);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            box-shadow: 0 8px 25px rgba(47, 47, 55, 0.03);
            margin-bottom: 20px;
        }

        .cute-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .cute-table th {
            background-color: #FAF5F7;
            padding: 15px 20px;
            font-weight: 700;
            font-size: 14px;
            color: var(--text-dark);
            border-bottom: 2px solid var(--border-cute);
        }

        .cute-table td {
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-cute);
            font-size: 14px;
        }

        .cute-table tr:last-child td {
            border-bottom: none;
        }

        .cute-table tr:hover td {
            background-color: #FFFDFE;
        }

        /* Badges status - High Contrast Modern Palette */
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border-radius: 10px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.35;
            white-space: nowrap;
            letter-spacing: 0.01em;
            border: 1px solid transparent;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }

        .status-badge i {
            font-size: 11px;
        }

        /* Status colors with high contrast & soft borders */
        .status-pending_admin, .status-pending { background-color: #FEF3C7; border-color: #FDE68A; color: #92400E; }
        .status-confirmed { background-color: #ECFDF5; border-color: #A7F3D0; color: #047857; }
        .status-assigned { background-color: #EFF6FF; border-color: #BFDBFE; color: #1D4ED8; }
        .status-installing { background-color: #F3E8FF; border-color: #E9D5FF; color: #6B21A8; }
        .status-completed, .status-available { background-color: #F0FDFA; border-color: #99F6E4; color: #0F766E; }
        .status-cancelled, .status-blocked { background-color: #F1F5F9; border-color: #CBD5E1; color: #475569; }
        .status-problem { background-color: #FFF1F2; border-color: #FECDD3; color: #BE123C; }
        .status-photo_review, .status-photo_uploaded { background-color: #EEF2FF; border-color: #C7D2FE; color: #3730A3; }

        /* Payment Badges */
        .badge-payment-front_store { background-color: #FEF9C3; border-color: #FDE047; color: #854D0E; }
        .badge-payment-slip_attached { background-color: #E0F2FE; border-color: #BAE6FD; color: #0369A1; }
        .badge-payment-slip_pending { background-color: #FFEDD5; border-color: #FED7AA; color: #C2410C; }

        /* Forms inputs */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .cute-input-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .cute-label {
            font-weight: 600;
            font-size: 14px;
        }

        .cute-input, .cute-select, .cute-textarea {
            border: 2px solid var(--border-cute);
            border-radius: 14px;
            padding: 10px 14px;
            font-size: 14px;
            font-family: inherit;
            color: var(--text-dark);
            background-color: var(--bg-card);
            outline: none;
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.2s ease;
        }

        .cute-input:focus, .cute-select:focus, .cute-textarea:focus {
            border-color: var(--primary);
        }

        .alert-cute {
            border-radius: 18px;
            padding: 15px 20px;
            margin-bottom: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid transparent;
        }

        .alert-success { background-color: #E2F9E9; color: #1E7E34; border-color: #C3E6CB; }
        .alert-danger { background-color: #F8D7DA; color: #721C24; border-color: #F5C6CB; }

        /* Fix Laravel Pagination Sizing & Styling */
        .pagination-cute, .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 24px;
            margin-bottom: 10px;
            width: 100%;
        }

        .pagination-cute nav, .pagination nav {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            width: 100%;
        }

        .pagination-cute nav > div:first-child,
        .pagination nav > div:first-child {
            display: none !important;
        }

        .pagination-cute nav > div:last-child,
        .pagination nav > div:last-child {
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            box-shadow: none !important;
            border: none !important;
            background: transparent !important;
        }

        .pagination-cute nav svg,
        .pagination nav svg,
        .pagination-cute svg,
        .pagination svg {
            width: 16px !important;
            height: 16px !important;
            max-width: 16px !important;
            max-height: 16px !important;
            min-width: 16px !important;
            min-height: 16px !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }

        .pagination-cute nav p,
        .pagination nav p {
            font-size: 13px;
            color: var(--text-muted);
            margin: 0;
        }

        .pagination-cute nav a,
        .pagination nav a,
        .pagination-cute nav span[aria-current="page"],
        .pagination nav span[aria-current="page"],
        .pagination-cute nav span[aria-disabled="true"],
        .pagination nav span[aria-disabled="true"] {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 38px !important;
            height: 38px !important;
            padding: 0 12px !important;
            border: 1px solid var(--border-cute) !important;
            border-radius: 10px !important;
            background-color: var(--bg-card) !important;
            color: var(--text-dark) !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            text-decoration: none !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
            margin: 0 !important;
        }

        .pagination-cute nav a:hover,
        .pagination nav a:hover {
            background-color: var(--bg-page) !important;
            border-color: var(--primary) !important;
            color: var(--primary-hover) !important;
        }

        .pagination-cute nav span[aria-current="page"],
        .pagination nav span[aria-current="page"] {
            background: var(--primary) !important;
            color: white !important;
            border-color: var(--primary) !important;
            box-shadow: 0 4px 12px rgba(255, 143, 177, 0.35) !important;
        }

        .pagination-cute nav span[aria-disabled="true"],
        .pagination nav span[aria-disabled="true"] {
            opacity: 0.4 !important;
            cursor: not-allowed !important;
        }

        /* Mobile specific admin styles */
        @media (max-width: 1023px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-panel {
                margin-left: 0;
                min-width: 0;
            }
            .top-navbar {
                padding: 10px 14px;
                gap: 8px;
            }
            .page-title {
                font-size: 15px;
                font-weight: 700;
                line-height: 1.2;
            }
            .user-info {
                gap: 6px;
                flex-shrink: 0;
            }
            .user-badge {
                font-size: 11px;
                padding: 4px 8px;
            }
            .logout-btn {
                font-size: 12px;
                padding: 4px 8px;
            }
            .content-body {
                padding: 12px 10px;
                min-width: 0;
                overflow-x: hidden;
            }
            .cute-card {
                padding: 14px 12px;
                border-radius: 16px;
                margin-bottom: 15px;
            }
            .btn-primary, .btn-secondary {
                padding: 8px 14px;
                font-size: 13px;
                border-radius: 12px;
            }
            .cute-table th, .cute-table td {
                padding: 10px 12px;
                font-size: 13px;
            }
            .cute-table-container {
                border-radius: 14px;
                margin-bottom: 15px;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }

            .admin-action-list { width: 100%; }
            .admin-action-list > a,
            .admin-action-list > form,
            .admin-action-list > form > button,
            .admin-action-list > form > label,
            .admin-action-list > button { flex: 1 1 150px; }
            .admin-action-list > form > button,
            .admin-action-list > form > label { width: 100%; }
            .admin-form-actions { width: 100%; }
            .admin-form-actions > * { flex: 1 1 0; text-align: center; justify-content: center; }
            .admin-filter-form { display: grid !important; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px !important; align-items: stretch !important; }
            .admin-filter-form .cute-input-group { min-width: 0 !important; width: auto; }
            .admin-filter-form .filter-action-btns { grid-column: 1 / -1; width: 100%; }
            .admin-filter-form .filter-action-btns > * { flex: 1 1 0; }
            .admin-page-toolbar { align-items: stretch !important; flex-direction: column; }
            .admin-page-toolbar > a { width: 100%; }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 14px;
            }
            .top-navbar {
                padding: 8px 10px;
            }
            .logout-text {
                display: none;
            }
            .content-body {
                padding: 10px 6px;
            }
            .admin-form-actions { flex-direction: column; }
            .admin-form-actions > * { width: 100%; }
            .cute-card-title { line-height: 1.35; }
            .admin-filter-form { grid-template-columns: 1fr; }
            .admin-filter-form .filter-action-btns { grid-column: auto; }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar navigation -->
    <aside class="sidebar" id="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <i class="fa-solid fa-campground"></i>
            <span>ระบบหลังบ้านร้านเต็นท์</span>
        </a>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('public.booking.create') }}" class="sidebar-link">
                    <i class="fa-solid fa-house"></i> Home
                </a>
            </li>
            <li>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge"></i> แผงควบคุม
                </a>
            </li>
            <li>
                <a href="{{ route('admin.bookings.index') }}" class="sidebar-link {{ Route::is('admin.bookings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-check"></i> รายการจอง
                </a>
            </li>
            <li>
                <a href="{{ route('admin.map.index') }}" class="sidebar-link {{ Route::is('admin.map.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-map-location-dot"></i> แผนผังโซนล็อต
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ Route::is('admin.users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-cog"></i> ผู้ใช้งานพนักงาน
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ Route::is('admin.reports.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i> รายงานสถิติ
                </a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <div>ลงชื่อเข้าใช้เป็น:</div>
            <strong style="color: var(--text-dark);">{{ Auth::user()->name }}</strong>
        </div>
    </aside>

    <div class="main-panel">
        <header class="top-navbar">
            <div style="display: flex; align-items: center; gap: 15px;">
                <button id="toggle-sidebar" class="btn-secondary" style="padding: 8px 12px; display: none;">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1 class="page-title">@yield('page_title', 'แผงควบคุม')</h1>
            </div>
            
            <div class="user-info">
                <button type="button" id="enable-notification-btn" class="btn-notification-toggle" style="padding: 6px 12px; font-size: 12px; font-weight: 700; border-radius: 10px; border: 1px solid var(--border-cute); background: var(--bg-card); color: var(--text-dark); cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease;" title="เปิดการแจ้งเตือนบนมือถือและบราวเซอร์">
                    <i class="fa-solid fa-bell"></i> <span class="notification-btn-text">เปิดแจ้งเตือนมือถือ</span>
                </button>
                <span class="user-badge"><i class="fa-solid fa-shield-halved"></i> แอดมิน</span>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fa-solid fa-right-from-bracket"></i> <span class="logout-text">ออกจากระบบ</span>
                    </button>
                </form>
            </div>
        </header>

        <main class="content-body">
            @if (session('success'))
                <div class="alert-cute alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert-cute alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // Sidebar toggle for mobile/tablet responsive layouts
        const toggleBtn = document.getElementById('toggle-sidebar');
        const sidebar = document.getElementById('admin-sidebar');
        
        if (window.innerWidth < 1024) {
            toggleBtn.style.display = 'block';
        }
        
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        // Close sidebar when clicking outside on mobile devices
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1024 && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js').catch(() => {});
            });
        }

        (function() {
            let lastPendingCount = null;
            let lastReviewCount = null;

            function playChimeSound() {
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const now = audioCtx.currentTime;
                    
                    const osc1 = audioCtx.createOscillator();
                    const gain1 = audioCtx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(880, now);
                    gain1.gain.setValueAtTime(0.15, now);
                    gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                    osc1.connect(gain1);
                    gain1.connect(audioCtx.destination);
                    osc1.start(now);
                    osc1.stop(now + 0.3);

                    const osc2 = audioCtx.createOscillator();
                    const gain2 = audioCtx.createGain();
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(1174.66, now + 0.15);
                    gain2.gain.setValueAtTime(0.2, now + 0.15);
                    gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.5);
                    osc2.connect(gain2);
                    gain2.connect(audioCtx.destination);
                    osc2.start(now + 0.15);
                    osc2.stop(now + 0.5);
                } catch(e) {}
            }

            function showPushNotification(title, body, url, tag) {
                playChimeSound();

                if (window.Notification && Notification.permission === 'granted') {
                    if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                        navigator.serviceWorker.ready.then(function(reg) {
                            reg.showNotification(title, {
                                body: body,
                                icon: '/images/tent.png',
                                badge: '/images/tent.png',
                                tag: tag || 'lot-market-alert',
                                data: { url: url },
                                vibrate: [200, 100, 200]
                            });
                        }).catch(function() {
                            new Notification(title, { body: body, icon: '/images/tent.png', data: { url: url } });
                        });
                    } else {
                        const n = new Notification(title, { body: body, icon: '/images/tent.png', data: { url: url } });
                        n.onclick = function(e) {
                            e.preventDefault();
                            window.open(url, '_blank');
                        };
                    }
                }
            }

            function checkNotifications() {
                fetch('{{ route("notifications.check") }}')
                    .then(res => res.json())
                    .then(data => {
                        if (lastPendingCount !== null && data.pending_bookings_count > lastPendingCount) {
                            const b = data.latest_pending_booking;
                            if (b) {
                                showPushNotification(
                                    '🔔 มีรายการจองใหม่เข้ามา!',
                                    `รหัส ${b.code} (แผง ${b.lots}) - ร้าน ${b.shop}`,
                                    b.url,
                                    'new-booking-' + b.id
                                );
                            }
                        }
                        lastPendingCount = data.pending_bookings_count;

                        if (lastReviewCount !== null && data.photo_review_count > lastReviewCount) {
                            const r = data.latest_photo_review;
                            if (r) {
                                showPushNotification(
                                    '📸 สตาฟส่งรูปงานติดตั้งรอตรวจ!',
                                    `รหัส ${r.code} (แผง ${r.lots}) - งาน ${r.type_label}`,
                                    r.url,
                                    'photo-review-' + r.task_id
                                );
                            }
                        }
                        lastReviewCount = data.photo_review_count;
                    })
                    .catch(() => {});
            }

            document.addEventListener('DOMContentLoaded', function() {
                const btn = document.getElementById('enable-notification-btn');

                function updateBtnState() {
                    if (!btn) return;
                    const textSpan = btn.querySelector('.notification-btn-text');
                    if (Notification.permission === 'granted') {
                        btn.style.backgroundColor = '#ECFDF5';
                        btn.style.borderColor = '#A7F3D0';
                        btn.style.color = '#047857';
                        if (textSpan) textSpan.textContent = 'เปิดแจ้งเตือนแล้ว';
                        btn.title = 'การแจ้งเตือนบนมือถือและบราวเซอร์เปิดใช้งานอยู่';
                    } else if (Notification.permission === 'denied') {
                        btn.style.backgroundColor = '#FFF1F2';
                        btn.style.borderColor = '#FECDD3';
                        btn.style.color = '#BE123C';
                        if (textSpan) textSpan.textContent = 'ระงับการแจ้งเตือน';
                        btn.title = 'สิทธิ์การแจ้งเตือนถูกปฏิเสธในบราวเซอร์';
                    } else {
                        btn.style.backgroundColor = 'var(--bg-card)';
                        btn.style.borderColor = 'var(--border-cute)';
                        btn.style.color = 'var(--text-dark)';
                        if (textSpan) textSpan.textContent = 'เปิดแจ้งเตือนมือถือ';
                        btn.title = 'กดเพื่อเปิดการแจ้งเตือนรายการจองใหม่และรูปงานตรวจ';
                    }
                }

                if ('Notification' in window) {
                    updateBtnState();

                    // Auto prompt permission upon logging in / visiting admin page
                    if (Notification.permission === 'default') {
                        Notification.requestPermission().then(function() {
                            updateBtnState();
                        });
                    }

                    if (btn) {
                        btn.addEventListener('click', function() {
                            Notification.requestPermission().then(function(perm) {
                                updateBtnState();
                                if (perm === 'granted') {
                                    showPushNotification(
                                        '🎉 เปิดการแจ้งเตือนสำเร็จ!',
                                        'ระบบจะแจ้งเตือนเมื่อมีรายการจองใหม่ หรือสตาฟส่งรูปงานเข้ามา',
                                        window.location.href,
                                        'test-alert'
                                    );
                                }
                            });
                        });
                    }
                } else if (btn) {
                    btn.style.display = 'none';
                }

                checkNotifications();
                setInterval(checkNotifications, 15000);
            });
        })();
    </script>
    @yield('scripts')
</body>
</html>
