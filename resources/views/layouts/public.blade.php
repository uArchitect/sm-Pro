@php
    $locale = app()->getLocale();
    $isTr = $locale === 'tr';
    $shareImage = asset('og-cover.svg');
    $currentUrl = url()->current();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}">
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
    <title>@yield('title')</title>
    <meta name="description" content="@yield('meta_description')">
    <meta name="keywords" content="@yield('meta_keywords')">
    <meta name="author" content="Sipariş Masanda">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="theme-color" content="#0a0f1e">
    <link rel="canonical" href="@yield('canonical', $currentUrl)">
    <link rel="alternate" hreflang="tr" href="{{ $currentUrl }}?lang=tr">
    <link rel="alternate" hreflang="en" href="{{ $currentUrl }}?lang=en">
    <link rel="alternate" hreflang="x-default" href="{{ $currentUrl }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="@yield('canonical', $currentUrl)">
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:site_name" content="Sipariş Masanda">
    <meta property="og:locale" content="{{ $isTr ? 'tr_TR' : 'en_US' }}">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title')">
    <meta name="twitter:description" content="@yield('meta_description')">
    <meta name="twitter:image" content="{{ $shareImage }}">

    @yield('schema')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        *{font-family:'Inter',sans-serif;box-sizing:border-box}
        body{background:#0a0f1e;color:#fff;margin:0;overflow-x:hidden}
        body::before{content:'';position:fixed;inset:0;background-image:linear-gradient(rgba(255,107,53,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,107,53,.03) 1px,transparent 1px);background-size:60px 60px;pointer-events:none;z-index:0}
        .blob{position:fixed;border-radius:50%;filter:blur(140px);pointer-events:none;opacity:.25;z-index:0}
        .blob-1{width:600px;height:600px;background:#FF6B35;top:-200px;left:-200px}
        .blob-2{width:500px;height:500px;background:#6C5CE7;bottom:-150px;right:-200px}
        .blob-3{width:300px;height:300px;background:#FF6B35;top:50%;left:50%;transform:translate(-50%,-50%)}
        section,.page-content{position:relative;z-index:1}

        /* Navbar */
        .lp-nav{position:fixed;top:0;left:0;right:0;z-index:100;padding:.9rem 0;transition:background .3s,box-shadow .3s}
        .lp-nav.scrolled{background:rgba(10,15,30,.92);backdrop-filter:blur(16px);box-shadow:0 1px 0 rgba(255,255,255,.06)}
        .lp-nav .logo-icon{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#FF6B35,#FF8C42);display:flex;align-items:center;justify-content:center;font-size:1rem;color:#fff;box-shadow:0 4px 16px rgba(255,107,53,.4)}
        .lp-nav .logo-text{font-size:.95rem;font-weight:800;color:#fff;letter-spacing:-.01em}
        .nav-link-item{color:rgba(255,255,255,.6);font-size:.82rem;font-weight:600;text-decoration:none;padding:.4rem .6rem;transition:color .18s}
        .nav-link-item:hover,.nav-link-item.active{color:#fff}
        .nav-btn{padding:.45rem 1.2rem;border-radius:9px;font-size:.82rem;font-weight:600;text-decoration:none;transition:all .18s}
        .nav-btn-ghost{color:rgba(255,255,255,.7);border:1px solid rgba(255,255,255,.12)}
        .nav-btn-ghost:hover{color:#fff;border-color:rgba(255,255,255,.25);background:rgba(255,255,255,.06)}
        .nav-btn-primary{background:linear-gradient(135deg,#FF6B35,#FF8C42);color:#fff;border:none;box-shadow:0 4px 16px rgba(255,107,53,.35)}
        .nav-btn-primary:hover{color:#fff;transform:translateY(-1px);box-shadow:0 6px 24px rgba(255,107,53,.5)}

        /* Page hero (inner pages) */
        .page-hero{padding:8rem 0 3rem;text-align:center}
        .page-hero h1{font-size:clamp(1.8rem,4vw,2.8rem);font-weight:900;line-height:1.12;letter-spacing:-.03em;margin-bottom:.75rem}
        .page-hero h1 .accent{background:linear-gradient(90deg,#FF6B35,#FFB347);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        .page-hero-sub{font-size:1rem;color:rgba(255,255,255,.5);max-width:560px;margin:0 auto;line-height:1.7}

        /* Section helpers */
        .section-badge{display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .75rem;border-radius:999px;font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;margin-bottom:.75rem}
        .section-title{font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:900;letter-spacing:-.03em;line-height:1.1;margin-bottom:.6rem}
        .section-sub{font-size:.95rem;color:rgba(255,255,255,.45);max-width:540px;line-height:1.7}

        /* Card helpers */
        .glass-card{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:18px;padding:1.75rem;transition:all .25s}
        .glass-card:hover{background:rgba(255,255,255,.05);border-color:rgba(255,107,53,.15);transform:translateY(-4px);box-shadow:0 12px 40px rgba(0,0,0,.2)}

        /* CTA bar */
        .cta-bar{padding:5rem 0 6rem}
        .cta-box{background:linear-gradient(135deg,rgba(255,107,53,.1),rgba(108,92,231,.08));border:1px solid rgba(255,107,53,.15);border-radius:24px;padding:3.5rem 2rem;text-align:center;position:relative;overflow:hidden}
        .cta-box::before{content:'';position:absolute;top:-60px;right:-60px;width:200px;height:200px;border-radius:50%;background:rgba(255,107,53,.08);filter:blur(60px)}
        .cta-box h2{font-size:clamp(1.6rem,3vw,2.2rem);font-weight:900;letter-spacing:-.02em;margin-bottom:.5rem;position:relative}
        .cta-box p{color:rgba(255,255,255,.5);font-size:.95rem;margin-bottom:2rem;position:relative}
        .hero-btn-primary{display:inline-flex;align-items:center;gap:.5rem;padding:.85rem 2rem;border-radius:12px;background:linear-gradient(135deg,#FF6B35,#FF8C42);color:#fff;font-weight:700;font-size:.95rem;text-decoration:none;box-shadow:0 8px 32px rgba(255,107,53,.35);transition:all .2s;border:none}
        .hero-btn-primary:hover{color:#fff;transform:translateY(-2px);box-shadow:0 12px 40px rgba(255,107,53,.5)}
        .hero-btn-outline{display:inline-flex;align-items:center;gap:.5rem;padding:.85rem 2rem;border-radius:12px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.8);font-weight:600;font-size:.95rem;text-decoration:none;transition:all .2s}
        .hero-btn-outline:hover{color:#fff;background:rgba(255,255,255,.1);border-color:rgba(255,255,255,.25);transform:translateY(-2px)}

        /* Footer */
        .lp-footer{border-top:1px solid rgba(255,255,255,.06);padding:3rem 0 2rem;position:relative;z-index:10}
        .lp-footer a{color:rgba(255,255,255,.45);text-decoration:none;font-size:.78rem;transition:color .18s}
        .lp-footer a:hover{color:#FF8C42}
        .footer-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:2rem}
        .footer-heading{font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:rgba(255,255,255,.3);margin-bottom:.65rem}
        .footer-bottom{border-top:1px solid rgba(255,255,255,.06);margin-top:2rem;padding-top:1.25rem;font-size:.75rem;color:rgba(255,255,255,.25)}

        /* WhatsApp float */
        .wa-float{
            position:fixed;left:0;top:50%;transform:translateY(-50%);
            z-index:99;display:flex;align-items:center;
            background:linear-gradient(135deg,#0a0f1e 0%,#151b2d 100%);
            border:1px solid rgba(255,107,53,.25);border-left:none;
            border-radius:0 14px 14px 0;
            padding:.5rem .6rem .5rem .5rem;
            box-shadow:0 4px 24px rgba(0,0,0,.35), 0 0 0 1px rgba(255,255,255,.04);
            transition:all .25s ease;text-decoration:none;color:#fff;
            font-size:.8rem;font-weight:600;gap:.5rem;
        }
        .wa-float:hover{color:#fff;padding-left:.85rem;border-color:rgba(255,107,53,.45);box-shadow:0 6px 32px rgba(255,107,53,.15)}
        .wa-float-icon{width:42px;height:42px;border-radius:12px;background:linear-gradient(145deg,#FF6B35,#FF8C42);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 14px rgba(255,107,53,.4)}
        .wa-float-icon svg{width:22px;height:22px;display:block}
        .wa-float-text{white-space:nowrap}

        /* Mobile nav toggle */
        .nav-toggler{display:none;background:none;border:1px solid rgba(255,255,255,.12);border-radius:10px;color:rgba(255,255,255,.7);font-size:1.1rem;padding:.35rem .55rem;cursor:pointer;transition:all .2s}
        .nav-toggler:hover{color:#fff;border-color:rgba(255,255,255,.3)}
        .nav-toggler.is-open .bi-list{display:none}
        .nav-toggler .bi-x-lg{display:none}
        .nav-toggler.is-open .bi-x-lg{display:inline}
        .nav-links-collapse{display:flex;align-items:center;gap:.5rem}

        /* Mobile overlay */
        .mobile-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:98;opacity:0;transition:opacity .3s}
        .mobile-overlay.show{display:block;opacity:1}

        @@media(max-width:991px){
            .nav-toggler{display:flex;align-items:center;justify-content:center;z-index:102}
            .nav-links-collapse{
                display:flex;flex-direction:column;
                position:fixed;top:0;right:-100%;width:min(320px,85vw);height:100vh;
                background:linear-gradient(180deg,#0c1121 0%,#0a0f1e 100%);
                padding:5rem 1.5rem 2rem;gap:0;
                z-index:101;
                transition:right .35s cubic-bezier(.4,0,.2,1);
                box-shadow:-8px 0 40px rgba(0,0,0,.5);
                overflow-y:auto;
            }
            .nav-links-collapse.show{right:0}
            .nav-link-item{
                padding:.85rem 0;width:100%;text-align:left;
                font-size:.92rem;font-weight:600;
                color:rgba(255,255,255,.6);
                border-bottom:1px solid rgba(255,255,255,.06);
                transition:color .18s,padding-left .18s;
            }
            .nav-link-item:hover,.nav-link-item.active{color:#fff;padding-left:.3rem}
            .nav-link-item.active{color:#FF8C42}
            .nav-links-collapse .mobile-nav-btns{
                display:flex;flex-direction:column;gap:.6rem;
                margin-top:auto;padding-top:1.5rem;
                border-top:1px solid rgba(255,255,255,.06);
                width:100%;
            }
            .nav-links-collapse .nav-btn{
                width:100%;text-align:center;
                padding:.7rem 1rem;border-radius:10px;font-size:.88rem;
            }
            .nav-links-collapse .nav-btn-primary{
                box-shadow:0 4px 20px rgba(255,107,53,.3);
            }
            body.nav-open{overflow:hidden}
        }
        @@media(max-width:768px){
            .wa-float{padding:.4rem .5rem .4rem .4rem;font-size:.75rem}
            .wa-float-icon{width:36px;height:36px;border-radius:10px}
            .wa-float-icon svg{width:18px;height:18px}
            .wa-float-text{display:none}
            .wa-float.is-text-visible .wa-float-text{display:inline}
            .page-hero{padding:6rem 0 2rem}
            .footer-grid{grid-template-columns:1fr 1fr;gap:1.5rem 1rem}
        }
        @@media(max-width:480px){
            .page-hero h1{font-size:1.6rem}
            .page-hero-sub{font-size:.9rem}
            .cta-bar{padding:3rem 0 4rem}
            .cta-box{padding:2rem 1rem;border-radius:18px}
            .cta-box h2{font-size:1.35rem}
            .footer-grid{grid-template-columns:1fr;gap:1.25rem;text-align:center}
            .footer-col .d-flex{justify-content:center}
            .lp-footer{padding:2rem 0 1.5rem}
            .footer-heading{margin-bottom:.4rem;font-size:.7rem}
            .lp-footer a{font-size:.8rem}
            .footer-bottom{font-size:.72rem;margin-top:1.25rem;padding-top:1rem}
        }
        @yield('styles')
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <a href="https://wa.me/905078928490" target="_blank" rel="noopener noreferrer" class="wa-float" aria-label="WhatsApp">
        <span class="wa-float-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M21 11.5a8.5 8.5 0 0 1-11.4 8.1L3 20.5l.9-5.7A8.5 8.5 0 1 1 21 11.5z" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                <path d="M8 12h8M8 9h5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
            </svg>
        </span>
        <span class="wa-float-text">{{ $isTr ? 'İletişime Geç' : 'Contact Us' }}</span>
    </a>

    <div class="mobile-overlay" id="mobileOverlay"></div>

    <nav class="lp-nav" id="lpNav">
        <div class="container d-flex align-items-center justify-content-between position-relative">
            <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <div class="logo-icon"><i class="bi bi-qr-code-scan"></i></div>
                <span class="logo-text">Sipariş Masanda</span>
            </a>
            <button type="button" class="nav-toggler" id="navToggler" aria-label="Menu">
                <i class="bi bi-list"></i>
                <i class="bi bi-x-lg"></i>
            </button>
            <div class="nav-links-collapse" id="navCollapse">
                <a href="{{ route('features') }}" class="nav-link-item {{ request()->routeIs('features') ? 'active' : '' }}">
                    <i class="bi bi-grid-3x3-gap me-2 d-lg-none" style="font-size:.85rem;opacity:.45"></i>{{ $isTr ? 'Özellikler' : 'Features' }}
                </a>
                <a href="{{ route('pricing') }}" class="nav-link-item {{ request()->routeIs('pricing') ? 'active' : '' }}">
                    <i class="bi bi-tag me-2 d-lg-none" style="font-size:.85rem;opacity:.45"></i>{{ $isTr ? 'Fiyatlar' : 'Pricing' }}
                </a>
                <a href="{{ route('about') }}" class="nav-link-item {{ request()->routeIs('about') ? 'active' : '' }}">
                    <i class="bi bi-building me-2 d-lg-none" style="font-size:.85rem;opacity:.45"></i>{{ $isTr ? 'Hakkımızda' : 'About' }}
                </a>
                <a href="{{ route('contact') }}" class="nav-link-item {{ request()->routeIs('contact') ? 'active' : '' }}">
                    <i class="bi bi-envelope me-2 d-lg-none" style="font-size:.85rem;opacity:.45"></i>{{ $isTr ? 'İletişim' : 'Contact' }}
                </a>
                <div class="d-none d-lg-flex gap-2 ms-lg-2">
                    <a href="{{ route('login') }}" class="nav-btn nav-btn-ghost">{{ $isTr ? 'Giriş Yap' : 'Sign In' }}</a>
                    <a href="{{ route('register') }}" class="nav-btn nav-btn-primary">{{ $isTr ? 'Ücretsiz Başla' : 'Start Free' }}</a>
                </div>
                <div class="mobile-nav-btns d-lg-none">
                    <a href="{{ route('login') }}" class="nav-btn nav-btn-ghost">{{ $isTr ? 'Giriş Yap' : 'Sign In' }}</a>
                    <a href="{{ route('register') }}" class="nav-btn nav-btn-primary">{{ $isTr ? 'Ücretsiz Başla' : 'Start Free' }}</a>
                </div>
            </div>
        </div>
    </nav>

    @yield('content')

    <footer class="lp-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-heading">{{ $isTr ? 'Ürün' : 'Product' }}</div>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('features') }}">{{ $isTr ? 'Özellikler' : 'Features' }}</a>
                        <a href="{{ route('pricing') }}">{{ $isTr ? 'Fiyatlar' : 'Pricing' }}</a>
                        <a href="{{ route('demo') }}">{{ $isTr ? 'Canlı Demo' : 'Live Demo' }}</a>
                    </div>
                </div>
                <div class="footer-col">
                    <div class="footer-heading">{{ $isTr ? 'Şirket' : 'Company' }}</div>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('about') }}">{{ $isTr ? 'Hakkımızda' : 'About' }}</a>
                        <a href="{{ route('contact') }}">{{ $isTr ? 'İletişim' : 'Contact' }}</a>
                    </div>
                </div>
                <div class="footer-col">
                    <div class="footer-heading">{{ $isTr ? 'Yasal' : 'Legal' }}</div>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('privacy') }}">{{ $isTr ? 'Gizlilik Politikası' : 'Privacy Policy' }}</a>
                        <a href="{{ route('terms') }}">{{ $isTr ? 'Kullanım Koşulları' : 'Terms of Use' }}</a>
                    </div>
                </div>
                <div class="footer-col">
                    <div class="footer-heading">{{ $isTr ? 'Hesap' : 'Account' }}</div>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('login') }}">{{ $isTr ? 'Giriş Yap' : 'Sign In' }}</a>
                        <a href="{{ route('register') }}">{{ $isTr ? 'Ücretsiz Başla' : 'Start Free' }}</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom text-center">
                <span>&copy; {{ date('Y') }} Sipariş Masanda &mdash; {{ $isTr ? 'Tüm hakları saklıdır.' : 'All rights reserved.' }}</span>
            </div>
        </div>
    </footer>

    <script>
    window.addEventListener('scroll',function(){
        document.getElementById('lpNav').classList.toggle('scrolled',window.scrollY>40);
    });
    (function(){
        var toggler=document.getElementById('navToggler');
        var collapse=document.getElementById('navCollapse');
        var overlay=document.getElementById('mobileOverlay');
        function toggleNav(){
            var open=collapse.classList.toggle('show');
            toggler.classList.toggle('is-open',open);
            overlay.classList.toggle('show',open);
            document.body.classList.toggle('nav-open',open);
        }
        function closeNav(){
            collapse.classList.remove('show');
            toggler.classList.remove('is-open');
            overlay.classList.remove('show');
            document.body.classList.remove('nav-open');
        }
        toggler.addEventListener('click',toggleNav);
        overlay.addEventListener('click',closeNav);
        collapse.querySelectorAll('.nav-link-item').forEach(function(l){l.addEventListener('click',closeNav)});
    })();
    (function(){
        var wf=document.querySelector('.wa-float');
        if(!wf)return;
        wf.addEventListener('click',function(e){
            if(window.matchMedia('(max-width:768px)').matches && !wf.classList.contains('is-text-visible')){
                e.preventDefault();
                wf.classList.add('is-text-visible');
                setTimeout(function(){wf.classList.remove('is-text-visible')},3000);
            }
        });
    })();
    </script>
    @yield('scripts')
</body>
</html>
