@php
    $locale = app()->getLocale();
    $isTr = $locale === 'tr';
    $title = $isTr
        ? 'Sipariş Masanda — Restoranınız İçin Dijital QR Menü Sistemi'
        : 'Siparis Masanda — Digital QR Menu Platform For Restaurants';
    $description = $isTr
        ? 'Sipariş Masanda ile restoranınızın dijital menüsünü dakikalar içinde oluşturun. QR kod ile masaya taşıyın, müşteri değerlendirmeleri alın ve işletmenizi dijitale taşıyın.'
        : 'Create your restaurant digital menu in minutes with Siparis Masanda. Publish with QR codes, collect guest reviews, and manage your menu online.';
    $keywords = $isTr
        ? 'dijital menü, QR menü, restoran menü, sipariş sistemi, QR kod menü, restoran yönetimi, dijital menü oluştur'
        : 'digital menu, qr menu, restaurant menu, qr code menu, restaurant management, menu software';
    $canonical = url('/') . ($locale === config('app.fallback_locale', 'en') ? '' : '?lang=' . $locale);
    $shareImage = asset('og-cover.svg');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="{{ $keywords }}">
    <meta name="author" content="Sipariş Masanda">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="theme-color" content="#0a0f1e">
    <link rel="canonical" href="{{ $canonical }}">
    <link rel="alternate" hreflang="tr" href="{{ url('/') }}?lang=tr">
    <link rel="alternate" hreflang="en" href="{{ url('/') }}?lang=en">
    <link rel="alternate" hreflang="x-default" href="{{ url('/') }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:site_name" content="Sipariş Masanda">
    <meta property="og:locale" content="{{ $isTr ? 'tr_TR' : 'en_US' }}">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $shareImage }}">

    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "SoftwareApplication",
        "name": "Sipariş Masanda",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "url": "{{ url('/') }}",
        "image": "{{ $shareImage }}",
        "description": "{{ $description }}",
        "offers": {
            "@@type": "Offer",
            "price": "0",
            "priceCurrency": "TRY"
        },
        "publisher": {
            "@@type": "Organization",
            "name": "Sipariş Masanda",
            "url": "{{ url('/') }}"
        }
    }
    </script>

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
        section{position:relative;z-index:1}

        /* Navbar */
        .lp-nav{position:fixed;top:0;left:0;right:0;z-index:100;padding:.9rem 0;transition:background .3s,box-shadow .3s}
        .lp-nav.scrolled{background:rgba(10,15,30,.92);backdrop-filter:blur(16px);box-shadow:0 1px 0 rgba(255,255,255,.06)}
        .lp-nav .logo-icon{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#FF6B35,#FF8C42);display:flex;align-items:center;justify-content:center;font-size:1rem;color:#fff;box-shadow:0 4px 16px rgba(255,107,53,.4)}
        .lp-nav .logo-text{font-size:.95rem;font-weight:800;color:#fff;letter-spacing:-.01em}
        .nav-btn{padding:.45rem 1.2rem;border-radius:9px;font-size:.82rem;font-weight:600;text-decoration:none;transition:all .18s}
        .nav-btn-ghost{color:rgba(255,255,255,.7);border:1px solid rgba(255,255,255,.12)}
        .nav-btn-ghost:hover{color:#fff;border-color:rgba(255,255,255,.25);background:rgba(255,255,255,.06)}
        .nav-btn-primary{background:linear-gradient(135deg,#FF6B35,#FF8C42);color:#fff;border:none;box-shadow:0 4px 16px rgba(255,107,53,.35)}
        .nav-btn-primary:hover{color:#fff;transform:translateY(-1px);box-shadow:0 6px 24px rgba(255,107,53,.5)}

        /* Hero */
        .hero{min-height:100vh;display:flex;align-items:center;padding-top:5rem}
        .hero-badge{display:inline-flex;align-items:center;gap:.4rem;padding:.35rem .85rem;border-radius:999px;background:rgba(255,107,53,.1);border:1px solid rgba(255,107,53,.2);font-size:.75rem;font-weight:600;color:#FF8C42;margin-bottom:1.5rem}
        .hero-badge .dot{width:6px;height:6px;border-radius:50%;background:#4ade80;animation:pulse 2s infinite}
        @@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
        .hero h1{font-size:clamp(2.2rem,5vw,3.8rem);font-weight:900;line-height:1.08;letter-spacing:-.03em;margin-bottom:1.25rem}
        .hero h1 .accent{background:linear-gradient(90deg,#FF6B35,#FFB347);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        .hero-sub{font-size:1.05rem;color:rgba(255,255,255,.5);line-height:1.7;max-width:520px;margin-bottom:2.5rem}
        .hero-btns{display:flex;gap:.75rem;flex-wrap:wrap}
        .hero-btn-primary{display:inline-flex;align-items:center;gap:.5rem;padding:.85rem 2rem;border-radius:12px;background:linear-gradient(135deg,#FF6B35,#FF8C42);color:#fff;font-weight:700;font-size:.95rem;text-decoration:none;box-shadow:0 8px 32px rgba(255,107,53,.35);transition:all .2s;border:none}
        .hero-btn-primary:hover{color:#fff;transform:translateY(-2px);box-shadow:0 12px 40px rgba(255,107,53,.5)}
        .hero-btn-outline{display:inline-flex;align-items:center;gap:.5rem;padding:.85rem 2rem;border-radius:12px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.8);font-weight:600;font-size:.95rem;text-decoration:none;transition:all .2s}
        .hero-btn-outline:hover{color:#fff;background:rgba(255,255,255,.1);border-color:rgba(255,255,255,.25);transform:translateY(-2px)}

        .hero-visual{position:relative}
        .hero-mockup{width:100%;max-width:380px;margin:0 auto;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:24px;padding:1.5rem;backdrop-filter:blur(10px)}
        .mock-header{text-align:center;margin-bottom:1.25rem}
        .mock-logo{width:40px;height:40px;border-radius:11px;background:linear-gradient(135deg,#FF6B35,#FF8C42);display:inline-flex;align-items:center;justify-content:center;font-size:1.1rem;color:#fff;margin-bottom:.5rem}
        .mock-title{font-size:.95rem;font-weight:800;color:#fff}
        .mock-sub{font-size:.72rem;color:rgba(255,255,255,.4)}
        .mock-item{display:flex;align-items:center;gap:.7rem;padding:.7rem .8rem;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);border-radius:12px;margin-bottom:.5rem}
        .mock-thumb{width:40px;height:40px;border-radius:8px;background:rgba(255,107,53,.1);display:flex;align-items:center;justify-content:center;font-size:.9rem;color:#FF6B35;flex-shrink:0}
        .mock-name{font-size:.78rem;font-weight:600;color:rgba(255,255,255,.8)}
        .mock-desc{font-size:.65rem;color:rgba(255,255,255,.3)}
        .mock-price{font-size:.82rem;font-weight:800;color:#FF6B35;margin-left:auto;white-space:nowrap}
        .mock-glow{position:absolute;width:200px;height:200px;border-radius:50%;background:rgba(255,107,53,.12);filter:blur(80px);top:50%;left:50%;transform:translate(-50%,-50%);pointer-events:none}

        /* Features */
        .features{padding:6rem 0}
        .section-badge{display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .75rem;border-radius:999px;font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;margin-bottom:.75rem}
        .section-title{font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:900;letter-spacing:-.03em;line-height:1.1;margin-bottom:.6rem}
        .section-sub{font-size:.95rem;color:rgba(255,255,255,.45);max-width:540px;line-height:1.7}
        .feat-card{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:18px;padding:1.75rem;transition:all .25s;height:100%}
        .feat-card:hover{background:rgba(255,255,255,.05);border-color:rgba(255,107,53,.15);transform:translateY(-4px);box-shadow:0 12px 40px rgba(0,0,0,.2)}
        .feat-icon{width:48px;height:48px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;margin-bottom:1rem}
        .feat-card h3{font-size:.95rem;font-weight:700;color:#fff;margin-bottom:.4rem}
        .feat-card p{font-size:.82rem;color:rgba(255,255,255,.45);line-height:1.6;margin:0}

        /* How it Works */
        .how{padding:6rem 0}
        .step-num{width:56px;height:56px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:900;margin:0 auto 1rem;position:relative}
        .step-num::after{content:'';position:absolute;inset:-3px;border-radius:18px;border:2px dashed rgba(255,107,53,.2);animation:spin 20s linear infinite}
        @@keyframes spin{to{transform:rotate(360deg)}}
        .step-card{text-align:center;padding:1.5rem}
        .step-card h3{font-size:.95rem;font-weight:700;color:#fff;margin-bottom:.35rem}
        .step-card p{font-size:.82rem;color:rgba(255,255,255,.4);line-height:1.6}
        .step-arrow{display:flex;align-items:center;justify-content:center;color:rgba(255,107,53,.3);font-size:1.5rem;padding-top:2rem}

        /* CTA */
        .cta-section{padding:5rem 0 6rem}
        .cta-box{background:linear-gradient(135deg,rgba(255,107,53,.1),rgba(108,92,231,.08));border:1px solid rgba(255,107,53,.15);border-radius:24px;padding:3.5rem 2rem;text-align:center;position:relative;overflow:hidden}
        .cta-box::before{content:'';position:absolute;top:-60px;right:-60px;width:200px;height:200px;border-radius:50%;background:rgba(255,107,53,.08);filter:blur(60px)}
        .cta-box h2{font-size:clamp(1.6rem,3vw,2.2rem);font-weight:900;letter-spacing:-.02em;margin-bottom:.5rem;position:relative}
        .cta-box p{color:rgba(255,255,255,.5);font-size:.95rem;margin-bottom:2rem;position:relative}

        /* Footer */
        .lp-footer{border-top:1px solid rgba(255,255,255,.06);padding:2rem 0;text-align:center}
        .lp-footer p{font-size:.78rem;color:rgba(255,255,255,.3);margin:0}
        .lp-footer a{color:#FF8C42;text-decoration:none}

        /* Fixed WhatsApp - sol taraf, özel ikon */
        .wa-float{
            position:fixed;left:0;top:50%;transform:translateY(-50%);
            z-index:99;display:flex;align-items:center;
            background:linear-gradient(135deg,#0a0f1e 0%,#151b2d 100%);
            border:1px solid rgba(255,107,53,.25);
            border-left:none;
            border-radius:0 14px 14px 0;
            padding:.5rem .6rem .5rem .5rem;
            box-shadow:0 4px 24px rgba(0,0,0,.35), 0 0 0 1px rgba(255,255,255,.04);
            transition:all .25s ease;
            text-decoration:none;
            color:#fff;
            font-size:.8rem;font-weight:600;
            gap:.5rem;
        }
        .wa-float:hover{color:#fff;padding-left:.85rem;border-color:rgba(255,107,53,.45);box-shadow:0 6px 32px rgba(255,107,53,.15)}
        .wa-float-icon{
            width:42px;height:42px;
            border-radius:12px;
            background:linear-gradient(145deg,#FF6B35,#FF8C42);
            display:flex;align-items:center;justify-content:center;
            flex-shrink:0;
            box-shadow:0 4px 14px rgba(255,107,53,.4);
        }
        .wa-float-icon svg{width:22px;height:22px;display:block}
        .wa-float-text{white-space:nowrap}
        @@media(max-width:768px){
            .wa-float{padding:.4rem .5rem .4rem .4rem;font-size:.75rem}
            .wa-float-icon{width:36px;height:36px;border-radius:10px}
            .wa-float-icon svg{width:18px;height:18px}
            .wa-float-text{display:none}
            .wa-float:hover .wa-float-text{display:inline}
        }

        @@media(max-width:768px){
            .hero-visual{margin-top:3rem}
            .step-arrow{transform:rotate(90deg);padding-top:0}
            .hero{min-height:auto;padding-top:6rem;padding-bottom:3rem}
            .features{padding:4rem 0}
            .how{padding:4rem 0}
            .cta-section{padding:3rem 0 4rem}
            .cta-box{padding:2.5rem 1.5rem}
            .lp-nav .container{padding-left:1rem;padding-right:1rem}
            .lp-nav .logo-text{font-size:.85rem}
            .nav-btn{padding:.4rem .9rem;font-size:.78rem}
        }
        @@media(max-width:576px){
            .hero{padding-top:5rem;padding-bottom:2rem}
            .hero h1{font-size:1.75rem}
            .hero-sub{font-size:.9rem;margin-bottom:1.75rem}
            .hero-btns{flex-direction:column;gap:.5rem}
            .hero-btn-primary,.hero-btn-outline{width:100%;justify-content:center;padding:.75rem 1.25rem;font-size:.88rem}
            .hero-mockup{padding:1rem;border-radius:18px}
            .mock-item{padding:.5rem .6rem}
            .mock-name{font-size:.72rem}
            .mock-price{font-size:.75rem}
            .features{padding:3rem 0}
            .section-title{font-size:1.5rem}
            .section-sub{font-size:.88rem}
            .feat-card{padding:1.25rem}
            .feat-card h3{font-size:.9rem}
            .feat-card p{font-size:.8rem}
            .how{padding:3rem 0}
            .step-card{padding:1rem .5rem}
            .step-card:not(:last-child){margin-bottom:1.5rem}
            .step-num{width:48px;height:48px;font-size:1.25rem}
            .cta-section{padding:2.5rem 0 3rem}
            .cta-box{padding:2rem 1rem;border-radius:18px}
            .cta-box h2{font-size:1.35rem}
            .cta-box p{font-size:.88rem;margin-bottom:1.5rem}
            .lp-footer{padding:1.5rem 0}
            .lp-footer p{font-size:.75rem}
            .lp-nav .logo-text{font-size:.8rem}
            .nav-btn{padding:.35rem .75rem;font-size:.75rem}
        }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <!-- Sabit WhatsApp - İletişime Geç -->
    <a href="https://wa.me/905078928490" target="_blank" rel="noopener noreferrer" class="wa-float" aria-label="WhatsApp ile iletişime geç">
        <span class="wa-float-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M21 11.5a8.5 8.5 0 0 1-11.4 8.1L3 20.5l.9-5.7A8.5 8.5 0 1 1 21 11.5z" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                <path d="M8 12h8M8 9h5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
            </svg>
        </span>
        <span class="wa-float-text">İletişime Geç</span>
    </a>

    <!-- Navbar -->
    <nav class="lp-nav" id="lpNav">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <div class="logo-icon"><i class="bi bi-qr-code-scan"></i></div>
                <span class="logo-text">Sipariş Masanda</span>
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('login') }}" class="nav-btn nav-btn-ghost">Giriş Yap</a>
                <a href="{{ route('register') }}" class="nav-btn nav-btn-primary">Hemen Başla</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="container">
            @if(session('demo_unavailable'))
                <div class="alert mb-3" role="alert" style="background:rgba(251,191,36,.15);border:1px solid rgba(251,191,36,.3);color:#fbbf24;border-radius:12px">
                    Demo menü henüz yüklenmemiş. Yönetici <code>php artisan db:seed --class=DemoRestaurantSeeder</code> komutunu çalıştırabilir.
                </div>
            @endif
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-badge"><span class="dot"></span> Dijital Menü Platformu</div>
                    <h1>Restoranınızı<br><span class="accent">Dijitale Taşıyın</span></h1>
                    <p class="hero-sub">
                        Dakikalar içinde profesyonel dijital menünüzü oluşturun.
                        QR kod ile masalara taşıyın, müşterilerinizin telefonundan menünüze ulaşmasını sağlayın.
                    </p>
                    <div class="hero-btns">
                        <a href="{{ route('register') }}" class="hero-btn-primary">
                            <i class="bi bi-rocket-takeoff"></i> Ücretsiz Başla
                        </a>
                        <a href="{{ route('demo') }}" class="hero-btn-outline">
                            <i class="bi bi-eye"></i> Test Sayfasına Bak
                        </a>
                        <a href="{{ route('login') }}" class="hero-btn-outline">
                            <i class="bi bi-box-arrow-in-right"></i> Giriş Yap
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 hero-visual">
                    <div class="hero-mockup">
                        <div class="mock-glow"></div>
                        <div class="mock-header">
                            <div class="mock-logo"><i class="bi bi-qr-code-scan"></i></div>
                            <div class="mock-title">Lezzet Dünyası</div>
                            <div class="mock-sub">Dijital Menü</div>
                        </div>
                        <div class="mock-item">
                            <div class="mock-thumb"><i class="bi bi-cup-hot"></i></div>
                            <div><div class="mock-name">Türk Kahvesi</div><div class="mock-desc">Geleneksel lezzet</div></div>
                            <div class="mock-price">35 ₺</div>
                        </div>
                        <div class="mock-item">
                            <div class="mock-thumb"><i class="bi bi-fire"></i></div>
                            <div><div class="mock-name">Izgara Köfte</div><div class="mock-desc">Pilav ve salata ile</div></div>
                            <div class="mock-price">120 ₺</div>
                        </div>
                        <div class="mock-item">
                            <div class="mock-thumb"><i class="bi bi-snow2"></i></div>
                            <div><div class="mock-name">Künefe</div><div class="mock-desc">Antep fıstıklı</div></div>
                            <div class="mock-price">85 ₺</div>
                        </div>
                        <div class="mock-item">
                            <div class="mock-thumb"><i class="bi bi-droplet"></i></div>
                            <div><div class="mock-name">Limonata</div><div class="mock-desc">Ev yapımı, naneli</div></div>
                            <div class="mock-price">45 ₺</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features" id="features">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-badge" style="background:rgba(255,107,53,.1);color:#FF8C42">
                    <i class="bi bi-stars"></i> Özellikler
                </div>
                <h2 class="section-title">Restoranınız İçin<br>İhtiyacınız Olan Her Şey</h2>
                <p class="section-sub mx-auto">Basit arayüz, güçlü özellikler. Teknik bilgi gerektirmeden profesyonel dijital menünüzü yönetin.</p>
            </div>
            <div class="row g-4">
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="feat-card">
                        <div class="feat-icon" style="background:rgba(255,107,53,.12);color:#FF6B35"><i class="bi bi-qr-code"></i></div>
                        <h3>QR Kod Menü</h3>
                        <p>Baskıya hazır QR kod oluşturun. Müşterileriniz telefonlarıyla tarayıp menünüzü anında görsün.</p>
                    </article>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="feat-card">
                        <div class="feat-icon" style="background:rgba(16,185,129,.12);color:#10b981"><i class="bi bi-grid-3x3-gap"></i></div>
                        <h3>Kategori & Ürün Yönetimi</h3>
                        <p>Sınırsız kategori ve ürün. Fotoğraf, açıklama, fiyat — sürükle-bırak sıralama ile düzenleyin.</p>
                    </article>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="feat-card">
                        <div class="feat-icon" style="background:rgba(251,191,36,.12);color:#FBBF24"><i class="bi bi-star"></i></div>
                        <h3>Müşteri Değerlendirmeleri</h3>
                        <p>Müşterileriniz QR menü üzerinden restoran değerlendirmesi bıraksın. Puanlarınızı takip edin.</p>
                    </article>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="feat-card">
                        <div class="feat-icon" style="background:rgba(99,102,241,.12);color:#6366f1"><i class="bi bi-people"></i></div>
                        <h3>Çoklu Kullanıcı</h3>
                        <p>Ekibinizi davet edin. Owner, admin ve personel rolleriyle yetkilendirme yapın.</p>
                    </article>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="feat-card">
                        <div class="feat-icon" style="background:rgba(236,72,153,.12);color:#EC4899"><i class="bi bi-instagram"></i></div>
                        <h3>Sosyal Medya Entegrasyonu</h3>
                        <p>Instagram, Facebook, WhatsApp hesaplarınızı ekleyin. Menü sayfanızda otomatik gösterilsin.</p>
                    </article>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="feat-card">
                        <div class="feat-icon" style="background:rgba(168,85,247,.12);color:#A855F7"><i class="bi bi-phone"></i></div>
                        <h3>Mobil Uyumlu</h3>
                        <p>Tüm cihazlarda kusursuz görüntülenen menü. Müşterileriniz her yerden erişsin.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section class="how" id="how">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-badge" style="background:rgba(108,92,231,.1);color:#a29bfe">
                    <i class="bi bi-lightning-charge"></i> Nasıl Çalışır?
                </div>
                <h2 class="section-title">3 Adımda Başlayın</h2>
                <p class="section-sub mx-auto">Dakikalar içinde dijital menünüz hazır.</p>
            </div>
            <div class="row align-items-start">
                <div class="col-12 col-md-4">
                    <div class="step-card">
                        <div class="step-num" style="background:rgba(255,107,53,.12);color:#FF6B35">1</div>
                        <h3>Kayıt Olun</h3>
                        <p>Firma ve restoran bilgilerinizi girin. Hesabınız anında oluşturulur.</p>
                    </div>
                </div>
                <div class="col-12 col-md-1 d-none d-md-flex step-arrow"><i class="bi bi-arrow-right"></i></div>
                <div class="col-12 col-md-3">
                    <div class="step-card">
                        <div class="step-num" style="background:rgba(16,185,129,.12);color:#10b981">2</div>
                        <h3>Menünüzü Oluşturun</h3>
                        <p>Kategorileri ve ürünleri ekleyin. Fotoğraf yükleyin, fiyatları belirleyin.</p>
                    </div>
                </div>
                <div class="col-12 col-md-1 d-none d-md-flex step-arrow"><i class="bi bi-arrow-right"></i></div>
                <div class="col-12 col-md-3">
                    <div class="step-card">
                        <div class="step-num" style="background:rgba(251,191,36,.12);color:#FBBF24">3</div>
                        <h3>QR Kodu Paylaşın</h3>
                        <p>QR kodunuzu yazdırıp masalara yerleştirin. Müşterileriniz tarayıp menüyü görsün.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-box">
                <h2>Restoranınızı Dijitale Taşımaya<br>Hazır mısınız?</h2>
                <p>Hemen ücretsiz hesap oluşturun ve dijital menünüzü dakikalar içinde yayına alın.</p>
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a href="{{ route('register') }}" class="hero-btn-primary" style="position:relative">
                        <i class="bi bi-rocket-takeoff"></i> Ücretsiz Hesap Oluştur
                    </a>
                    <a href="{{ route('demo') }}" class="hero-btn-outline" style="position:relative">
                        <i class="bi bi-eye"></i> Test Sayfasına Bak
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="lp-footer">
        <div class="container">
            <p>© {{ date('Y') }} <a href="{{ url('/') }}">Sipariş Masanda</a> — Tüm hakları saklıdır.</p>
        </div>
    </footer>

    <script>
    window.addEventListener('scroll',function(){
        document.getElementById('lpNav').classList.toggle('scrolled',window.scrollY>40);
    });
    </script>
</body>
</html>
