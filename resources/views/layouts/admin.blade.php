<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'แผงควบคุมผู้ดูแลระบบ') - ระบบจองเต็นท์ตลาด</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
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
            font-family: 'Prompt', 'Outfit', sans-serif;
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
            overflow: hidden;
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

        /* Badges status */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 700;
        }

        .status-pending_admin { background-color: #FFF3CD; color: #856404; }
        .status-confirmed { background-color: #E2F9E9; color: #1E7E34; }
        .status-assigned { background-color: #E8F4FD; color: #004085; }
        .status-installing { background-color: #EBE3FC; color: #5B21B6; }
        .status-completed { background-color: #D1ECF1; color: #0C5460; }
        .status-cancelled { background-color: #E2E3E5; color: #383D41; }
        .status-problem { background-color: #FFE6D2; color: #D35400; }

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

        /* Pagination custom styling */
        .pagination-cute {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }

        .pagination-cute a, .pagination-cute span {
            padding: 10px 15px;
            border: 2px solid var(--border-cute);
            border-radius: 12px;
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 600;
            background-color: var(--bg-card);
            transition: all 0.2s ease;
        }

        .pagination-cute .active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
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
            }
            .top-navbar {
                padding: 15px 20px;
            }
            .content-body {
                padding: 20px 15px;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
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
                <a href="{{ route('admin.tasks.index') }}" class="sidebar-link {{ Route::is('admin.tasks.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-truck-ramp-box"></i> งานจัดส่งพนักงาน
                </a>
            </li>
            <li>
                <a href="{{ route('admin.lot_photo_reviews.index') }}" class="sidebar-link {{ Route::is('admin.lot_photo_reviews.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-camera-retro"></i> ตรวจรูปเลขล็อต
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
                <span class="user-badge"><i class="fa-solid fa-shield-halved"></i> แอดมิน</span>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fa-solid fa-right-from-bracket"></i> ออกจากระบบ
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
    </script>
    @yield('scripts')
</body>
</html>
