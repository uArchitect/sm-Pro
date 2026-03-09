<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-PLW9XB0WC9"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-PLW9XB0WC9');
    </script>
    <title>Kayıt — Sipariş Masanda</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #0a0f1e; min-height: 100vh; }
        body::before {
            content:''; position:fixed; inset:0;
            background-image: linear-gradient(rgba(255,107,53,.04) 1px,transparent 1px), linear-gradient(90deg,rgba(255,107,53,.04) 1px,transparent 1px);
            background-size: 60px 60px; pointer-events:none;
        }
        .auth-card { background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08); border-radius:20px; backdrop-filter:blur(20px); box-shadow:0 24px 64px rgba(0,0,0,.4); }
        .logo-mark { width:52px; height:52px; border-radius:14px; background:linear-gradient(135deg,#FF6B35,#FF8C42); display:flex; align-items:center; justify-content:center; font-size:1.4rem; color:#fff; margin:0 auto 1rem; box-shadow:0 10px 32px rgba(255,107,53,.4); }
        .auth-title { font-size:1.35rem; font-weight:800; color:#fff; margin-bottom:.2rem; }
        .auth-sub { font-size:.85rem; color:rgba(255,255,255,.4); margin-bottom:1.5rem; }
        .form-label { font-size:.78rem; font-weight:600; color:rgba(255,255,255,.7); margin-bottom:.35rem; }
        .form-control { background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); color:#fff; border-radius:10px; padding:.55rem .9rem; font-size:.875rem; }
        .form-control::placeholder { color:rgba(255,255,255,.22); }
        .form-control:focus { background:rgba(255,255,255,.09); border-color:#FF6B35; box-shadow:0 0 0 3px rgba(255,107,53,.18); color:#fff; }
        .section-divider { font-size:.68rem; text-transform:uppercase; letter-spacing:.1em; color:rgba(255,255,255,.28); margin:1.1rem 0 .8rem; border-bottom:1px solid rgba(255,255,255,.07); padding-bottom:.5rem; font-weight:600; }
        .btn-register { background:linear-gradient(135deg,#FF6B35,#FF8C42); border:none; color:#fff; font-weight:700; padding:.7rem; border-radius:11px; box-shadow:0 8px 28px rgba(255,107,53,.4); transition:all .2s; font-size:.9rem; }
        .btn-register:hover { color:#fff; transform:translateY(-2px); box-shadow:0 12px 36px rgba(255,107,53,.55); }
        .auth-footer { font-size:.82rem; color:rgba(255,255,255,.35); text-align:center; margin-top:1.1rem; }
        .auth-footer a { color:#FF8C42; text-decoration:none; font-weight:600; }
        .invalid-feedback { color:#fca5a5; font-size:.78rem; }
        .is-invalid { border-color:rgba(239,68,68,.5)!important; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-5 px-3">
<div style="width:100%;max-width:480px;position:relative;z-index:1">
    <div class="text-center">
        <div class="logo-mark"><i class="bi bi-qr-code-scan"></i></div>
        <div class="auth-title">Sipariş Masanda</div>
        <div class="auth-sub">Yeni işletme hesabı oluşturun</div>
    </div>

    <div class="auth-card p-4">
        @if($errors->any())
            <div class="mb-3 py-2 px-3" style="background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);border-radius:10px;">
                @foreach($errors->all() as $error)
                    <div class="small" style="color:#fca5a5"><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <div class="section-divider">Restoran Bilgileri</div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Restoran Adı</label>
                <input type="text" name="restoran_adi" class="form-control @error('restoran_adi') is-invalid @enderror"
                       value="{{ old('restoran_adi') }}" placeholder="Örn: Lezzet Dünyası" required>
                @error('restoran_adi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="section-divider">Hesap Bilgileri</div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Ad Soyad</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="Adınız Soyadınız" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">E-posta</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="ornek@email.com" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Şifre</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="En az 8 karakter" required minlength="8">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small">Şifre Tekrar</label>
                <input type="password" name="password_confirmation" class="form-control"
                       placeholder="Şifreyi tekrar girin" required>
            </div>

            <button type="submit" class="btn btn-register w-100">
                <i class="bi bi-rocket-takeoff me-2"></i>Hesap Oluştur
            </button>
        </form>
    </div>

    <p class="auth-footer mt-3">
        Zaten hesabınız var mı? <a href="{{ route('login') }}">Giriş yapın</a>
    </p>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
