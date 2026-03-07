<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('common.panel')) — {{ __('common.app_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    @stack('styles')
    <style>
        :root {
            --accent:        #FF6B35;
            --accent-2:      #FF8C42;
            --accent-glow:   rgba(255,107,53,.18);
            --accent-soft:   rgba(255,107,53,.09);
            --sidebar-bg:    #0d1117;
            --sidebar-w:     260px;
            --topbar-h:      62px;
            --radius-card:   16px;
            --shadow-card:   0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.06);
            --border:        #eaecf0;
            --text-primary:  #101828;
            --text-secondary:#475467;
            --text-muted:    #98a2b3;
            --bg-page:       #f7f8fa;
        }
        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-page); color: var(--text-primary); margin: 0; }

        /* ═══════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════ */
        .sidebar {
            position: fixed; inset: 0 auto 0 0;
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            display: flex; flex-direction: column;
            z-index: 200;
            border-right: 1px solid rgba(255,255,255,.04);
        }
        /* Decorative top gradient strip */
        .sidebar::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, var(--accent), var(--accent-2), #FFB347);
        }

        /* Brand area */
        .sb-brand {
            padding: 1.2rem 1.1rem 1rem;
            display: flex; align-items: center; gap: .8rem;
            border-bottom: 1px solid rgba(255,255,255,.05);
        }
        .sb-logo {
            width: 40px; height: 40px; border-radius: 11px; flex-shrink: 0;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; color: #fff;
            box-shadow: 0 6px 20px rgba(255,107,53,.45);
        }
        .sb-brand-text { line-height: 1; min-width: 0; }
        .sb-name { font-size: .92rem; font-weight: 800; color: #fff; letter-spacing: -.01em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sb-tenant { font-size: .7rem; color: rgba(255,255,255,.35); margin-top: .22rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* Nav */
        .sb-nav { flex: 1; overflow-y: auto; padding: .6rem 0; }
        .sb-nav::-webkit-scrollbar { width: 3px; }
        .sb-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.08); border-radius: 3px; }

        .sb-section {
            padding: .9rem 1.1rem .3rem;
            font-size: .62rem; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: rgba(255,255,255,.22);
        }

        .sb-link {
            display: flex; align-items: center; gap: .7rem;
            margin: 1px .6rem; padding: .58rem .85rem;
            border-radius: 9px;
            color: rgba(255,255,255,.5);
            font-size: .845rem; font-weight: 500;
            text-decoration: none;
            transition: background .14s, color .14s;
            position: relative;
        }
        .sb-link i { font-size: .95rem; width: 17px; text-align: center; flex-shrink: 0; transition: color .14s; }
        .sb-link:hover { background: rgba(255,255,255,.06); color: rgba(255,255,255,.88); }
        .sb-link:hover i { color: rgba(255,255,255,.88); }

        .sb-link.active {
            background: rgba(255,107,53,.12);
            color: #FF7A48;
            border: 1px solid rgba(255,107,53,.18);
        }
        .sb-link.active i { color: var(--accent); }
        .sb-link.active::before {
            content: '';
            position: absolute; left: -0.6rem; top: 20%; bottom: 20%;
            width: 3px; background: var(--accent);
            border-radius: 0 3px 3px 0;
        }

        /* QR link special */
        .sb-link.qr-link {
            background: rgba(255,107,53,.07);
            border: 1px dashed rgba(255,107,53,.25);
            color: rgba(255,150,80,.8);
            margin-top: .3rem;
        }
        .sb-link.qr-link:hover, .sb-link.qr-link.active {
            background: rgba(255,107,53,.14);
            border-color: rgba(255,107,53,.4);
            color: #FF7A48;
        }
        .sb-link.qr-link i { color: var(--accent); }

        /* Sidebar footer / user card */
        .sb-footer {
            padding: .9rem 1rem;
            border-top: 1px solid rgba(255,255,255,.05);
        }
        .sb-user {
            display: flex; align-items: center; gap: .7rem;
            padding: .55rem .75rem;
            border-radius: 10px;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.06);
        }
        .sb-avatar {
            width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
            background: linear-gradient(135deg, #6C5CE7, #a29bfe);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: .8rem; color: #fff;
        }
        .sb-user-name { font-size: .8rem; font-weight: 600; color: #fff; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sb-role {
            display: inline-block; font-size: .6rem; font-weight: 700;
            padding: .12rem .4rem; border-radius: 4px;
            text-transform: uppercase; letter-spacing: .05em; margin-top: .15rem;
        }
        .role-owner   { background: rgba(255,107,53,.18); color: #FF7A48; }
        .role-admin   { background: rgba(108,92,231,.2);  color: #a29bfe; }
        .role-personel{ background: rgba(255,255,255,.07); color: rgba(255,255,255,.45); }
        .sb-logout {
            background: none; border: none; padding: .3rem .4rem;
            color: rgba(255,255,255,.22); cursor: pointer;
            border-radius: 7px; transition: all .15s; line-height: 1;
        }
        .sb-logout:hover { color: #FF6B35; background: rgba(255,107,53,.1); }

        /* ═══════════════════════════════════════
           MAIN WRAP
        ═══════════════════════════════════════ */
        .main-wrap {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* ═══════════════════════════════════════
           TOPBAR
        ═══════════════════════════════════════ */
        .topbar {
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 1.75rem; gap: .85rem;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 1px 0 var(--border), 0 2px 12px rgba(0,0,0,.04);
        }
        .topbar-mob {
            display: none; background: none; border: 1px solid var(--border);
            border-radius: 8px; padding: .3rem .5rem; cursor: pointer;
            color: var(--text-secondary); font-size: 1.2rem; line-height: 1;
            transition: all .14s;
        }
        .topbar-mob:hover { background: #f3f4f6; }

        .topbar-titles { flex: 1; min-width: 0; }
        .topbar-page {
            font-size: 1rem; font-weight: 700; color: var(--text-primary);
            letter-spacing: -.015em; line-height: 1.2;
        }
        .topbar-bc {
            font-size: .73rem; color: var(--text-muted); margin-top: .05rem;
        }

        .topbar-right { display: flex; align-items: center; gap: .65rem; }
        .topbar-chip {
            display: flex; align-items: center; gap: .45rem;
            font-size: .75rem; color: var(--text-secondary); font-weight: 500;
            background: var(--bg-page); border: 1px solid var(--border);
            padding: .32rem .75rem; border-radius: 8px;
        }
        .topbar-chip i { color: var(--text-muted); }

        /* Language dropdown in topbar */
        .topbar-lang-select {
            font-size: .75rem; font-weight: 500; color: var(--text-secondary);
            border: none; background: transparent; cursor: pointer;
            padding: 0 .2rem; min-width: 5rem;
            font-family: inherit;
        }
        .topbar-lang-select:focus { outline: none; }

        /* User pill in topbar */
        .topbar-user {
            display: flex; align-items: center; gap: .5rem;
            background: var(--bg-page); border: 1px solid var(--border);
            padding: .28rem .65rem .28rem .35rem;
            border-radius: 10px; font-size: .78rem;
            color: var(--text-secondary); font-weight: 500;
        }
        .topbar-avatar {
            width: 26px; height: 26px; border-radius: 7px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            display: flex; align-items: center; justify-content: center;
            font-size: .68rem; font-weight: 800; color: #fff;
        }

        /* ═══════════════════════════════════════
           CONTENT
        ═══════════════════════════════════════ */
        .content-area { padding: 1.75rem; flex: 1; }

        /* ═══════════════════════════════════════
           CARDS
        ═══════════════════════════════════════ */
        .sm-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-card);
        }
        .sm-card-header {
            padding: 1rem 1.35rem;
            border-bottom: 1px solid #f2f4f7;
            display: flex; align-items: center; gap: .55rem;
            font-weight: 700; font-size: .875rem; color: var(--text-primary);
        }
        .sm-card-header i { font-size: 1rem; }
        .sm-card-body { padding: 1.35rem; }

        /* Stat card variant */
        .stat-card {
            background: #fff; border: 1px solid var(--border);
            border-radius: var(--radius-card); box-shadow: var(--shadow-card);
            padding: 1.25rem 1.4rem;
            display: flex; align-items: center; gap: 1rem;
            transition: box-shadow .18s, transform .18s;
        }
        .stat-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,.1); transform: translateY(-2px); }
        .stat-icon {
            width: 50px; height: 50px; border-radius: 13px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem;
        }
        .stat-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--text-muted); margin-bottom: .2rem; }
        .stat-value { font-size: 1.85rem; font-weight: 900; color: var(--text-primary); line-height: 1; letter-spacing: -.03em; }

        /* ═══════════════════════════════════════
           ALERTS
        ═══════════════════════════════════════ */
        .alert { border: none; border-radius: 12px; font-size: .875rem; font-weight: 500; }
        .alert-success {
            background: #f0fdf4; color: #166534;
            border-left: 3px solid #22c55e;
        }
        .alert-danger {
            background: #fef2f2; color: #991b1b;
            border-left: 3px solid #ef4444;
        }

        /* ═══════════════════════════════════════
           TABLE
        ═══════════════════════════════════════ */
        .sm-table { font-size: .845rem; }
        .sm-table thead th {
            font-weight: 700; font-size: .72rem; text-transform: uppercase;
            letter-spacing: .06em; color: var(--text-muted);
            background: #f9fafb; border-bottom: 1px solid var(--border);
            padding: .75rem 1rem; white-space: nowrap;
        }
        .sm-table td { vertical-align: middle; color: var(--text-secondary); padding: .85rem 1rem; border-bottom: 1px solid #f2f4f7; }
        .sm-table tbody tr { transition: background .12s; }
        .sm-table tbody tr:hover td { background: #fafbfc; }
        .sm-table tbody tr:last-child td { border-bottom: none; }

        /* ═══════════════════════════════════════
           BUTTONS
        ═══════════════════════════════════════ */
        .btn { font-weight: 600; border-radius: 9px; font-size: .845rem; transition: all .15s; }
        .btn-accent {
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            border: none; color: #fff;
            box-shadow: 0 2px 10px rgba(255,107,53,.28);
        }
        .btn-accent:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(255,107,53,.42); }
        .btn-accent:active { transform: translateY(0); }
        .btn-sm { padding: .35rem .8rem; border-radius: 7px; font-size: .8rem; }
        .btn-outline-secondary { border-color: var(--border); color: var(--text-secondary); }
        .btn-outline-secondary:hover { background: #f9fafb; border-color: #d0d5dd; color: var(--text-primary); }

        /* ═══════════════════════════════════════
           FORMS
        ═══════════════════════════════════════ */
        .form-control, .form-select {
            border-radius: 9px; border: 1.5px solid #e5e7eb;
            font-size: .875rem; padding: .58rem .95rem;
            background: #fff; color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            transition: border-color .15s, box-shadow .15s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent); box-shadow: 0 0 0 3px rgba(255,107,53,.12);
            background: #fff; color: var(--text-primary);
        }
        .form-control::placeholder { color: #c0c7d0; }
        textarea.form-control { resize: vertical; min-height: 80px; }
        .form-label { font-weight: 600; font-size: .8rem; color: #374151; margin-bottom: .45rem; }
        .form-text { font-size: .75rem; color: var(--text-muted); margin-top: .3rem; }
        .invalid-feedback { font-size: .77rem; }
        .input-group-text {
            background: #f9fafb; border: 1.5px solid #e5e7eb;
            color: var(--text-muted); font-size: .875rem; border-radius: 9px 0 0 9px;
        }
        .input-group .form-control { border-radius: 0 9px 9px 0; }
        .form-select { background-position: right .85rem center; padding-right: 2.5rem; }

        /* ═══════════════════════════════════════
           BADGE
        ═══════════════════════════════════════ */
        .status-badge {
            display: inline-flex; align-items: center; gap: .3rem;
            font-size: .72rem; font-weight: 700; padding: .22rem .6rem;
            border-radius: 999px; letter-spacing: .02em;
        }

        /* ═══════════════════════════════════════
           DATATABLES OVERRIDE
        ═══════════════════════════════════════ */
        .dataTables_wrapper .dataTables_info { font-size:.78rem; color:var(--text-muted); padding-top:.75rem; }
        .dataTables_wrapper .dataTables_paginate { padding-top:.5rem; }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            font-size:.8rem !important; border-radius:6px !important; padding:.3rem .65rem !important;
            border:1px solid var(--border) !important; margin:0 2px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background:var(--accent) !important; color:#fff !important; border-color:var(--accent) !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current):not(.disabled) {
            background:rgba(255,107,53,.08) !important; color:var(--accent) !important; border-color:rgba(255,107,53,.2) !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            color:var(--text-muted) !important; opacity:.4; cursor:default;
        }
        table.dataTable { border-collapse:collapse !important; }

        /* ═══════════════════════════════════════
           SCROLLBAR
        ═══════════════════════════════════════ */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,.1); border-radius: 5px; }

        /* ═══════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════ */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform .25s cubic-bezier(.4,0,.2,1); z-index: 1050; }
            .sidebar.open { transform: translateX(0); }
            .main-wrap { margin-left: 0; }
            .topbar-mob { display: block; }
            .sb-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 1049; backdrop-filter: blur(2px); }
            .sb-backdrop.show { display: block; }
            .topbar-chip { display: none; }
        }
        @media (min-width: 769px) {
            .sb-backdrop { display: none !important; }
        }
    </style>
</head>
<body>

@php
    $tenant  = DB::table('tenants')->find(session('tenant_id'));
    $user    = auth()->user();
    $initials = strtoupper(substr($user->name ?? 'U', 0, 2));
@endphp

{{-- ══ SIDEBAR ══ --}}
<aside class="sidebar" id="sidebar">

    <div class="sb-brand">
        <div class="sb-logo"><i class="bi bi-qr-code-scan"></i></div>
        <div class="sb-brand-text">
            <div class="sb-name">{{ __('common.app_name') }}</div>
            <div class="sb-tenant">{{ $tenant->restoran_adi ?? __('common.management') }}</div>
        </div>
    </div>

    <nav class="sb-nav">
        <a href="{{ route('dashboard') }}" class="sb-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> {{ __('nav.nav.dashboard') }}
        </a>

        @if($user->role === 'owner')
        <div class="sb-section">{{ __('nav.nav.company') }}</div>
        <a href="{{ route('company.edit') }}" class="sb-link {{ request()->routeIs('company.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i> {{ __('nav.nav.company_info') }}
        </a>
        <a href="{{ route('users.index') }}" class="sb-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> {{ __('nav.nav.staff') }}
        </a>
        @endif

        <div class="sb-section">{{ __('nav.nav.digital_menu') }}</div>
        <a href="{{ route('categories.index') }}" class="sb-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap"></i> {{ __('nav.nav.categories') }}
        </a>
        <a href="{{ route('products.index') }}" class="sb-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> {{ __('nav.nav.products') }}
        </a>

        <div class="sb-section">{{ __('nav.nav.qr_link') }}</div>
        <a href="{{ route('menu.qr') }}" class="sb-link qr-link {{ request()->routeIs('menu.qr') ? 'active' : '' }}">
            <i class="bi bi-qr-code"></i> {{ __('nav.nav.menu_qr') }}
        </a>
    </nav>

    <div class="sb-footer">
        <div class="sb-user">
            <div class="sb-avatar">{{ $initials }}</div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="sb-user-name">{{ $user->name }}</div>
                <div><span class="sb-role role-{{ $user->role }}">{{ __('nav.roles.' . $user->role) }}</span></div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sb-logout" title="{{ __('common.logout') }}">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<div class="sb-backdrop" id="sbBackdrop" onclick="closeSb()"></div>

{{-- ══ MAIN ══ --}}
<div class="main-wrap">

    <header class="topbar">
        <button class="topbar-mob" onclick="toggleSb()"><i class="bi bi-list"></i></button>

        <div class="topbar-titles">
            <div class="topbar-page">@yield('page-title', __('nav.nav.dashboard'))</div>
            @hasSection('breadcrumb')
            <div class="topbar-bc">@yield('breadcrumb')</div>
            @endif
        </div>

        <div class="topbar-right">
            <form method="POST" action="{{ route('locale.switch') }}" class="d-inline" id="localeForm">
                @csrf
                <input type="hidden" name="redirect" value="{{ url()->current() }}">
                <label class="topbar-chip mb-0 d-flex align-items-center gap-2 pe-2" style="cursor:pointer" for="localeSelect">
                    <i class="bi bi-translate"></i>
                    <select id="localeSelect" name="locale" class="topbar-lang-select" onchange="this.form.submit()" aria-label="{{ __('common.language') }}">
                        @foreach(config('app.available_locales', ['en' => 'English', 'tr' => 'Türkçe']) as $code => $label)
                        <option value="{{ $code }}" {{ app()->getLocale() === $code ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </form>
            <div class="topbar-chip">
                <i class="bi bi-calendar3"></i>
                {{ now()->locale(app()->getLocale())->isoFormat('D MMM YYYY') }}
            </div>
            <div class="topbar-user">
                <div class="topbar-avatar">{{ $initials }}</div>
                <span>{{ explode(' ', $user->name)[0] }}</span>
            </div>
        </div>
    </header>

    <main class="content-area">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
                <ul class="mb-0 ps-2 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
function toggleSb() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sbBackdrop').classList.toggle('show');
}
function closeSb() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sbBackdrop').classList.remove('show');
}
document.querySelectorAll('.sb-link').forEach(el =>
    el.addEventListener('click', () => { if (window.innerWidth <= 768) closeSb(); })
);
</script>
@stack('scripts')
</body>
</html>
