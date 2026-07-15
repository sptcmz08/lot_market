<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงชื่อเข้าสู่ระบบ - ระบบจองเต็นท์ตลาด</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- FontAwesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
        }

        body {
            font-family: 'Prompt', sans-serif;
            background-color: var(--bg-page);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            box-sizing: border-box;
        }

        .login-card {
            background-color: var(--bg-card);
            border-radius: 28px;
            padding: 40px 30px;
            box-shadow: 0 15px 40px rgba(255, 143, 177, 0.12);
            border: 2px solid var(--border-cute);
            text-align: center;
        }

        .login-logo {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .login-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-dark);
            margin: 0 0 5px 0;
        }

        .login-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin: 0 0 30px 0;
        }

        .cute-input-group {
            margin-bottom: 20px;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .cute-label {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-dark);
        }

        .cute-input {
            border: 2px solid var(--border-cute);
            border-radius: 16px;
            padding: 12px 16px;
            font-size: 15px;
            font-family: inherit;
            color: var(--text-dark);
            background-color: var(--bg-card);
            outline: none;
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.2s ease;
        }

        .cute-input:focus {
            border-color: var(--primary);
        }

        .remember-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 500;
        }

        .btn-submit {
            width: 100%;
            border: none;
            border-radius: 18px;
            padding: 14px;
            font-size: 16px;
            font-weight: 700;
            color: white;
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            box-shadow: 0 8px 20px rgba(255, 143, 177, 0.25);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(255, 143, 177, 0.35);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: var(--primary-hover);
        }

        .alert-error {
            background-color: #F8D7DA;
            color: #721C24;
            border: 1px solid #F5C6CB;
            border-radius: 16px;
            padding: 12px 15px;
            margin-bottom: 20px;
            text-align: left;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <i class="fa-solid fa-helmet-safety"></i>
            </div>
            <h1 class="login-title">ระบบเจ้าหน้าที่และผู้จัดการ</h1>
            <p class="login-subtitle">ลงชื่อเข้าใช้งานระบบเพื่อบันทึกและตรวจสอบงาน</p>

            @if ($errors->any())
                <div class="alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="cute-input-group">
                    <label class="cute-label" for="login">Username / เบอร์โทร</label>
                    <input type="text" id="login" name="login" class="cute-input" value="{{ old('login') }}" required placeholder="เช่น admin หรือ staff" autofocus>
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="password">รหัสผ่าน</label>
                    <input type="password" id="password" name="password" class="cute-input" required placeholder="กรอกรหัสผ่าน...">
                </div>

                <div class="remember-row">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="remember" style="accent-color: var(--primary); width: 16px; height: 16px;">
                        <span>จดจำการเข้าสู่ระบบ</span>
                    </label>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-sign-in-alt"></i> เข้าสู่ระบบ
                </button>
            </form>

            <a href="{{ route('public.map') }}" class="back-link">
                <i class="fa-solid fa-arrow-left-long"></i> กลับไปยังหน้าแผนที่ตลาด
            </a>
        </div>
    </div>

</body>
</html>
