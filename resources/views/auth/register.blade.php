@php $isTr = app()->getLocale() === 'tr'; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @if(config('services.google.gtm_id'))
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ config('services.google.gtm_id') }}');</script>
    <!-- End Google Tag Manager -->
    @endif
    @if(config('services.google.ga_id'))
    <!-- Google tag (gtag.js) -->
    <script src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.ga_id') }}"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    @if(config('services.google.ga_id_secondary'))
    gtag('config', '{{ config('services.google.ga_id_secondary') }}');
    @endif
    gtag('config', '{{ config('services.google.ga_id') }}');
    </script>
    @endif
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon-indigo.svg') }}">

    <title>{{ $isTr ? 'Kayıt — Sipariş Masanda' : 'Register — Siparis Masanda' }}</title>
    <meta name="description" content="{{ $isTr ? 'Sipariş Masanda ile restoranınızı dakikalar içinde dijitale taşıyın. Ücretsiz kayıt olun.' : 'Take your restaurant digital in minutes with Siparis Masanda. Register for free.' }}">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="theme-color" content="#ffffff">
    <link rel="canonical" href="{{ route('register') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('register') }}">
    <meta property="og:title" content="{{ $isTr ? 'Kayıt — Sipariş Masanda' : 'Register — Siparis Masanda' }}">
    <meta property="og:description" content="{{ $isTr ? 'Restoranınız için QR menü ve modern sipariş deneyimi.' : 'QR menu and modern ordering for your restaurant.' }}">
    <meta property="og:site_name" content="Sipariş Masanda">
    <meta property="og:image" content="{{ asset('og-cover.svg') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $isTr ? 'Kayıt — Sipariş Masanda' : 'Register — Siparis Masanda' }}">
    <meta name="twitter:description" content="{{ $isTr ? 'Restoranınız için ücretsiz dijital menü.' : 'Free digital menu for your restaurant.' }}">
    <meta name="twitter:image" content="{{ asset('og-cover.svg') }}">
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebPage",
        "name": "{{ $isTr ? 'Ücretsiz Kayıt — Sipariş Masanda' : 'Free Registration — Siparis Masanda' }}",
        "url": "{{ route('register') }}",
        "description": "{{ $isTr ? 'Ücretsiz dijital QR menü hesabınızı oluşturun' : 'Create your free digital QR menu account' }}",
        "isPartOf": { "@@id": "{{ url('/') }}/#website" }
    }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f8fafc; min-height: 100vh; }
        .auth-card { background:#fff; border:1px solid #e2e8f0; border-radius:20px; box-shadow:0 4px 24px rgba(0,0,0,.06); }
        .logo-mark { width:52px; height:52px; border-radius:14px; background:linear-gradient(135deg,#4F46E5,#6366F1); display:flex; align-items:center; justify-content:center; font-size:1.4rem; color:#fff; margin:0 auto 1rem; box-shadow:0 10px 32px rgba(79,70,229,.25); }
        .auth-title { font-size:1.35rem; font-weight:800; color:#0f172a; margin-bottom:.2rem; }
        .auth-sub { font-size:.85rem; color:#94a3b8; margin-bottom:1.5rem; }
        .form-label { font-size:.78rem; font-weight:600; color:#475569; margin-bottom:.35rem; }
        .form-control { background:#f8fafc; border:1.5px solid #e2e8f0; color:#0f172a; border-radius:10px; padding:.55rem .9rem; font-size:.875rem; }
        .form-control::placeholder { color:#c0c9d4; }
        .form-control:focus { background:#fff; border-color:#4F46E5; box-shadow:0 0 0 3px rgba(79,70,229,.1); color:#0f172a; }
        .section-divider { font-size:.68rem; text-transform:uppercase; letter-spacing:.1em; color:#94a3b8; margin:1.1rem 0 .8rem; border-bottom:1px solid #f1f5f9; padding-bottom:.5rem; font-weight:600; }
        .btn-register { background:linear-gradient(135deg,#4F46E5,#6366F1); border:none; color:#fff; font-weight:700; padding:.7rem; border-radius:11px; box-shadow:0 8px 28px rgba(79,70,229,.3); transition:all .2s; font-size:.9rem; }
        .btn-register:hover { color:#fff; transform:translateY(-2px); box-shadow:0 12px 36px rgba(79,70,229,.4); }
        .auth-footer { font-size:.82rem; color:#94a3b8; text-align:center; margin-top:1.1rem; }
        .auth-footer a { color:#4F46E5; text-decoration:none; font-weight:600; }
        .invalid-feedback { color:#ef4444; font-size:.78rem; }
        .is-invalid { border-color:#ef4444!important; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-5 px-3">
    @if(config('services.google.gtm_id'))
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ config('services.google.gtm_id') }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    @endif
<div style="width:100%;max-width:480px;position:relative;z-index:1">
    <div class="text-center">
        <div style="display:inline-flex;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;background:#fff;margin-bottom:1rem">
            <a href="{{ request()->fullUrlWithQuery(['lang' => 'tr']) }}" style="padding:.3rem .6rem;font-size:.72rem;font-weight:700;letter-spacing:.04em;text-decoration:none;transition:all .15s;line-height:1;{{ $isTr ? 'background:linear-gradient(135deg,#4F46E5,#6366F1);color:#fff;' : 'color:#94a3b8;' }}">TR</a>
            <span style="width:1px;background:#e2e8f0"></span>
            <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}" style="padding:.3rem .6rem;font-size:.72rem;font-weight:700;letter-spacing:.04em;text-decoration:none;transition:all .15s;line-height:1;{{ !$isTr ? 'background:linear-gradient(135deg,#4F46E5,#6366F1);color:#fff;' : 'color:#94a3b8;' }}">EN</a>
        </div>
        <div class="logo-mark"><i class="bi bi-qr-code-scan"></i></div>
        <div class="auth-title">{{ __('common.app_name') }}</div>
        <div class="auth-sub">{{ __('auth.register_sub') }}</div>
    </div>

    <div class="auth-card p-4">
        @if($errors->any())
            <div class="mb-3 py-2 px-3" style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;">
                @foreach($errors->all() as $error)
                    <div class="small" style="color:#dc2626"><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <div class="section-divider">{{ __('auth.company_info') }}</div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">{{ __('auth.restaurant_name') }}</label>
                <input type="text" name="restoran_adi" class="form-control @error('restoran_adi') is-invalid @enderror"
                       value="{{ old('restoran_adi') }}" placeholder="{{ __('auth.restaurant_placeholder') }}" required>
                @error('restoran_adi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="section-divider">{{ __('auth.account_info') }}</div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">{{ __('auth.full_name') }}</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="{{ __('auth.full_name_placeholder') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">{{ __('auth.email') }}</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="{{ __('auth.email_placeholder') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">{{ __('auth.password') }}</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="{{ __('auth.password_min') }}" required minlength="8">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small">{{ __('auth.password_confirm') }}</label>
                <input type="password" name="password_confirmation" class="form-control"
                       placeholder="{{ __('auth.password_confirm_placeholder') }}" required>
            </div>

            <button type="submit" class="btn btn-register w-100">
                <i class="bi bi-rocket-takeoff me-2"></i>{{ __('auth.create_btn') }}
            </button>
        </form>
    </div>

    <p class="auth-footer mt-3">
        {{ __('auth.have_account') }} <a href="{{ route('login') }}">{{ __('auth.sign_in') }}</a>
    </p>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
