<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'พนักงานติดตั้ง') - ระบบจองเต็นท์ตลาด</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- FontAwesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FF8FB1">
    
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
            
            /* Statuses */
            --waiting: #8BD3DD;
            --started: #A78BFA;
            --photo_uploaded: #FFD166;
            --completed: #4ECDC4;
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
            /* Support for mobile safe-areas */
            padding-bottom: env(safe-area-inset-bottom, 20px);
        }

        .staff-header {
            background-color: var(--bg-card);
            border-bottom: 2px solid var(--border-cute);
            padding: 18px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(255, 143, 177, 0.08);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .staff-brand {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-dark);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .staff-brand i {
            color: var(--primary);
            font-size: 22px;
        }

        .logout-form {
            margin: 0;
        }

        .logout-link {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 18px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .staff-content {
            flex: 1;
            padding: 15px;
            box-sizing: border-box;
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
        }

        /* Mobile task components */
        .staff-card {
            background-color: var(--bg-card);
            border-radius: 24px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(47, 47, 55, 0.06);
            border: 1px solid var(--border-cute);
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .lot-display-big {
            font-size: 52px;
            font-weight: 900;
            color: var(--primary-hover);
            text-align: center;
            letter-spacing: 1px;
            margin: 5px 0;
            line-height: 1;
            text-shadow: 1px 1px 0px rgba(255, 143, 177, 0.2);
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px dashed var(--border-cute);
            padding-bottom: 8px;
        }

        .info-row:last-of-type {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-label {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 14px;
        }

        .info-value {
            font-weight: 700;
            font-size: 15px;
        }

        /* Large touch friendly buttons */
        .btn-large {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            height: 52px; /* Minimum 48px as requested */
            border-radius: 18px;
            font-size: 16px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(47, 47, 55, 0.08);
            transition: all 0.2s ease;
            text-decoration: none;
            box-sizing: border-box;
        }

        .btn-large-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: white;
            box-shadow: 0 8px 20px rgba(255, 143, 177, 0.25);
        }

        .btn-large-secondary {
            background-color: var(--bg-card);
            border: 2px solid var(--border-cute);
            color: var(--text-dark);
        }

        .btn-large-danger {
            background-color: #F8D7DA;
            color: #721C24;
            border: 2px solid #F5C6CB;
        }

        .btn-large-success {
            background: linear-gradient(135deg, #4ECDC4, #3BBAAF);
            color: white;
            box-shadow: 0 8px 20px rgba(78, 205, 196, 0.25);
        }

        .btn-large:active {
            transform: scale(0.97);
        }

        .alert-cute {
            border-radius: 18px;
            padding: 15px;
            margin-bottom: 15px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .alert-success { background-color: #E2F9E9; color: #1E7E34; }
        .alert-danger { background-color: #F8D7DA; color: #721C24; }

        /* Sticky bottom action for thumbs */
        .sticky-bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: var(--bg-card);
            border-top: 2px solid var(--border-cute);
            padding: 12px 15px env(safe-area-inset-bottom, 12px) 15px;
            box-shadow: 0 -8px 25px rgba(255, 143, 177, 0.08);
            z-index: 99;
            display: flex;
            gap: 10px;
        }

        .has-sticky-bottom {
            padding-bottom: 95px; /* Offset to prevent overlap */
        }
    </style>
    @yield('styles')
</head>
<body class="@yield('body_class')">

    <header class="staff-header">
        <a href="{{ route('staff.tasks.index') }}" class="staff-brand">
            <i class="fa-solid fa-helmet-safety"></i>
            <span>งานติดตั้งของฉัน</span>
        </a>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">{{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="logout-link" title="ออกจากระบบ">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </header>

    <div class="staff-content">
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

    @yield('sticky_footer')
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
