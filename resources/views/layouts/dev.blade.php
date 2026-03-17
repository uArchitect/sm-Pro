<!DOCTYPE html>
<html lang="tr">
<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-T58K7CMS');</script>
    <!-- End Google Tag Manager -->
    <!-- Google tag (gtag.js) -->
    <script src="https://www.googletagmanager.com/gtag/js?id=G-B4H9LNYM0C"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-PLW9XB0WC9');
    gtag('config', 'G-B4H9LNYM0C');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>@yield('title', 'Developer') — Sipariş Masanda</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --sidebar-width: 240px;
            --topbar-h: 58px;
            --dev-accent: #ef4444;
            --dev-dark: #0f0f10;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: #f4f5f7; color: #111827; }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-width);
            background: var(--dev-dark);
            display: flex; flex-direction: column;
            z-index: 200;
        }
        .sidebar-brand {
            padding: 1.1rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.06);
            display: flex; align-items: center; gap: .75rem; flex-shrink: 0;
        }
        .logo {
            width: 36px; height: 36px; border-radius: 10px;
            background: var(--dev-accent);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1rem;
        }
        .brand-text .name { font-weight: 800; font-size: .9rem; color: #fff; line-height: 1.2; }
        .brand-text .sub  { font-size: .68rem; color: rgba(255,255,255,.35); margin-top: .1rem; }

        .sidebar-section {
            padding: .85rem 1.25rem .25rem;
            font-size: .62rem; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: rgba(255,255,255,.22);
        }
        .nav-item-link {
            display: flex; align-items: center; gap: .65rem;
            padding: .55rem 1.1rem; margin: .1rem .6rem;
            border-radius: 8px; color: rgba(255,255,255,.55);
            text-decoration: none; font-size: .83rem; font-weight: 500;
            transition: background .15s, color .15s;
        }
        .nav-item-link:hover { background: rgba(255,255,255,.06); color: rgba(255,255,255,.85); }
        .nav-item-link.active { background: rgba(239,68,68,.18); color: #fca5a5; }
        .nav-item-link.active i { color: var(--dev-accent); }

        .sidebar-footer {
            padding: .9rem 1rem; border-top: 1px solid rgba(255,255,255,.06); flex-shrink: 0;
        }
        .user-card { display: flex; align-items: center; gap: .65rem; }
        .user-avatar {
            width: 32px; height: 32px; border-radius: 8px;
            background: var(--dev-accent); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: .78rem; font-weight: 700; flex-shrink: 0;
        }
        .user-name { font-size: .8rem; font-weight: 600; color: #fff; }
        .role-badge {
            font-size: .65rem; font-weight: 700; border-radius: 4px;
            padding: .1rem .4rem; background: rgba(239,68,68,.2); color: #fca5a5;
        }
        .btn-logout {
            background: none; border: none; padding: .25rem;
            color: rgba(255,255,255,.35); cursor: pointer; transition: color .15s;
        }
        .btn-logout:hover { color: var(--dev-accent); }

        /* ── Main ── */
        .main-wrap { margin-left: var(--sidebar-width); min-height: 100vh; display: flex; flex-direction: column; }
        .topbar {
            height: var(--topbar-h); background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex; align-items: center; padding: 0 1.75rem; gap: 1rem;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 1px 8px rgba(0,0,0,.06);
        }
        .topbar-title { font-size: .95rem; font-weight: 700; color: #111827; }
        .dev-badge {
            font-size: .68rem; font-weight: 700; border-radius: 5px;
            padding: .2rem .5rem; background: rgba(239,68,68,.1); color: #dc2626;
            border: 1px solid rgba(239,68,68,.2);
        }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: .75rem; }
        .topbar-date {
            font-size: .78rem; color: #9ca3af;
            background: #f9fafb; border: 1px solid #e5e7eb;
            padding: .3rem .75rem; border-radius: 8px;
        }
        .content-area { padding: 1.75rem; flex: 1; }

        /* Cards / Tables (reuse same system) */
        .sm-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; box-shadow:0 1px 6px rgba(0,0,0,.04); }
        .sm-card-header { padding:1rem 1.25rem; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; gap:.5rem; font-weight:600; font-size:.9rem; color:#374151; }
        .sm-card-body   { padding:1.25rem; }
        .sm-table { font-size:.855rem; }
        .sm-table th { font-weight:600; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; color:#6b7280; background:#f9fafb; }
        .sm-table td { vertical-align:middle; color:#374151; }
        .btn { font-weight:500; border-radius:9px; font-size:.855rem; }
        .btn-sm { padding:.35rem .75rem; border-radius:7px; }
        .btn-accent { background:linear-gradient(135deg,#ef4444,#f87171); border:none; color:#fff; box-shadow:0 4px 14px rgba(239,68,68,.3); }
        .btn-accent:hover { color:#fff; box-shadow:0 6px 20px rgba(239,68,68,.45); transform:translateY(-1px); }
        .form-control,.form-select { border-radius:9px; border-color:#e5e7eb; font-size:.875rem; padding:.6rem .95rem; color:#111827; }
        .form-control:focus,.form-select:focus { border-color:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,.12); }
        .form-label { font-weight:600; font-size:.8rem; color:#374151; margin-bottom:.4rem; }
        .alert { border:none; border-radius:10px; font-size:.875rem; }
        .alert-success { background:#f0fdf4; color:#166534; }
        .alert-danger  { background:#fef2f2; color:#991b1b; }
        ::-webkit-scrollbar { width:4px; }
        ::-webkit-scrollbar-thumb { background:rgba(0,0,0,.12); border-radius:4px; }
    </style>
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T58K7CMS"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
@php $devUser = auth()->user(); @endphp

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="logo"><i class="bi bi-terminal-fill"></i></div>
        <div class="brand-text">
            <div class="name">Developer Panel</div>
            <div class="sub">Sipariş Masanda</div>
        </div>
    </div>

    <nav class="flex-grow-1 py-1 overflow-auto">
        <div class="sidebar-section">Platform</div>
        <a href="{{ route('developer.index') }}" class="nav-item-link {{ request()->routeIs('developer.index') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Genel Bakış
        </a>
        <a href="{{ route('developer.users') }}" class="nav-item-link {{ request()->routeIs('developer.users') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Tüm Kullanıcılar
        </a>
        <a href="{{ route('developer.tickets') }}" class="nav-item-link {{ request()->routeIs('developer.tickets*') ? 'active' : '' }}">
            <i class="bi bi-headset"></i> Destek Mesajları
        </a>
        <a href="{{ route('developer.blog.index') }}" class="nav-item-link {{ request()->routeIs('developer.blog*') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i> Blog
        </a>

        <div class="sidebar-section">Sistem</div>
        <a href="{{ route('developer.migrations') }}" class="nav-item-link {{ request()->routeIs('developer.migrations') ? 'active' : '' }}">
            <i class="bi bi-database-gear"></i> Migration
        </a>
        <a href="{{ route('developer.settings') }}" class="nav-item-link {{ request()->routeIs('developer.settings') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Ayarlar
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ mb_strtoupper(mb_substr($devUser->name ?? 'D', 0, 1, 'UTF-8'), 'UTF-8') }}</div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="user-name text-truncate">{{ $devUser->name }}</div>
                <span class="role-badge">developer</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout" title="Çıkış Yap">
                    <i class="bi bi-box-arrow-right fs-6"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<div class="main-wrap">
    <header class="topbar">
        <div class="topbar-title">@yield('page-title', 'Developer')</div>
        <span class="dev-badge"><i class="bi bi-shield-fill-check me-1"></i>DEV</span>
        <div class="topbar-right">
            <span class="topbar-date"><i class="bi bi-calendar3 me-1"></i>{{ now()->locale(app()->getLocale())->isoFormat('D MMM YYYY') }}</span>
        </div>
    </header>

    <main class="content-area">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{!! nl2br(e(session('success'))) !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <ul class="mb-0 ps-2">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
