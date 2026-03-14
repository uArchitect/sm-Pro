@php
    $locale = app()->getLocale();
    $menuTitle = $tenant->restoran_adi . ' — ' . __('public.menu_suffix');
    $menuDescription = __('public.menu_description', ['restaurant' => $tenant->restoran_adi]);
    $menuCanonical = request()->fullUrlWithoutQuery(['lang', 'preview']);
    $menuCurrentUrl = $menuCanonical . ($locale === config('app.fallback_locale', 'tr') ? '' : '?lang=' . $locale);
    $menuShareImage = $tenant->logo ? asset('uploads/' . $tenant->logo) : asset('og-cover.svg');
    $allowDesktopPreview = request()->boolean('preview');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}">
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>{{ $menuTitle }}</title>
    <meta name="description" content="{{ $menuDescription }}">
    <meta name="robots" content="{{ isset($isDemoMenu) && $isDemoMenu ? 'index, follow, max-image-preview:large' : 'noindex, follow, max-image-preview:large' }}">
    <link rel="canonical" href="{{ $menuCurrentUrl }}">
    <link rel="alternate" hreflang="tr" href="{{ $menuCanonical }}?lang=tr">
    <link rel="alternate" hreflang="en" href="{{ $menuCanonical }}?lang=en">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $menuTitle }}">
    <meta property="og:description" content="{{ $menuDescription }}">
    <meta property="og:url" content="{{ $menuCurrentUrl }}">
    <meta property="og:image" content="{{ $menuShareImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $menuTitle }}">
    <meta name="twitter:description" content="{{ $menuDescription }}">
    <meta name="twitter:image" content="{{ $menuShareImage }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --accent: #FF6B35;
            --accent2: #FF8C42;
            --accent-soft: rgba(255, 107, 53, 0.08);
            --accent-soft-2: rgba(255, 107, 53, 0.12);
            --dark: #0f172a;
            --dark2: #1e293b;
            --text: #1e293b;
            --text2: #475569;
            --text3: #94a3b8;
            --border: #e2e8f0;
            --border-light: #f1f5f9;
            --bg: #f8fafc;
            --card: #ffffff;
            --star: #f59e0b;
            --radius: 16px;
            --radius-sm: 12px;
            --radius-pill: 999px;
            --shadow: 0 1px 3px rgba(0,0,0,.06);
            --shadow-md: 0 4px 12px rgba(0,0,0,.08);
            --safe-b: env(safe-area-inset-bottom, 0px);
        }
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { background: var(--bg); color: var(--text); -webkit-font-smoothing: antialiased; min-height: 100vh; margin: 0; }
        .menu-app { display: flex; flex-direction: column; min-height: 100vh; min-height: 100dvh; }
        img { display: block; max-width: 100%; height: auto; object-fit: cover; }

        /* ========== Header ========== */
        .hdr {
            background: linear-gradient(160deg, var(--dark) 0%, var(--dark2) 100%);
            position: relative; overflow: hidden;
            padding: 1.75rem 1.25rem 1.5rem;
            text-align: center;
        }
        .hdr::after {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(255,107,53,.12) 0%, transparent 55%);
            pointer-events: none;
        }
        .hdr-content { position: relative; z-index: 1; }
        .hdr-logo {
            width: 80px; height: 80px;
            border-radius: 20px;
            object-fit: cover;
            border: 3px solid rgba(255,255,255,.12);
            margin: 0 auto .85rem;
            box-shadow: 0 12px 40px rgba(0,0,0,.25);
        }
        .hdr-logo-fallback {
            width: 80px; height: 80px;
            border-radius: 20px;
            margin: 0 auto .85rem;
            background: linear-gradient(145deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: #fff;
            box-shadow: 0 12px 40px rgba(255,107,53,.35);
            border: 2px solid rgba(255,255,255,.1);
        }
        .hdr-name { font-size: 1.5rem; font-weight: 800; color: #fff; letter-spacing: -.02em; line-height: 1.2; margin: 0; }
        .hdr-sub { font-size: .7rem; color: rgba(255,255,255,.4); margin-top: .2rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; }
        .hdr-meta { display: flex; flex-wrap: wrap; justify-content: center; gap: .5rem .85rem; margin-top: .75rem; }
        .hdr-meta-item {
            display: inline-flex; align-items: center; gap: .3rem;
            font-size: .75rem; color: rgba(255,255,255,.5);
            text-decoration: none; transition: color .2s;
        }
        .hdr-meta-item:hover { color: rgba(255,255,255,.85); }
        .hdr-social { display: flex; justify-content: center; gap: .5rem; margin-top: .85rem; }
        .hdr-social a {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: .9rem; text-decoration: none;
            transition: transform .2s, opacity .2s; color: #fff;
        }
        .hdr-social a:hover { transform: scale(1.1); opacity: .9; }
        .soc-ig { background: linear-gradient(135deg, #833AB4, #FD1D1D, #F77737); }
        .soc-fb { background: #1877F2; }
        .soc-tw { background: #000; }
        .soc-wa { background: #25D366; }
        .hdr-order {
            display: inline-flex; align-items: center; gap: .35rem;
            margin-top: .65rem;
            background: rgba(34,197,94,.12); border: 1px solid rgba(34,197,94,.25);
            color: #22c55e; font-size: .7rem; font-weight: 700;
            padding: .25rem .7rem; border-radius: var(--radius-pill);
        }
        .hdr-order .dot { width: 6px; height: 6px; border-radius: 50%; background: #22c55e; animation: pls 2s infinite; }
        @@keyframes pls { 0%, 100% { opacity: 1; } 50% { opacity: .35; } }
        .hdr-review-btn {
            display: inline-flex; align-items: center; gap: .4rem; margin-top: .65rem;
            background: rgba(245,158,11,.12); border: 1px solid rgba(245,158,11,.25);
            color: #f59e0b; font-size: .74rem; font-weight: 700;
            padding: .35rem .85rem; border-radius: var(--radius-pill);
            cursor: pointer; transition: all .2s; text-decoration: none;
        }
        .hdr-review-btn:hover { background: rgba(245,158,11,.2); color: #fbbf24; transform: translateY(-1px); }

        /* ========== Toolbar ========== */
        .toolbar {
            position: sticky; top: 0; z-index: 50;
            background: #fff;
            border-bottom: 1px solid var(--border-light);
            padding: .75rem 1rem;
        }
        .toolbar-inner {
            max-width: 640px; margin: 0 auto;
            display: flex; align-items: center; gap: .65rem;
        }
        .tb-cat-btn {
            width: 44px; height: 44px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            background: #fff;
            flex-shrink: 0; cursor: pointer;
            font-size: 1.05rem; color: var(--text2);
            display: flex; align-items: center; justify-content: center;
            transition: background .2s, border-color .2s, color .2s;
        }
        .tb-cat-btn:hover {
            background: var(--accent-soft);
            border-color: rgba(255,107,53,.25);
            color: var(--accent);
        }
        .tb-cat-btn:active { background: var(--accent-soft-2); }
        .tb-search {
            flex: 1; height: 44px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--bg);
            padding: 0 1rem;
            font-size: .9rem; color: var(--text);
            outline: none; font-family: inherit;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .tb-search::placeholder { color: var(--text3); }
        .tb-search:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-soft);
            background: #fff;
        }
        @@media (min-width: 769px) { .tb-cat-btn { display: none !important; } }

        /* ========== Category pills (foto varsa göster, yoksa ikon) ========== */
        .cat-pills {
            max-width: 640px; margin: 0 auto;
            padding: .5rem 1rem .35rem;
            display: flex; gap: .4rem;
            overflow-x: auto; scrollbar-width: none;
        }
        .cat-pills::-webkit-scrollbar { display: none; }
        .cat-pill {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .4rem .85rem;
            border-radius: var(--radius-pill);
            font-size: .78rem; font-weight: 600; white-space: nowrap;
            background: var(--card); color: var(--text2);
            border: 1px solid var(--border);
            cursor: pointer; transition: all .2s; flex-shrink: 0; user-select: none;
            box-shadow: var(--shadow);
        }
        .cat-pill:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-soft); }
        .cat-pill.active {
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: #fff; border-color: transparent;
            box-shadow: 0 4px 14px rgba(255,107,53,.3);
        }
        .cat-pill .cat-pill-img {
            width: 22px; height: 22px;
            border-radius: 6px;
            object-fit: cover;
        }
        .cat-pill .cat-pill-icon {
            width: 22px; height: 22px;
            border-radius: 6px;
            background: var(--accent-soft);
            color: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem;
        }
        .cat-pill.active .cat-pill-icon { background: rgba(255,255,255,.25); color: #fff; }
        @@media (max-width: 768px) { .cat-pills { display: none !important; } }

        /* ========== Offcanvas drawer ========== */
        .offcanvas { --bs-offcanvas-width: 300px; }
        .drawer-item {
            display: flex; align-items: center; gap: .75rem;
            padding: .7rem 1rem;
            cursor: pointer; transition: background .15s;
            border: none; background: none; width: 100%;
            text-align: left; font-family: inherit;
        }
        .drawer-item:hover { background: var(--border-light); }
        .drawer-item.active { background: var(--accent-soft); }
        .drawer-item.active .di-name { color: var(--accent); font-weight: 700; }
        .di-img {
            width: 40px; height: 40px;
            border-radius: var(--radius-sm);
            object-fit: cover;
            flex-shrink: 0;
        }
        .di-icon {
            width: 40px; height: 40px;
            border-radius: var(--radius-sm);
            flex-shrink: 0;
            background: linear-gradient(145deg, var(--accent-soft), var(--accent-soft-2));
            display: flex; align-items: center; justify-content: center;
            color: var(--accent); font-size: 1rem;
            border: 1px solid rgba(255,107,53,.1);
        }
        .drawer-item.active .di-icon { background: linear-gradient(145deg, var(--accent), var(--accent2)); color: #fff; border-color: transparent; }
        .di-name { font-size: .88rem; font-weight: 600; color: var(--text); }
        .di-count { font-size: .72rem; color: var(--text3); margin-top: .15rem; }
        .drawer-sub .drawer-item { padding-left: 1.75rem; }
        .drawer-sub .di-img, .drawer-sub .di-icon { width: 32px; height: 32px; border-radius: 8px; font-size: .8rem; }

        /* ========== Content & accordion ========== */
        .content { max-width: 640px; margin: 0 auto; padding: 1rem 1rem 2.5rem; }
        .cat-section { margin-bottom: .5rem; }
        .cat-header {
            display: flex; align-items: center; gap: .75rem;
            padding: .85rem 0;
            border-bottom: 2px solid var(--border-light);
            cursor: pointer; user-select: none; transition: background .15s;
            border: none; background: transparent; width: 100%;
            text-align: left; font-family: inherit; margin: 0;
        }
        .cat-header:hover { background: linear-gradient(90deg, var(--accent-soft), transparent); }
        .cat-header-img {
            width: 44px; height: 44px;
            border-radius: var(--radius-sm);
            object-fit: cover;
            flex-shrink: 0;
            border: 1px solid var(--border-light);
        }
        .cat-header-icon {
            width: 44px; height: 44px;
            border-radius: var(--radius-sm);
            flex-shrink: 0;
            background: linear-gradient(145deg, var(--accent-soft), var(--accent-soft-2));
            display: flex; align-items: center; justify-content: center;
            color: var(--accent); font-size: 1.1rem;
            border: 1px solid rgba(255,107,53,.1);
        }
        .cat-header[aria-expanded="true"] .cat-header-icon {
            background: linear-gradient(145deg, var(--accent), var(--accent2));
            color: #fff; border-color: transparent;
        }
        .cat-header-name { font-size: .95rem; font-weight: 800; color: var(--text); }
        .cat-header-count { font-size: .72rem; color: var(--text3); margin-left: auto; }
        .cat-header .cat-chevron { margin-left: .5rem; font-size: 1rem; color: var(--text3); transition: transform .25s; }
        .cat-header[aria-expanded="true"] .cat-chevron { transform: rotate(180deg); color: var(--accent); }

        .sub-header {
            font-size: .78rem; font-weight: 700; color: var(--text2);
            margin: 1rem 0 .4rem;
            padding-left: .5rem;
            display: flex; align-items: center; gap: .4rem;
        }
        .sub-header::before { content: ''; width: 12px; height: 2px; background: var(--border); border-radius: 1px; }
        .sub-header img { width: 20px; height: 20px; border-radius: 5px; object-fit: cover; }
        .sub-header-icon {
            width: 20px; height: 20px;
            border-radius: 5px;
            background: var(--accent-soft);
            color: var(--accent);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: .65rem;
        }

        /* ========== Product cards (foto varsa göster, yoksa placeholder) ========== */
        .prod {
            background: var(--card);
            border: 1px solid var(--border-light);
            border-radius: var(--radius);
            padding: .85rem 1rem;
            display: flex; align-items: center; gap: .85rem;
            margin-bottom: .5rem;
            transition: box-shadow .2s, transform .2s, border-color .2s;
            box-shadow: var(--shadow);
        }
        a.prod {
            text-decoration: none;
            color: inherit;
        }
        .prod:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
            border-color: var(--border);
        }
        .prod-img {
            width: 64px; height: 64px;
            border-radius: var(--radius-sm);
            object-fit: cover;
            flex-shrink: 0;
        }
        .prod-img-empty {
            width: 64px; height: 64px;
            border-radius: var(--radius-sm);
            flex-shrink: 0;
            background: linear-gradient(145deg, #f1f5f9, #e2e8f0);
            display: flex; align-items: center; justify-content: center;
            color: var(--text3); font-size: 1.4rem;
            border: 1px solid var(--border-light);
        }
        .prod-body { flex: 1; min-width: 0; }
        .prod-name { font-size: .9rem; font-weight: 700; color: var(--text); line-height: 1.35; }
        .prod-desc {
            font-size: .78rem; color: var(--text3);
            margin-top: .2rem; line-height: 1.5;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .prod-price { font-size: 1rem; font-weight: 800; color: var(--accent); white-space: nowrap; flex-shrink: 0; }

        .no-results { text-align: center; padding: 3rem 1.5rem; color: var(--text3); }
        .no-results i { font-size: 2.5rem; margin-bottom: .75rem; display: block; opacity: .4; }

        /* ========== Review modal ========== */
        #reviewModal .modal-dialog { max-width: 480px; }
        #reviewModal .modal-content { background: transparent; border: none; box-shadow: none; }
        #reviewModal .modal-body { padding-top: 0; }
        .rv-modal-panel {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,.15);
            overflow: hidden;
        }
        .rv-summary { text-align: center; padding: 1.25rem; margin-bottom: 1rem; background: #fefce8; border: 1px solid #fef3c7; border-radius: var(--radius); }
        .rv-big { font-size: 2.8rem; font-weight: 900; color: var(--text); line-height: 1; }
        .rv-stars { color: var(--star); font-size: 1.1rem; margin: .25rem 0; }
        .rv-item { padding: .85rem 0; border-bottom: 1px solid var(--border-light); }
        .rv-item:last-child { border-bottom: none; }
        .rv-item-name { font-weight: 700; font-size: .82rem; color: var(--text); }
        .rv-item-stars { color: var(--star); font-size: .74rem; }
        .rv-form-wrap { background: linear-gradient(135deg, var(--dark), var(--dark2)); border-radius: var(--radius); padding: 1.35rem; }
        .rv-form-wrap h4 { color: #fff; font-size: .9rem; font-weight: 700; margin-bottom: .9rem; }
        .rv-label { display: block; color: rgba(255,255,255,.6); font-size: .74rem; font-weight: 600; margin-bottom: .3rem; }
        .rv-input {
            width: 100%; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12);
            color: #fff; border-radius: var(--radius-sm); padding: .55rem .85rem; font-size: .85rem;
            font-family: inherit; margin-bottom: .7rem; outline: none;
        }
        .rv-input::placeholder { color: rgba(255,255,255,.25); }
        .star-picker { display: flex; gap: .3rem; margin-bottom: .75rem; }
        .star-picker .btn-star {
            background: none; border: none; padding: 0; cursor: pointer;
            font-size: 1.65rem; color: rgba(255,255,255,.2);
            transition: color .15s, transform .15s;
        }
        .star-picker .btn-star.lit { color: var(--star); }
        .star-picker .btn-star:hover { transform: scale(1.12); }
        .rv-submit {
            width: 100%; padding: .65rem; border: none; border-radius: var(--radius-sm);
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: #fff; font-weight: 700; font-size: .86rem; cursor: pointer;
            transition: transform .2s, box-shadow .2s;
        }
        .rv-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(255,107,53,.35); }
        .rv-msg { border-radius: var(--radius-sm); font-size: .82rem; padding: .6rem .9rem; margin-bottom: .7rem; }
        .rv-msg-ok { background: rgba(34,197,94,.12); color: #16a34a; border: 1px solid rgba(34,197,94,.2); }
        .rv-msg-warn { background: rgba(251,191,36,.12); color: #b45309; border: 1px solid rgba(251,191,36,.2); }

        /* ========== Footer (her zaman en altta) ========== */
        .menu-app main { flex: 1; }
        .ftr {
            margin-top: auto;
            padding: 1.25rem 1rem calc(1.25rem + var(--safe-b));
            text-align: center;
            background: #fff;
            border-top: 1px solid var(--border-light);
            flex-shrink: 0;
        }
        .ftr-inner {
            max-width: 640px; margin: 0 auto;
            display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
            font-size: .8rem; color: var(--text3);
            font-weight: 500;
        }
        .ftr a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 700;
            transition: color .2s;
        }
        .ftr a:hover { color: var(--accent2); }
        .ftr-logo {
            display: inline-flex; align-items: center; justify-content: center;
            width: 22px; height: 22px;
            border-radius: 6px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: #fff;
            font-size: .7rem;
        }

        /* Değerlendirme sonrası geri bildirim toast */
        .review-feedback-toast {
            position: fixed; top: 0; left: 0; right: 0; z-index: 9999;
            padding: 0.75rem 1rem; padding-left: max(1rem, env(safe-area-inset-left)); padding-right: max(1rem, env(safe-area-inset-right)); padding-top: max(0.75rem, env(safe-area-inset-top));
            animation: reviewFeedbackSlide 0.35s ease-out;
        }
        .review-feedback-ok { background: linear-gradient(135deg, #059669, #10b981); color: #fff; box-shadow: 0 4px 20px rgba(5,150,105,.4); }
        .review-feedback-warn { background: linear-gradient(135deg, #d97706, #f59e0b); color: #fff; box-shadow: 0 4px 20px rgba(217,119,6,.4); }
        .review-feedback-inner { display: flex; align-items: center; justify-content: center; max-width: 480px; margin: 0 auto; font-size: 0.9rem; font-weight: 600; }
        .review-feedback-inner span { flex: 1; }
        .review-feedback-close { background: none; border: none; color: inherit; opacity: 0.85; padding: 0.25rem; margin-left: 0.5rem; cursor: pointer; border-radius: 6px; line-height: 1; }
        .review-feedback-close:hover { opacity: 1; background: rgba(255,255,255,.2); }
        @@keyframes reviewFeedbackSlide { from { transform: translateY(-100%); } to { transform: translateY(0); } }

        @@media (max-width: 380px) {
            .ftr-inner { flex-wrap: wrap; font-size: .75rem; }
        }

        /* ========== Masaüstü: Sadece mobil uyarısı ========== */
        .desktop-only-screen {
            display: none;
        }
        @@media (min-width: 769px) {
            @if(!$allowDesktopPreview)
            .desktop-only-screen {
                display: flex;
                position: fixed;
                inset: 0;
                z-index: 9999;
                background: var(--bg);
                align-items: center;
                justify-content: center;
                padding: 2rem;
            }
            .menu-app {
                display: none !important;
            }
            @endif
        }
        .desktop-only-screen .dm-box {
            max-width: 440px;
            text-align: center;
            background: var(--card);
            border-radius: var(--radius);
            padding: 2.5rem 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-light);
        }
        .desktop-only-screen .dm-icon {
            width: 64px; height: 64px;
            margin: 0 auto 1.25rem;
            border-radius: 16px;
            background: linear-gradient(145deg, var(--accent-soft), var(--accent-soft-2));
            color: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.75rem;
        }
        .desktop-only-screen .dm-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 1rem;
            line-height: 1.4;
        }
        .desktop-only-screen .dm-text {
            font-size: .9rem;
            color: var(--text2);
            line-height: 1.65;
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T58K7CMS"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    @unless($allowDesktopPreview)
    <div class="desktop-only-screen" aria-live="polite">
        <div class="dm-box">
            <div class="dm-icon" aria-hidden="true"><i class="bi bi-phone"></i></div>
            <h2 class="dm-title">{{ $locale === 'tr' ? 'Sipariş Masanda QR Menüsü' : 'Siparis Masanda QR Menu' }}</h2>
            <p class="dm-text">
                {{ $locale === 'tr'
                    ? 'Bu sayfa yalnızca mobil cihazlar için tasarlanmıştır. Menüyü en iyi şekilde görüntülemek için lütfen cep telefonu veya tabletinizden erişiniz. QR kodu cihazınızla tarayarak menüye ulaşabilirsiniz.'
                    : 'This page is designed for mobile devices only. For the best experience, please open the menu from your phone or tablet and access it by scanning the QR code.' }}
            </p>
        </div>
    </div>
    @endunless

    <div class="menu-app">
    <header class="hdr">
        <div class="hdr-content">
            @if($tenant->logo)
                <img src="{{ asset('uploads/'.$tenant->logo) }}" alt="{{ $tenant->restoran_adi }}" class="hdr-logo">
            @else
                <div class="hdr-logo-fallback"><i class="bi bi-shop"></i></div>
            @endif
            <h1 class="hdr-name">{{ $tenant->restoran_adi }}</h1>
            <div class="hdr-sub">{{ __('public.menu_suffix') }}</div>
            @if(($tenant->restoran_adresi ?? null) || ($tenant->restoran_telefonu ?? null))
            <div class="hdr-meta">
                @if($tenant->restoran_adresi)
                <span class="hdr-meta-item"><i class="bi bi-geo-alt-fill"></i> {{ $tenant->restoran_adresi }}</span>
                @endif
                @if($tenant->restoran_telefonu)
                <a href="tel:{{ $tenant->restoran_telefonu }}" class="hdr-meta-item"><i class="bi bi-telephone-fill"></i> {{ $tenant->restoran_telefonu }}</a>
                @endif
            </div>
            @endif
            @if(($tenant->instagram ?? null) || ($tenant->facebook ?? null) || ($tenant->twitter ?? null) || ($tenant->whatsapp ?? null))
            <div class="hdr-social">
                @if($tenant->instagram)<a href="https://instagram.com/{{ $tenant->instagram }}" target="_blank" class="soc-ig"><i class="bi bi-instagram"></i></a>@endif
                @if($tenant->facebook)<a href="https://facebook.com/{{ $tenant->facebook }}" target="_blank" class="soc-fb"><i class="bi bi-facebook"></i></a>@endif
                @if($tenant->twitter)<a href="https://x.com/{{ $tenant->twitter }}" target="_blank" class="soc-tw"><i class="bi bi-twitter-x"></i></a>@endif
                @if($tenant->whatsapp)<a href="https://wa.me/90{{ preg_replace('/\D/', '', $tenant->whatsapp) }}" target="_blank" class="soc-wa"><i class="bi bi-whatsapp"></i></a>@endif
            </div>
            @endif
            <div class="d-flex justify-content-center gap-2 flex-wrap mt-2">
                @if(!empty($tenant->ordering_enabled))
                <div class="hdr-order"><span class="dot"></span> Sipariş Açık</div>
                @endif
                <button type="button" class="hdr-review-btn" data-bs-toggle="modal" data-bs-target="#reviewModal">
                    <i class="bi bi-star-fill"></i>
                    @if($reviewStats->total > 0)
                        {{ number_format($reviewStats->avg_rating, 1) }} ({{ $reviewStats->total }})
                    @else
                        {{ __('public.rate_now') }}
                    @endif
                </button>
            </div>
        </div>
    </header>

    {{-- Slider Carousel (premium tenants) --}}
    @if(isset($sliders) && $sliders->isNotEmpty())
    <div id="menuSlider" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-indicators">
            @foreach($sliders as $i => $sl)
            <button type="button" data-bs-target="#menuSlider" data-bs-slide-to="{{ $i }}" {!! $i === 0 ? 'class="active"' : '' !!}></button>
            @endforeach
        </div>
        <div class="carousel-inner">
            @foreach($sliders as $i => $sl)
            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                <img src="{{ asset('uploads/' . $sl->image) }}" alt="{{ $sl->title }}" class="d-block w-100" style="height:200px;object-fit:cover;">
                @if($sl->title || $sl->description)
                <div class="carousel-caption d-block" style="background:linear-gradient(transparent,rgba(0,0,0,.6));bottom:0;left:0;right:0;padding:.75rem 1rem;">
                    @if($sl->title)<div style="font-size:.88rem;font-weight:700">{{ $sl->title }}</div>@endif
                    @if($sl->description)<div style="font-size:.72rem;opacity:.85;margin-top:.15rem">{{ Str::limit($sl->description, 80) }}</div>@endif
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @if($sliders->count() > 1)
        <button class="carousel-control-prev" type="button" data-bs-target="#menuSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#menuSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
        @endif
    </div>
    @endif

    <div class="toolbar">
        <div class="toolbar-inner">
            <button type="button" class="tb-cat-btn d-md-none" data-bs-toggle="offcanvas" data-bs-target="#catOffcanvas" aria-label="Kategoriler">
                <i class="bi bi-grid-3x3-gap-fill"></i>
            </button>
            <input type="text" class="tb-search form-control" id="menuSearch" placeholder="{{ __('public.search_placeholder') }}" autocomplete="off">
        </div>
    </div>

    @if($categories->isNotEmpty())
    <div class="cat-pills">
        <button type="button" class="cat-pill active" data-cat-id=""> <span class="cat-pill-icon"><i class="bi bi-grid-3x3-gap"></i></span> {{ __('public.all') }} </button>
        @foreach($categories as $cat)
        <button type="button" class="cat-pill" data-cat-id="{{ $cat->id }}">
            @if($cat->image)
                <img src="{{ asset('uploads/'.$cat->image) }}" alt="" class="cat-pill-img">
            @else
                <span class="cat-pill-icon"><i class="bi bi-grid-3x3-gap-fill"></i></span>
            @endif
            {{ $cat->name }}
        </button>
        @endforeach
    </div>
    @endif

    <div class="offcanvas offcanvas-start" tabindex="-1" id="catOffcanvas">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title"><i class="bi bi-grid-3x3-gap-fill text-warning me-1"></i> Kategoriler</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <button type="button" class="drawer-item active" data-cat-id="">
                <div class="di-icon"><i class="bi bi-grid-3x3-gap"></i></div>
                <div>
                    <div class="di-name">Tüm Menü</div>
                <div class="di-count" id="totalProductCount">{{ __('public.products_count', ['count' => $products->sum(fn($c) => $c->count())]) }}</div>
                </div>
            </button>
            @foreach($categories as $cat)
                @php
                    $catProds = $products->get($cat->id, collect());
                    $subs = $subCategories[$cat->id] ?? collect();
                    $totalInCat = $catProds->count();
                    foreach ($subs as $sub) { $totalInCat += ($products->get($sub->id, collect()))->count(); }
                @endphp
                <button type="button" class="drawer-item" data-cat-id="{{ $cat->id }}">
                    @if($cat->image)
                        <img src="{{ asset('uploads/'.$cat->image) }}" class="di-img" alt="">
                    @else
                        <div class="di-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
                    @endif
                    <div>
                        <div class="di-name">{{ $cat->name }}</div>
                        <div class="di-count">{{ __('public.products_count', ['count' => $totalInCat]) }}</div>
                    </div>
                </button>
                @if($subs->isNotEmpty())
                <div class="drawer-sub">
                    @foreach($subs as $sub)
                        @php $subProdCount = ($products->get($sub->id, collect()))->count(); @endphp
                        @if($subProdCount > 0)
                        <button type="button" class="drawer-item" data-cat-id="{{ $sub->id }}">
                            @if($sub->image)
                                <img src="{{ asset('uploads/'.$sub->image) }}" class="di-img" alt="">
                            @else
                                <div class="di-icon"><i class="bi bi-dash"></i></div>
                            @endif
                            <div>
                                <div class="di-name">{{ $sub->name }}</div>
                                <div class="di-count">{{ __('public.products_count', ['count' => $subProdCount]) }}</div>
                            </div>
                        </button>
                        @endif
                    @endforeach
                </div>
                @endif
            @endforeach
        </div>
    </div>

    <main class="content">
        @if($categories->isEmpty())
            <div class="no-results">
                <i class="bi bi-journal-text"></i>
                <div class="fw-bold">{{ __('public.menu_not_ready') }}</div>
            </div>
        @else
            <div id="menuAccordion">
            @foreach($categories as $cat)
                @php
                    $catProducts = $products->get($cat->id, collect());
                    $subs = $subCategories[$cat->id] ?? collect();
                    $totalInCat = $catProducts->count();
                    foreach ($subs as $sub) { $totalInCat += ($products->get($sub->id, collect()))->count(); }
                @endphp
                <section class="cat-section" data-cat-id="{{ $cat->id }}" id="cat-{{ $cat->id }}">
                    <button type="button" class="cat-header" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $cat->id }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse-{{ $cat->id }}">
                        @if($cat->image)
                            <img src="{{ asset('uploads/'.$cat->image) }}" class="cat-header-img" alt="">
                        @else
                            <div class="cat-header-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
                        @endif
                        <span class="cat-header-name">{{ $cat->name }}</span>
                        <span class="cat-header-count cat-count" data-cat-id="{{ $cat->id }}">{{ __('public.products_count', ['count' => $totalInCat]) }}</span>
                        <i class="bi bi-chevron-down cat-chevron"></i>
                    </button>
                    <div class="collapse {{ $loop->first ? 'show' : '' }}" id="collapse-{{ $cat->id }}" data-bs-parent="#menuAccordion">
                    <div class="pt-2 pb-3">
                    @foreach($catProducts as $product)
                    <a href="{{ route('public.product', ['tenantId' => $tenant->id, 'productId' => $product->id, 'lang' => app()->getLocale()]) }}" class="prod" data-cat-id="{{ $cat->id }}" data-name="{{ strtolower($product->name) }}" data-desc="{{ strtolower($product->description ?? '') }}">
                        @if($product->image)
                            <img src="{{ asset('uploads/'.$product->image) }}" alt="{{ $product->name }}" class="prod-img">
                        @else
                            <div class="prod-img-empty"><i class="bi bi-box-seam"></i></div>
                        @endif
                        <div class="prod-body">
                            <div class="prod-name">{{ $product->name }}</div>
                            @if($product->description)
                            <div class="prod-desc">{{ $product->description }}</div>
                            @endif
                        </div>
                        <div class="prod-price">{{ number_format($product->price, 2, ',', '.') }} ₺</div>
                    </a>
                    @endforeach
                    @foreach($subs as $sub)
                        @php $subProducts = $products->get($sub->id, collect()); @endphp
                        @if($subProducts->isNotEmpty())
                        <div class="sub-header">
                            @if($sub->image)
                                <img src="{{ asset('uploads/'.$sub->image) }}" alt="{{ $sub->name }}">
                            @else
                                <span class="sub-header-icon"><i class="bi bi-dash-lg"></i></span>
                            @endif
                            {{ $sub->name }}
                        </div>
                        @foreach($subProducts as $product)
                        <a href="{{ route('public.product', ['tenantId' => $tenant->id, 'productId' => $product->id, 'lang' => app()->getLocale()]) }}" class="prod" data-cat-id="{{ $sub->id }}" data-parent-cat="{{ $cat->id }}" data-name="{{ strtolower($product->name) }}" data-desc="{{ strtolower($product->description ?? '') }}">
                            @if($product->image)
                                <img src="{{ asset('uploads/'.$product->image) }}" alt="{{ $product->name }}" class="prod-img">
                            @else
                                <div class="prod-img-empty"><i class="bi bi-box-seam"></i></div>
                            @endif
                            <div class="prod-body">
                                <div class="prod-name">{{ $product->name }}</div>
                                @if($product->description)
                                <div class="prod-desc">{{ $product->description }}</div>
                                @endif
                            </div>
                            <div class="prod-price">{{ number_format($product->price, 2, ',', '.') }} ₺</div>
                        </a>
                        @endforeach
                        @endif
                    @endforeach
                    </div>
                    </div>
                </section>
            @endforeach
            <div class="no-results d-none" id="noResults">
                <i class="bi bi-search"></i>
                <div class="fw-bold">Sonuç bulunamadı</div>
                <div class="small mt-1">Farklı bir arama deneyin</div>
            </div>
        @endif
    </main>

    <footer class="ftr">
        <div class="ftr-inner">
            <span class="ftr-logo"><i class="bi bi-qr-code"></i></span>
            <span><a href="{{ route('home', ['lang' => app()->getLocale()]) }}">Sipariş <span style="color:var(--accent)">Masanda</span></a></span>
        </div>
    </footer>

    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="rv-modal-panel">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"><i class="bi bi-star-fill text-warning me-1"></i> {{ __('public.reviews') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($reviewStats->total > 0)
                    <div class="rv-summary">
                        <div class="rv-big">{{ number_format($reviewStats->avg_rating, 1) }}</div>
                        <div class="rv-stars">
                            @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= round($reviewStats->avg_rating) ? '-fill' : '' }}"></i>
                            @endfor
                        </div>
                        <div class="small text-secondary">{{ $locale === 'tr' ? $reviewStats->total . ' değerlendirme' : $reviewStats->total . ' reviews' }}</div>
                    </div>
                    @endif
                    @foreach($reviews as $review)
                    <div class="rv-item">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="rv-item-name"><i class="bi bi-person-circle text-secondary me-1"></i>{{ $review->customer_name ?: ($locale === 'tr' ? 'Anonim' : 'Anonymous') }}</span>
                            <span class="small text-secondary">{{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}</span>
                        </div>
                        <div class="rv-item-stars">
                            @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                            @endfor
                        </div>
                        @if($review->comment)
                        <div class="small text-secondary mt-1">{{ $review->comment }}</div>
                        @endif
                    </div>
                    @endforeach
                    <hr>
                    <div class="rv-form-wrap">
                        <h4><i class="bi bi-chat-heart text-warning me-1"></i> {{ $locale === 'tr' ? 'Deneyiminizi paylaşın' : 'Share your experience' }}</h4>
                        @if(session('review_success'))
                        <div class="rv-msg rv-msg-ok"><i class="bi bi-check-circle-fill me-1"></i> {{ __('public.review_saved') }}</div>
                        @endif
                        @if(session('review_error') === 'already_reviewed')
                        <div class="rv-msg rv-msg-warn"><i class="bi bi-info-circle-fill me-1"></i> {{ $locale === 'tr' ? 'Bugün zaten bir değerlendirme yaptınız.' : 'You have already submitted a review today.' }}</div>
                        @endif
                        <form method="POST" action="{{ route('public.review', $tenant->id) }}">
                            @csrf
                            <label class="rv-label">{{ $locale === 'tr' ? 'Adınız' : 'Your name' }} <span class="opacity-50">({{ $locale === 'tr' ? 'opsiyonel' : 'optional' }})</span></label>
                            <input type="text" name="customer_name" class="rv-input form-control" maxlength="100" placeholder="{{ $locale === 'tr' ? 'Adınız...' : 'Your name...' }}">
                            <label class="rv-label">{{ $locale === 'tr' ? 'Puanınız' : 'Your rating' }}</label>
                            <div class="star-picker">
                                @for($s = 1; $s <= 5; $s++)
                                <button type="button" class="btn-star lit" data-rating="{{ $s }}" aria-label="{{ $s }} {{ $locale === 'tr' ? 'yıldız' : 'stars' }}">
                                    <i class="bi bi-star-fill"></i>
                                </button>
                                @endfor
                                <input type="hidden" name="rating" id="ratingInput" value="5">
                            </div>
                            <label class="rv-label">{{ $locale === 'tr' ? 'Yorumunuz' : 'Your comment' }} <span class="opacity-50">({{ $locale === 'tr' ? 'opsiyonel' : 'optional' }})</span></label>
                            <textarea name="comment" class="rv-input form-control" rows="3" maxlength="1000" placeholder="{{ $locale === 'tr' ? 'Deneyiminizi anlatın...' : 'Tell us about your experience...' }}"></textarea>
                            <button type="submit" class="rv-submit btn w-100">
                                <i class="bi bi-send me-1"></i> {{ __('public.review_send') }}
                            </button>
                        </form>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    </div><!-- .menu-app -->

    @if(session('review_success') || session('review_error'))
    <div id="reviewFeedbackToast" class="review-feedback-toast review-feedback-{{ session('review_success') ? 'ok' : 'warn' }}" role="alert">
        <div class="review-feedback-inner">
            @if(session('review_success'))
                <i class="bi bi-check-circle-fill me-2"></i>
                <span>{{ __('public.review_saved') }}</span>
            @else
                <i class="bi bi-info-circle-fill me-2"></i>
                <span>{{ $locale === 'tr' ? 'Bugün zaten bir değerlendirme yaptınız.' : 'You have already submitted a review today.' }}</span>
            @endif
            <button type="button" class="review-feedback-close" onclick="this.closest('.review-feedback-toast').remove()" aria-label="{{ $locale === 'tr' ? 'Kapat' : 'Close' }}"><i class="bi bi-x-lg"></i></button>
        </div>
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @php
        $catChildren = [];
        foreach ($subCategories as $parentId => $subs) {
            $catChildren[$parentId] = $subs->pluck('id')->values()->toArray();
        }
    @endphp
    @if(session('review_success') || session('review_error'))
    <script>
    (function() {
        var toast = document.getElementById('reviewFeedbackToast');
        if (toast) {
            setTimeout(function() { toast.remove(); }, 5000);
        }
        var modalEl = document.getElementById('reviewModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    })();
    </script>
    @endif
    <script>
    (function() {
        var productCountSuffix = @json(app()->getLocale() === 'tr' ? 'ürün' : 'products');
        var allProducts = document.querySelectorAll('.prod');
        var sections = document.querySelectorAll('.cat-section');
        var collapses = document.querySelectorAll('#menuAccordion .collapse');
        var searchEl = document.getElementById('menuSearch');
        var noResultsEl = document.getElementById('noResults');
        var totalCountEl = document.getElementById('totalProductCount');
        var activeCatId = '';

        function applySearch() {
            var q = (searchEl ? searchEl.value : '').toLowerCase().trim();
            var total = 0;

            allProducts.forEach(function(prod) {
                if (q) {
                    var name = prod.getAttribute('data-name') || '';
                    var desc = prod.getAttribute('data-desc') || '';
                    var show = name.indexOf(q) !== -1 || desc.indexOf(q) !== -1;
                    prod.style.display = show ? '' : 'none';
                    if (show) total++;
                } else {
                    prod.style.display = '';
                    total++;
                }
            });

            if (q) {
                collapses.forEach(function(el) {
                    bootstrap.Collapse.getOrCreateInstance(el).show();
                });
                sections.forEach(function(sec) {
                    var any = 0;
                    sec.querySelectorAll('.prod').forEach(function(p) {
                        if (p.style.display !== 'none') any++;
                    });
                    sec.style.display = any > 0 ? '' : 'none';
                });
            } else {
                sections.forEach(function(sec) { sec.style.display = ''; });
                if (activeCatId) {
                    collapses.forEach(function(el) {
                        var inst = bootstrap.Collapse.getOrCreateInstance(el);
                        if (el.id === 'collapse-' + activeCatId) inst.show(); else inst.hide();
                    });
                }
            }

            if (noResultsEl) noResultsEl.classList.toggle('d-none', total > 0);
            if (totalCountEl) totalCountEl.textContent = total + ' ' + productCountSuffix;
        }

        function setCategory(catId) {
            activeCatId = catId || '';

            document.querySelectorAll('.cat-pill, .drawer-item').forEach(function(btn) {
                btn.classList.toggle('active', (btn.getAttribute('data-cat-id') || '') === activeCatId);
            });

            allProducts.forEach(function(p) { p.style.display = ''; });
            sections.forEach(function(s) { s.style.display = ''; });

            collapses.forEach(function(el) {
                var inst = bootstrap.Collapse.getOrCreateInstance(el);
                if (!activeCatId) {
                    inst.show();
                } else if (el.id === 'collapse-' + activeCatId) {
                    inst.show();
                } else {
                    inst.hide();
                }
            });

            if (searchEl) searchEl.value = '';

            window.scrollTo({ top: 0, behavior: 'smooth' });
            var oc = bootstrap.Offcanvas.getInstance(document.getElementById('catOffcanvas'));
            if (oc) oc.hide();
        }

        if (searchEl) {
            searchEl.addEventListener('input', applySearch);
        }

        document.querySelectorAll('.cat-pill').forEach(function(btn) {
            btn.addEventListener('click', function() { setCategory(this.getAttribute('data-cat-id') || ''); });
        });
        document.querySelectorAll('.drawer-item').forEach(function(btn) {
            btn.addEventListener('click', function() { setCategory(this.getAttribute('data-cat-id') || ''); });
        });

        document.querySelectorAll('.star-picker .btn-star').forEach(function(btn) {
            var rating = parseInt(btn.getAttribute('data-rating'), 10);
            btn.addEventListener('click', function() {
                document.getElementById('ratingInput').value = rating;
                var parent = btn.closest('.star-picker');
                parent.querySelectorAll('.btn-star').forEach(function(b) {
                    b.classList.toggle('lit', parseInt(b.getAttribute('data-rating'), 10) <= rating);
                    var icon = b.querySelector('i.bi');
                    if (icon) icon.className = parseInt(b.getAttribute('data-rating'), 10) <= rating ? 'bi bi-star-fill' : 'bi bi-star';
                });
            });
            btn.addEventListener('mouseenter', function() {
                var parent = btn.closest('.star-picker');
                parent.querySelectorAll('.btn-star').forEach(function(b) {
                    var r = parseInt(b.getAttribute('data-rating'), 10);
                    b.classList.toggle('lit', r <= rating);
                    var icon = b.querySelector('i.bi');
                    if (icon) icon.className = r <= rating ? 'bi bi-star-fill' : 'bi bi-star';
                });
            });
        });
        var starPicker = document.querySelector('.star-picker');
        if (starPicker) {
            starPicker.addEventListener('mouseleave', function() {
                var current = parseInt(document.getElementById('ratingInput').value, 10);
                this.querySelectorAll('.btn-star').forEach(function(b) {
                    var r = parseInt(b.getAttribute('data-rating'), 10);
                    b.classList.toggle('lit', r <= current);
                    var icon = b.querySelector('i.bi');
                    if (icon) icon.className = r <= current ? 'bi bi-star-fill' : 'bi bi-star';
                });
            });
        }

    })();

    var reviewModalEl = document.getElementById('reviewModal');
    if (reviewModalEl) {
        reviewModalEl.addEventListener('hidden.bs.modal', function() {
            var search = document.getElementById('menuSearch');
            if (search) search.focus({ preventScroll: true });
        });
    }
    </script>

    {{-- Event Modal (premium tenants) --}}
    @if(isset($activeEvent) && $activeEvent)
    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="max-width:380px">
            <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(0,0,0,.25)">
                @if($activeEvent->image)
                <img src="{{ asset('uploads/' . $activeEvent->image) }}" alt="{{ $activeEvent->title }}" style="width:100%;height:180px;object-fit:cover;">
                @endif
                <div style="padding:1.25rem 1.1rem;text-align:center">
                    <div style="font-size:1.1rem;font-weight:800;color:#1f2937;margin-bottom:.35rem">{{ $activeEvent->title }}</div>
                    <div style="font-size:.72rem;color:#9ca3af;font-weight:600;margin-bottom:.6rem">
                        <i class="bi bi-calendar3"></i>
                        {{ \Carbon\Carbon::parse($activeEvent->start_date)->format('d.m.Y') }}
                        @if($activeEvent->end_date) — {{ \Carbon\Carbon::parse($activeEvent->end_date)->format('d.m.Y') }} @endif
                    </div>
                    @if($activeEvent->description)
                    <div style="font-size:.84rem;color:#4b5563;line-height:1.65">{!! nl2br(e($activeEvent->description)) !!}</div>
                    @endif
                    <button type="button" class="btn w-100 mt-3" data-bs-dismiss="modal"
                            style="background:linear-gradient(135deg,var(--accent),var(--accent2));color:#fff;font-weight:700;font-size:.85rem;border-radius:12px;padding:.6rem">
                        Tamam
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
    (function(){
        var key = 'eventSeen_{{ $activeEvent->id }}';
        if (!sessionStorage.getItem(key)) {
            var m = new bootstrap.Modal(document.getElementById('eventModal'));
            m.show();
            sessionStorage.setItem(key, '1');
        }
    })();
    </script>
    @endif
</body>
</html>
