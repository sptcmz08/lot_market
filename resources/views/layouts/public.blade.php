<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'ระบบจองเต็นท์ตลาด') - Market Tent Booking</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0F172A">
    
    <!-- FontAwesome for cute icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS style matches Mood & Tone -->
    <style>
        :root {
            --primary: #38BDF8;
            --primary-hover: #0EA5E9;
            --secondary: #2DD4BF;
            --accent: #F59E0B;
            --bg-page: #0F172A;
            --bg-card: #111827;
            --bg-card-soft: #1F2937;
            --text-dark: #F8FAFC;
            --text-muted: #CBD5E1;
            --border-cute: #334155;
            
            /* Statuses colors */
            --available: #6FD08C;
            --pending: #FFD166;
            --booked: #FF6B6B;
            --installing: #A78BFA;
            --completed: #4ECDC4;
            --blocked: #BDBDBD;
            --problem: #FF9F1C;
        }

        body {
            font-family: 'Prompt', 'Outfit', sans-serif;
            background-color: var(--bg-page);
            color: var(--text-dark);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-text-size-adjust: 100%;
            overflow-x: hidden;
        }

        .header-bar {
            background-color: var(--bg-card);
            border-bottom: 1px solid var(--border-cute);
            padding: 15px 20px;
            box-shadow: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .brand-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-dark);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-title i {
            color: var(--primary);
            font-size: 24px;
        }

        .main-nav {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 12px;
            transition: all 0.2s ease;
            min-height: 42px;
            box-sizing: border-box;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .nav-link:hover, .nav-link.active {
            background-color: var(--bg-card-soft);
            color: var(--primary);
        }

        .nav-btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: #08111F !important;
            box-shadow: none;
        }

        .content-container {
            flex: 1;
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
            padding: 20px 15px;
            box-sizing: border-box;
        }

        /* Cute common components */
        .cute-card {
            background-color: var(--bg-card);
            border-radius: 18px;
            padding: 25px;
            box-shadow: none;
            border: 1px solid var(--border-cute);
            margin-bottom: 20px;
        }

        .cute-card-title {
            font-size: 22px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 20px;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cute-card-title i {
            color: var(--primary);
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            border-radius: 18px;
            padding: 14px 22px;
            font-size: 16px;
            font-weight: 700;
            color: #08111F;
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            box-shadow: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            text-align: center;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: none;
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 2px solid var(--border-cute);
            border-radius: 18px;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            background-color: var(--bg-card-soft);
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            text-align: center;
        }

        .btn-secondary:hover {
            background-color: #0B1220;
            border-color: var(--primary);
            color: var(--primary);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 700;
        }

        .status-available { background-color: #064E3B; color: #A7F3D0; }
        .status-pending { background-color: #78350F; color: #FDE68A; }
        .status-booked { background-color: #7F1D1D; color: #FECACA; }
        .status-installing { background-color: #4C1D95; color: #DDD6FE; }
        .status-completed { background-color: #164E63; color: #BAE6FD; }
        .status-blocked { background-color: #374151; color: #E5E7EB; }
        .status-problem { background-color: #7C2D12; color: #FED7AA; }

        .cute-input-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .cute-label {
            font-weight: 600;
            font-size: 15px;
            color: var(--text-dark);
        }

        .cute-input, .cute-select, .cute-textarea {
            border: 2px solid var(--border-cute);
            border-radius: 16px;
            padding: 12px 16px;
            font-size: 16px;
            font-family: inherit;
            color: var(--text-dark);
            background-color: var(--bg-card);
            outline: none;
            transition: border-color 0.2s ease;
            width: 100%;
            box-sizing: border-box;
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

        .alert-success {
            background-color: #052E2B;
            color: #99F6E4;
            border-color: #115E59;
        }

        .alert-danger {
            background-color: #450A0A;
            color: #FECACA;
            border-color: #991B1B;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: var(--bg-card);
            border-top: 1px solid var(--border-cute);
            color: var(--text-muted);
            font-size: 13px;
            margin-top: auto;
        }

        footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        /* Mobile specific fixes */
        @media (max-width: 767px) {
            .header-bar {
                flex-direction: column;
                gap: 10px;
                padding: 10px 12px;
                align-items: stretch;
            }

            .brand-title {
                justify-content: center;
                font-size: 17px;
                line-height: 1.25;
            }

            .brand-title i {
                font-size: 21px;
            }

            .main-nav {
                width: 100%;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .nav-link {
                padding: 9px 10px;
                font-size: 13px;
                border: 1px solid var(--border-cute);
                background: var(--bg-card);
            }

            .nav-btn {
                background: linear-gradient(135deg, var(--primary), var(--primary-hover));
                border: 0;
            }

            .content-container {
                padding: 12px 10px 18px;
                min-width: 0;
            }

            .cute-card {
                border-radius: 18px;
                padding: 16px;
                margin-bottom: 14px;
            }

            .cute-card-title {
                font-size: 18px;
                line-height: 1.35;
                margin-bottom: 14px;
            }

            .btn-primary,
            .btn-secondary {
                min-height: 48px;
                width: 100%;
                padding: 12px 14px;
                font-size: 15px;
            }

            .alert-cute {
                align-items: flex-start;
                padding: 12px 14px;
                font-size: 14px;
            }

            .cute-input,
            .cute-select,
            .cute-textarea {
                font-size: 16px;
                min-width: 0;
            }

            footer {
                padding: 14px 10px calc(14px + env(safe-area-inset-bottom, 0px));
            }
        }

        @media (max-width: 390px) {
            .header-bar { padding-inline: 8px; }
            .brand-title { font-size: 15px; }
            .main-nav { gap: 6px; }
            .nav-link { padding: 8px 5px; font-size: 12px; gap: 4px; }
            .content-container { padding-inline: 7px; }
        }
    </style>
    @yield('styles')
</head>
<body>

    <header class="header-bar">
        <a href="{{ route('public.booking.create') }}" class="brand-title">
            <i class="fa-solid fa-campground"></i>
            <span>จองเต็นท์และเคาน์เตอร์ตลาด</span>
        </a>
        <nav class="main-nav">
            <a href="{{ route('public.booking.create') }}" class="nav-link {{ Route::is('public.booking.create') ? 'active' : '' }}">
                <i class="fa-solid fa-file-signature"></i> จองอุปกรณ์
            </a>
            <a href="{{ route('public.map') }}" class="nav-link {{ Route::is('public.map') ? 'active' : '' }}">
                <i class="fa-solid fa-map-location-dot"></i> ดูแผนผัง
            </a>
            <a href="{{ route('public.booking.check') }}" class="nav-link {{ Route::is('public.booking.check') || Route::is('public.booking.check.submit') ? 'active' : '' }}">
                <i class="fa-solid fa-search"></i> ตรวจสถานะ
            </a>
            <a href="{{ route('login') }}" class="nav-link nav-btn">
                <i class="fa-solid fa-sign-in-alt"></i> พนักงาน / แอดมิน
            </a>
        </nav>
    </header>

    <div class="content-container">
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
    </div>

    <footer>
        <div>&copy; {{ date('Y') }} ระบบจองและติดตั้งเต็นท์ตลาด &bull; ⛺ ออกแบบด้วยความรักและความใส่ใจ</div>
    </footer>

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
