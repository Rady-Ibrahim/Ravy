<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Admin login') }} | {{ config('app.name', 'Ravy Boutique') }}</title>
    @if (file_exists(public_path('images/brand/ravy-logo.png')))
        <link rel="icon" type="image/png" href="{{ asset('images/brand/ravy-logo.png') }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-gold: #c5a059; /* لون ذهبي خافت للفخامة */
            --navy-dark: #040d1a;
            --navy-surface: rgba(10, 25, 47, 0.7);
            --cream: #f9f6ee;
            --glass-border: rgba(255, 255, 255, 0.1);
            --input-bg: rgba(255, 255, 255, 0.05);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Tajawal', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--navy-dark);
            /* خلفية احترافية متدرجة مع حركة */
            background: radial-gradient(circle at top right, #1a3a5c, transparent),
                        radial-gradient(circle at bottom left, #0a192f, #040d1a);
            overflow: hidden;
            position: relative;
        }

        /* إضافة دوائر ضوئية في الخلفية لمظهر عصري */
        body::before, body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: var(--primary-gold);
            filter: blur(150px);
            opacity: 0.05;
            z-index: 0;
        }
        body::before { top: -10%; right: -5%; }
        body::after { bottom: -10%; left: -5%; }

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 20px;
            animation: fadeInScale 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .glass-card {
            position: relative;
            background: var(--navy-surface);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border);
            border-radius: 28px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        @if (file_exists(public_path('images/brand/ravy-logo.png')))
        .glass-card::after {
            content: '';
            position: absolute;
            inset: auto -20% -35% auto;
            width: 55%;
            height: 55%;
            background: url("{{ asset('images/brand/ravy-logo.png') }}") no-repeat center / contain;
            opacity: 0.04;
            pointer-events: none;
        }
        @endif

        .brand-section {
            margin-bottom: 35px;
        }

        .brand-logo-img {
            width: 120px;
            height: auto;
            max-height: 120px;
            object-fit: contain;
            margin: 0 auto 15px;
            display: block;
            border-radius: 12px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.35);
        }

        .logo-placeholder {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--primary-gold), #8e7341);
            margin: 0 auto 15px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--navy-dark);
            font-family: 'Cinzel', serif;
            font-weight: bold;
            font-size: 24px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .brand-name {
            font-family: 'Cinzel', serif;
            color: var(--cream);
            font-size: 1.4rem;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .admin-badge {
            display: inline-block;
            padding: 4px 12px;
            background: rgba(197, 160, 89, 0.15);
            color: var(--primary-gold);
            border-radius: 8px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
            margin-top: 5px;
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h2 {
            color: var(--cream);
            font-size: 1.2rem;
            font-weight: 500;
            opacity: 0.9;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: right;
        }

        .input-group label {
            display: block;
            color: rgba(249, 246, 238, 0.6);
            font-size: 0.85rem;
            margin-bottom: 8px;
            margin-right: 5px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 45px 14px 15px;
            background: var(--input-bg);
            border: 1px solid var(--glass-border);
            border-radius: 14px;
            color: #fff;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: var(--primary-gold);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px rgba(197, 160, 89, 0.1);
        }

        .input-icon {
            position: absolute;
            right: 15px;
            color: var(--primary-gold);
            opacity: 0.7;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            margin-top: 10px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary-gold), #8e7341);
            color: var(--navy-dark);
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(197, 160, 89, 0.2);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(197, 160, 89, 0.3);
            filter: brightness(1.1);
        }

        .btn-login:active { transform: translateY(0); }

        .footer-links {
            margin-top: 25px;
            font-size: 0.85rem;
        }

        .footer-links a {
            color: rgba(249, 246, 238, 0.5);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover { color: var(--primary-gold); }

        .error-msg {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.2);
            color: #ff8a8a;
            padding: 12px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="glass-card">
            
            <div class="brand-section">
                @if (file_exists(public_path('images/brand/ravy-logo.png')))
                    <img src="{{ asset('images/brand/ravy-logo.png') }}" alt="{{ config('app.name', 'Ravy Boutique') }}" class="brand-logo-img" width="120" height="120">
                @else
                    <div class="logo-placeholder">RB</div>
                @endif
                <h1 class="brand-name">RAVY BOUTIQUE</h1>
                <span class="admin-badge">Admin Control Panel</span>
            </div>

            <div class="form-header">
                <h2>مرحباً بك، سجل دخولك</h2>
            </div>

            @if ($errors->any())
                <div class="error-msg">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.auth.login.submit') }}">
                @csrf

                <div class="input-group">
                    <label>البريد الإلكتروني</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="name@company.com" required autofocus>
                    </div>
                </div>

                <div class="input-group">
                    <label>كلمة المرور</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </span>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">دخول للوحة التحكم</button>
            </form>

           

        </div>
    </div>

</body>
</html>