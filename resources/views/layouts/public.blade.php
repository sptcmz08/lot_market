<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ระบบจองเต็นท์ตลาด') - Market Tent Booking</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FF8FB1">
    
    <!-- FontAwesome for cute icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS style matches Mood & Tone -->
    <style>
        :root {
            --primary: #FF8FB1;
            --primary-hover: #ff769d;
            --secondary: #8BD3DD;
            --accent: #FFD166;
            --bg-page: #FFF7FA;
            --bg-card: #FFFFFF;
            --text-dark: #2F2F37;
            --text-muted: #7A7A85;
            --border-cute: #F1DDE5;
            
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
        }

        .header-bar {
            background-color: var(--bg-card);
            border-bottom: 2px solid var(--border-cute);
            padding: 15px 20px;
            box-shadow: 0 4px 15px rgba(255, 143, 177, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            background-color: var(--border-cute);
            color: var(--primary-hover);
        }

        .nav-btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: white !important;
            box-shadow: 0 4px 12px rgba(255, 143, 177, 0.25);
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
            border-radius: 24px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(47, 47, 55, 0.06);
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
            color: white;
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            box-shadow: 0 8px 20px rgba(255, 143, 177, 0.25);
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            text-align: center;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 143, 177, 0.35);
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
            background-color: var(--bg-card);
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            text-align: center;
        }

        .btn-secondary:hover {
            background-color: var(--bg-page);
            border-color: var(--primary);
            color: var(--primary-hover);
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

        .status-available { background-color: #E2F9E9; color: #1E7E34; }
        .status-pending { background-color: #FFF3CD; color: #856404; }
        .status-booked { background-color: #F8D7DA; color: #721C24; }
        .status-installing { background-color: #EBE3FC; color: #5B21B6; }
        .status-completed { background-color: #D1ECF1; color: #0C5460; }
        .status-blocked { background-color: #E2E3E5; color: #383D41; }
        .status-problem { background-color: #FFE6D2; color: #D35400; }

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
            font-size: 15px;
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
            background-color: #E2F9E9;
            color: #1E7E34;
            border-color: #C3E6CB;
        }

        .alert-danger {
            background-color: #F8D7DA;
            color: #721C24;
            border-color: #F5C6CB;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: var(--bg-card);
            border-top: 2px solid var(--border-cute);
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
                gap: 15px;
                padding: 15px;
            }
            .main-nav {
                width: 100%;
                justify-content: space-around;
            }
            .nav-link {
                padding: 8px 10px;
                font-size: 14px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <header class="header-bar">
        <a href="{{ route('public.map') }}" class="brand-title">
            <i class="fa-solid fa-campground"></i>
            <span>เช่าเต็นท์ตลาดหวานแหวว</span>
        </a>
        <nav class="main-nav">
            <a href="{{ route('public.map') }}" class="nav-link {{ Route::is('public.map') ? 'active' : '' }}">
                <i class="fa-solid fa-map-location-dot"></i> แผนที่จอง
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
