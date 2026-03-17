@extends('layouts.public')

@php
    $locale = app()->getLocale();
    $isTr = $locale === 'tr';
    $tenantCount = \Illuminate\Support\Facades\DB::table('tenants')->where('is_active', true)->count();
    $tenantLabel = max($tenantCount, 10) . '+';
@endphp

@section('title', $isTr
    ? 'Restoran ve Kafeler İçin Dijital QR Menü Sistemi | Sipariş Masanda'
    : 'Digital QR Menu System for Restaurants & Cafes | Siparis Masanda')

@section('meta_description', $isTr
    ? 'Restoranın için dakikalar içinde QR menü oluştur. Ücretsiz başla, kağıt menü masrafına son ver. 3 adımda dijital menün hazır.'
    : 'Create a QR menu for your restaurant in minutes. Start free, eliminate paper menu costs. Your digital menu ready in 3 steps.')

@section('meta_keywords', $isTr
    ? 'dijital menü, QR menü, karekod menü, restoran menü sistemi, ücretsiz dijital menü, temassız menü, online menü oluştur, kafe menüsü'
    : 'digital menu, QR menu, qr code menu, restaurant menu system, free digital menu, contactless menu, online menu creator, cafe menu')

@section('canonical', url('/'))

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "HowTo",
    "name": "{{ $isTr ? 'QR Menü Nasıl Oluşturulur?' : 'How to Create a QR Menu?' }}",
    "description": "{{ $isTr ? 'Dakikalar içinde ücretsiz dijital QR menü oluşturun.' : 'Create a free digital QR menu in minutes.' }}",
    "totalTime": "PT3M",
    "tool": {
        "@@type": "HowToTool",
        "name": "Sipariş Masanda"
    },
    "step": [
        {
            "@@type": "HowToStep",
            "position": 1,
            "name": "{{ $isTr ? 'Ücretsiz Kayıt Olun' : 'Register for Free' }}",
            "text": "{{ $isTr ? 'Restoran bilgilerinizi girin. Ücretsiz dijital menü hesabınız anında oluşturulur.' : 'Enter your restaurant details. Your free digital menu account is created instantly.' }}",
            "url": "{{ route('register') }}"
        },
        {
            "@@type": "HowToStep",
            "position": 2,
            "name": "{{ $isTr ? 'Menünüzü Oluşturun' : 'Create Your Menu' }}",
            "text": "{{ $isTr ? 'Kategorileri ve ürünleri ekleyin. Fotoğraf yükleyin, fiyatları belirleyin.' : 'Add categories and products. Upload photos, set prices.' }}"
        },
        {
            "@@type": "HowToStep",
            "position": 3,
            "name": "{{ $isTr ? 'QR Kodu Paylaşın' : 'Share Your QR Code' }}",
            "text": "{{ $isTr ? 'QR kodunuzu yazdırıp masalara yerleştirin. Müşterileriniz QR kodu tarayıp menüyü görsün.' : 'Print and place your QR code on tables. Your customers scan the QR code and view the menu.' }}"
        }
    ]
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {
            "@@type": "Question",
            "name": "{{ $isTr ? 'QR menü nedir ve nasıl çalışır?' : 'What is a QR menu and how does it work?' }}",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "{{ $isTr ? 'QR menü, restoranınızın menüsünü dijital olarak sunan ve müşterilerin telefonlarıyla QR kodu tarayarak erişebildiği bir sistemdir. Kağıt menü yerine anlık güncellenebilir bir dijital menü kullanırsınız.' : 'A QR menu is a system that digitally presents your restaurant menu, allowing customers to access it by scanning a QR code with their phones.' }}"
            }
        },
        {
            "@@type": "Question",
            "name": "{{ $isTr ? 'QR menü kurmak için teknik bilgi gerekiyor mu?' : 'Do I need technical knowledge to set up a QR menu?' }}",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "{{ $isTr ? 'Hayır, teknik bilgi gerekmez. Sipariş Masanda ile 3 adımda dijital menünüzü oluşturabilirsiniz: kayıt olun, ürünlerinizi ekleyin, QR kodunuzu yazdırın.' : 'No, no technical knowledge is needed. With Siparis Masanda you can create your digital menu in 3 steps: register, add your products, print your QR code.' }}"
            }
        },
        {
            "@@type": "Question",
            "name": "{{ $isTr ? 'Dijital menü ücretsiz mi?' : 'Is the digital menu free?' }}",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "{{ $isTr ? 'Evet, Sipariş Masanda ücretsiz plan sunmaktadır. Sınırsız kategori ve ürün ekleyebilir, QR kodunuzu oluşturabilirsiniz. Kredi kartı gerekmez.' : 'Yes, Siparis Masanda offers a free plan. You can add unlimited categories and products, and create your QR code. No credit card required.' }}"
            }
        },
        {
            "@@type": "Question",
            "name": "{{ $isTr ? 'Müşteriler uygulama indirmek zorunda mı?' : 'Do customers need to download an app?' }}",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "{{ $isTr ? 'Hayır, müşterileriniz herhangi bir uygulama indirmeden telefonlarının kamerasıyla QR kodu tarayarak menüye doğrudan erişebilir.' : 'No, your customers can directly access the menu by scanning the QR code with their phone camera without downloading any app.' }}"
            }
        },
        {
            "@@type": "Question",
            "name": "{{ $isTr ? 'Menü fiyatlarını anlık güncelleyebilir miyim?' : 'Can I update menu prices instantly?' }}",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "{{ $isTr ? 'Evet, yönetim panelinden ürün fiyatlarını, açıklamalarını ve fotoğraflarını istediğiniz zaman anında güncelleyebilirsiniz. Değişiklikler hemen yansır.' : 'Yes, you can instantly update product prices, descriptions, and photos from the management panel at any time. Changes are reflected immediately.' }}"
            }
        }
    ]
}
</script>
@endsection

@section('styles')
        /* Hero */
        .hero{min-height:100vh;display:flex;align-items:center;padding-top:5rem}
        .hero-badge{display:inline-flex;align-items:center;gap:.4rem;padding:.35rem .85rem;border-radius:999px;background:rgba(255,107,53,.1);border:1px solid rgba(255,107,53,.2);font-size:.75rem;font-weight:600;color:#FF8C42;margin-bottom:1.5rem}
        .hero-badge .dot{width:6px;height:6px;border-radius:50%;background:#4ade80;animation:pulse 2s infinite}
        @@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
        .hero h1 .accent{background:linear-gradient(90deg,#FF6B35,#FFB347);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        .hero h1{font-size:clamp(2.2rem,5vw,3.8rem);font-weight:900;line-height:1.08;letter-spacing:-.03em;margin-bottom:1.25rem}
        .hero-sub{font-size:1.05rem;color:rgba(255,255,255,.78);line-height:1.7;max-width:520px;margin-bottom:2.5rem}
        .hero-btns{display:flex;gap:.75rem;flex-wrap:wrap}

        .hero-visual{position:relative}
        .hero-mockup{width:100%;max-width:380px;margin:0 auto;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:24px;padding:1.5rem;backdrop-filter:blur(10px)}
        .mock-header{text-align:center;margin-bottom:1.25rem}
        .mock-logo{width:40px;height:40px;border-radius:11px;background:linear-gradient(135deg,#FF6B35,#FF8C42);display:inline-flex;align-items:center;justify-content:center;font-size:1.1rem;color:#fff;margin-bottom:.5rem}
        .mock-title{font-size:.95rem;font-weight:800;color:#fff}
        .mock-sub{font-size:.72rem;color:rgba(255,255,255,.75)}
        .mock-item{display:flex;align-items:center;gap:.7rem;padding:.7rem .8rem;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);border-radius:12px;margin-bottom:.5rem}
        .mock-thumb{width:40px;height:40px;border-radius:8px;background:rgba(255,107,53,.1);display:flex;align-items:center;justify-content:center;font-size:.9rem;color:#FF6B35;flex-shrink:0}
        .mock-name{font-size:.78rem;font-weight:600;color:rgba(255,255,255,.9)}
        .mock-desc{font-size:.65rem;color:rgba(255,255,255,.7)}
        .mock-price{font-size:.82rem;font-weight:800;color:#FF8C42;margin-left:auto;white-space:nowrap}
        .mock-glow{position:absolute;width:200px;height:200px;border-radius:50%;background:rgba(255,107,53,.12);filter:blur(80px);top:50%;left:50%;transform:translate(-50%,-50%);pointer-events:none}

        /* Social proof */
        .proof-bar{padding:3rem 0}
        .proof-stat{text-align:center}
        .proof-stat .num{font-size:clamp(1.3rem,5vw,2rem);font-weight:900;color:#fff;line-height:1.1;letter-spacing:-.02em}
        .proof-stat .lbl{font-size:.78rem;color:rgba(255,255,255,.75);margin-top:.2rem;line-height:1.35}

        /* Features */
        .features{padding:6rem 0}
        .feat-card{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:18px;padding:1.75rem;transition:all .25s;height:100%}
        .feat-card:hover{background:rgba(255,255,255,.05);border-color:rgba(255,107,53,.15);transform:translateY(-4px);box-shadow:0 12px 40px rgba(0,0,0,.2)}
        .feat-icon{width:48px;height:48px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;margin-bottom:1rem}
        .feat-card h3{font-size:.95rem;font-weight:700;color:#fff;margin-bottom:.4rem}
        .feat-card p{font-size:.82rem;color:rgba(255,255,255,.78);line-height:1.6;margin:0}

        /* How it Works */
        .how{padding:6rem 0}
        .step-num{width:56px;height:56px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:900;margin:0 auto 1rem;position:relative}
        .step-num::after{content:'';position:absolute;inset:-3px;border-radius:18px;border:2px dashed rgba(255,107,53,.2);animation:spin 20s linear infinite}
        @@keyframes spin{to{transform:rotate(360deg)}}
        .step-card{text-align:center;padding:1.5rem}
        .step-card h3{font-size:.95rem;font-weight:700;color:#fff;margin-bottom:.35rem}
        .step-card p{font-size:.82rem;color:rgba(255,255,255,.75);line-height:1.6}
        .step-arrow{display:flex;align-items:center;justify-content:center;color:rgba(255,107,53,.3);font-size:1.5rem;padding-top:2rem}

        /* FAQ */
        .faq-section{padding:5rem 0}
        .faq-item{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:14px;margin-bottom:.6rem;overflow:hidden}
        .faq-btn{width:100%;background:none;border:none;padding:1rem 1.25rem;text-align:left;color:#fff;font-family:inherit;font-size:.88rem;font-weight:600;cursor:pointer;display:flex;justify-content:space-between;align-items:center;gap:.75rem}
        .faq-btn:hover{background:rgba(255,255,255,.02)}
        .faq-btn i{color:#FF6B35;transition:transform .25s;font-size:.85rem;flex-shrink:0}
        .faq-btn:not(.collapsed) i{transform:rotate(180deg)}
        .faq-answer{padding:0 1.25rem 1rem;font-size:.84rem;color:rgba(255,255,255,.78);line-height:1.7}

        @@media(max-width:768px){
            .hero-visual{margin-top:3rem}
            .step-arrow{transform:rotate(90deg);padding-top:0}
            .hero{min-height:auto;padding-top:6rem;padding-bottom:3rem}
            .features{padding:4rem 0}
            .how{padding:4rem 0}
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
            .faq-section{padding:3rem 0}
            .proof-bar .row > [class*="col-"]{flex:0 0 100%;max-width:100%}
            .proof-stat{padding:.85rem 1rem;border:1px solid rgba(255,255,255,.08);border-radius:12px;background:rgba(255,255,255,.02)}
            .proof-stat .lbl{font-size:.74rem}
            /* Right-side floating widgets can cover content on narrow screens */
            .proof-bar .container,.lp-footer .container{padding-right:3.1rem}
        }
@endsection

@section('content')
    <section class="hero">
        <div class="container">
            @if(session('demo_unavailable'))
                <div class="alert mb-3" role="alert" style="background:rgba(251,191,36,.15);border:1px solid rgba(251,191,36,.3);color:#fbbf24;border-radius:12px">
                    Demo menü henüz yüklenmemiş.
                </div>
            @endif
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-badge"><span class="dot"></span> {{ $isTr ? 'Dijital QR Menü Platformu' : 'Digital QR Menu Platform' }}</div>
                    <h1>{{ $isTr ? 'Restoran ve Kafeler İçin' : 'Digital QR Menu' }}<br><span class="accent">{{ $isTr ? 'Dijital QR Menü Sistemi' : 'For Restaurants & Cafes' }}</span></h1>
                    <p class="hero-sub">
                        {{ $isTr
                            ? 'Dakikalar içinde ücretsiz dijital menünüzü oluşturun. Karekod menü ile masalara taşıyın, temassız menü deneyimi sunun. Online menü oluşturmak hiç bu kadar kolay olmamıştı.'
                            : 'Create your free digital menu in minutes. Bring it to tables with QR codes and offer a contactless menu experience. Creating an online menu has never been this easy.' }}
                    </p>
                    <div class="hero-btns">
                        <a href="{{ route('register') }}" class="hero-btn-primary">
                            <i class="bi bi-rocket-takeoff"></i> {{ $isTr ? 'Ücretsiz Başla' : 'Start Free' }}
                        </a>
                        <a href="{{ route('demo') }}" class="hero-btn-outline">
                            <i class="bi bi-eye"></i> {{ $isTr ? 'Canlı Demo' : 'Live Demo' }}
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 hero-visual">
                    <div class="hero-mockup">
                        <div class="mock-glow"></div>
                        <div class="mock-header">
                            <div class="mock-logo"><i class="bi bi-qr-code-scan"></i></div>
                            <div class="mock-title">{{ $isTr ? 'Lezzet Dünyası' : 'Taste World' }}</div>
                            <div class="mock-sub">{{ $isTr ? 'Dijital Menü' : 'Digital Menu' }}</div>
                        </div>
                        <div class="mock-item">
                            <div class="mock-thumb"><i class="bi bi-cup-hot"></i></div>
                            <div><div class="mock-name">{{ $isTr ? 'Türk Kahvesi' : 'Turkish Coffee' }}</div><div class="mock-desc">{{ $isTr ? 'Geleneksel lezzet' : 'Traditional taste' }}</div></div>
                            <div class="mock-price">35 ₺</div>
                        </div>
                        <div class="mock-item">
                            <div class="mock-thumb"><i class="bi bi-fire"></i></div>
                            <div><div class="mock-name">{{ $isTr ? 'Izgara Köfte' : 'Grilled Meatballs' }}</div><div class="mock-desc">{{ $isTr ? 'Pilav ve salata ile' : 'With rice and salad' }}</div></div>
                            <div class="mock-price">120 ₺</div>
                        </div>
                        <div class="mock-item">
                            <div class="mock-thumb"><i class="bi bi-snow2"></i></div>
                            <div><div class="mock-name">Künefe</div><div class="mock-desc">{{ $isTr ? 'Antep fıstıklı' : 'With pistachio' }}</div></div>
                            <div class="mock-price">85 ₺</div>
                        </div>
                        <div class="mock-item">
                            <div class="mock-thumb"><i class="bi bi-droplet"></i></div>
                            <div><div class="mock-name">{{ $isTr ? 'Limonata' : 'Lemonade' }}</div><div class="mock-desc">{{ $isTr ? 'Ev yapımı, naneli' : 'Homemade, with mint' }}</div></div>
                            <div class="mock-price">45 ₺</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Social Proof --}}
    <section class="proof-bar">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <div class="col-12 col-sm-4 col-md-3">
                    <div class="proof-stat">
                        <div class="num">{{ $tenantLabel }}</div>
                        <div class="lbl">{{ $isTr ? 'restoran dijital menü kullanıyor' : 'restaurants use digital menu' }}</div>
                    </div>
                </div>
                <div class="col-12 col-sm-4 col-md-3">
                    <div class="proof-stat">
                        <div class="num">%100</div>
                        <div class="lbl">{{ $isTr ? 'ücretsiz başlangıç' : 'free to start' }}</div>
                    </div>
                </div>
                <div class="col-12 col-sm-4 col-md-3">
                    <div class="proof-stat">
                        <div class="num">3 {{ $isTr ? 'dk' : 'min' }}</div>
                        <div class="lbl">{{ $isTr ? 'kurulum süresi' : 'setup time' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="features" id="features">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-badge" style="background:rgba(255,107,53,.1);color:#FF8C42">
                    <i class="bi bi-stars"></i> {{ $isTr ? 'Özellikler' : 'Features' }}
                </div>
                <h2 class="section-title">{{ $isTr ? 'Restoran Menü Sisteminiz İçin' : 'Everything You Need For' }}<br>{{ $isTr ? 'İhtiyacınız Olan Her Şey' : 'Your Restaurant Menu' }}</h2>
                <p class="section-sub mx-auto">{{ $isTr ? 'Basit arayüz, güçlü özellikler. Teknik bilgi gerektirmeden profesyonel dijital menünüzü yönetin.' : 'Simple interface, powerful features. Manage your professional digital menu without any technical knowledge.' }}</p>
            </div>
            <div class="row g-4">
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="feat-card">
                        <div class="feat-icon" style="background:rgba(255,107,53,.12);color:#FF6B35"><i class="bi bi-qr-code"></i></div>
                        <h3>{{ $isTr ? 'QR Kod Menü' : 'QR Code Menu' }}</h3>
                        <p>{{ $isTr ? 'Baskıya hazır karekod menü oluşturun. Müşterileriniz telefonlarıyla tarayıp dijital menünüzü anında görsün.' : 'Create print-ready QR code menus. Your customers can scan with their phones and instantly view your digital menu.' }}</p>
                    </article>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="feat-card">
                        <div class="feat-icon" style="background:rgba(16,185,129,.12);color:#10b981"><i class="bi bi-grid-3x3-gap"></i></div>
                        <h3>{{ $isTr ? 'Kategori & Ürün Yönetimi' : 'Category & Product Management' }}</h3>
                        <p>{{ $isTr ? 'Sınırsız kategori ve ürün. Fotoğraf, açıklama, fiyat — sürükle-bırak sıralama ile online menü yönetimi.' : 'Unlimited categories and products. Photos, descriptions, prices — drag-and-drop ordering for online menu management.' }}</p>
                    </article>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="feat-card">
                        <div class="feat-icon" style="background:rgba(251,191,36,.12);color:#FBBF24"><i class="bi bi-star"></i></div>
                        <h3>{{ $isTr ? 'Müşteri Değerlendirmeleri' : 'Customer Reviews' }}</h3>
                        <p>{{ $isTr ? 'Müşterileriniz QR menü üzerinden restoran değerlendirmesi bıraksın. Puanlarınızı takip edin.' : 'Let your customers leave reviews via the QR menu. Track your ratings.' }}</p>
                    </article>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="feat-card">
                        <div class="feat-icon" style="background:rgba(99,102,241,.12);color:#6366f1"><i class="bi bi-people"></i></div>
                        <h3>{{ $isTr ? 'Çoklu Kullanıcı' : 'Multi-User Access' }}</h3>
                        <p>{{ $isTr ? 'Ekibinizi davet edin. Owner, admin ve personel rolleriyle restoran yönetim paneli.' : 'Invite your team. Restaurant management panel with owner, admin, and staff roles.' }}</p>
                    </article>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="feat-card">
                        <div class="feat-icon" style="background:rgba(236,72,153,.12);color:#EC4899"><i class="bi bi-instagram"></i></div>
                        <h3>{{ $isTr ? 'Sosyal Medya Entegrasyonu' : 'Social Media Integration' }}</h3>
                        <p>{{ $isTr ? 'Instagram, Facebook, WhatsApp hesaplarınızı ekleyin. Dijital menü sayfanızda otomatik gösterilsin.' : 'Add your Instagram, Facebook, WhatsApp accounts. Automatically displayed on your digital menu page.' }}</p>
                    </article>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="feat-card">
                        <div class="feat-icon" style="background:rgba(168,85,247,.12);color:#A855F7"><i class="bi bi-phone"></i></div>
                        <h3>{{ $isTr ? 'Mobil Uyumlu Menü' : 'Mobile Friendly Menu' }}</h3>
                        <p>{{ $isTr ? 'Tüm cihazlarda kusursuz görüntülenen mobil uyumlu menü tasarımı. Müşterileriniz her yerden erişsin.' : 'Mobile-friendly menu design that displays perfectly on all devices. Your customers can access from anywhere.' }}</p>
                    </article>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('features') }}" class="hero-btn-outline" style="font-size:.85rem;padding:.65rem 1.5rem">
                    <i class="bi bi-arrow-right"></i> {{ $isTr ? 'Tüm Özellikleri Gör' : 'See All Features' }}
                </a>
            </div>
        </div>
    </section>

    {{-- How it Works --}}
    <section class="how" id="how">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-badge" style="background:rgba(108,92,231,.1);color:#a29bfe">
                    <i class="bi bi-lightning-charge"></i> {{ $isTr ? 'Nasıl Çalışır?' : 'How It Works?' }}
                </div>
                <h2 class="section-title">{{ $isTr ? '3 Adımda QR Menü Oluşturun' : 'Create a QR Menu in 3 Steps' }}</h2>
                <p class="section-sub mx-auto">{{ $isTr ? 'Dakikalar içinde dijital menünüz hazır. Teknik bilgi gerektirmez.' : 'Your digital menu is ready in minutes. No technical knowledge required.' }}</p>
            </div>
            <div class="row align-items-start">
                <div class="col-12 col-md-4">
                    <div class="step-card">
                        <div class="step-num" style="background:rgba(255,107,53,.12);color:#FF6B35">1</div>
                        <h3>{{ $isTr ? 'Ücretsiz Kayıt Olun' : 'Register for Free' }}</h3>
                        <p>{{ $isTr ? 'Restoran bilgilerinizi girin. Ücretsiz dijital menü hesabınız anında oluşturulur.' : 'Enter your restaurant details. Your free digital menu account is created instantly.' }}</p>
                    </div>
                </div>
                <div class="col-12 col-md-1 d-none d-md-flex step-arrow"><i class="bi bi-arrow-right"></i></div>
                <div class="col-12 col-md-3">
                    <div class="step-card">
                        <div class="step-num" style="background:rgba(16,185,129,.12);color:#10b981">2</div>
                        <h3>{{ $isTr ? 'Menünüzü Oluşturun' : 'Create Your Menu' }}</h3>
                        <p>{{ $isTr ? 'Kategorileri ve ürünleri ekleyin. Fotoğraf yükleyin, fiyatları belirleyin. Anlık menü güncelleme yapın.' : 'Add categories and products. Upload photos, set prices. Make instant menu updates.' }}</p>
                    </div>
                </div>
                <div class="col-12 col-md-1 d-none d-md-flex step-arrow"><i class="bi bi-arrow-right"></i></div>
                <div class="col-12 col-md-3">
                    <div class="step-card">
                        <div class="step-num" style="background:rgba(251,191,36,.12);color:#FBBF24">3</div>
                        <h3>{{ $isTr ? 'QR Kodu Paylaşın' : 'Share Your QR Code' }}</h3>
                        <p>{{ $isTr ? 'QR kodunuzu yazdırıp masalara yerleştirin. Müşterileriniz karekod menüyü tarayıp menüyü görsün.' : 'Print and place your QR code on tables. Your customers scan the QR code menu and view the menu.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="faq-section" id="faq">
        <div class="container" style="max-width:720px">
            <div class="text-center mb-4">
                <div class="section-badge" style="background:rgba(16,185,129,.1);color:#10b981">
                    <i class="bi bi-question-circle"></i> {{ $isTr ? 'SSS' : 'FAQ' }}
                </div>
                <h2 class="section-title">{{ $isTr ? 'Sık Sorulan Sorular' : 'Frequently Asked Questions' }}</h2>
            </div>
            <div id="faqAccordion">
                @php
                $faqs = $isTr ? [
                    ['QR menü nedir ve nasıl çalışır?', 'QR menü, restoranınızın menüsünü dijital olarak sunan ve müşterilerin telefonlarıyla karekod menüyü tarayarak erişebildiği bir temassız menü sistemidir. Kağıt menü yerine anlık güncellenebilir bir dijital menü kullanırsınız.'],
                    ['QR menü kurmak için teknik bilgi gerekiyor mu?', 'Hayır, teknik bilgi gerekmez. Sipariş Masanda ile 3 adımda dijital menünüzü oluşturabilirsiniz: kayıt olun, ürünlerinizi ekleyin, QR kodunuzu yazdırın. Restoran menü sistemi kullanımı son derece kolaydır.'],
                    ['Ücretsiz dijital menü oluşturabilir miyim?', 'Evet, Sipariş Masanda ücretsiz plan sunmaktadır. Sınırsız kategori ve ürün ekleyebilir, QR kodunuzu oluşturabilirsiniz. Kredi kartı gerekmez. Online menü oluşturmak tamamen ücretsizdir.'],
                    ['Müşteriler uygulama indirmek zorunda mı?', 'Hayır, müşterileriniz herhangi bir uygulama indirmeden telefonlarının kamerasıyla karekod menüyü tarayarak dijital menüye doğrudan erişebilir.'],
                    ['Menü fiyatlarını anlık güncelleyebilir miyim?', 'Evet, restoran yönetim panelinden ürün fiyatlarını, açıklamalarını ve fotoğraflarını istediğiniz zaman anında güncelleyebilirsiniz. Değişiklikler kafe menünüze hemen yansır.'],
                ] : [
                    ['What is a QR menu and how does it work?', 'A QR menu is a contactless menu system that digitally presents your restaurant menu. Customers can access it by scanning the QR code with their phone camera — no app download needed.'],
                    ['Do I need technical knowledge to set up a QR menu?', 'No technical knowledge is needed. With Siparis Masanda you can create your digital menu in 3 steps: register, add your products, and print your QR code.'],
                    ['Can I create a free digital menu?', 'Yes, Siparis Masanda offers a free plan. You can add unlimited categories and products, and create your QR code. No credit card required.'],
                    ['Do customers need to download an app?', 'No, your customers can directly access the menu by scanning the QR code with their phone camera without downloading any app.'],
                    ['Can I update menu prices instantly?', 'Yes, you can instantly update product prices, descriptions, and photos from the management panel at any time. Changes are reflected immediately.'],
                ];
                @endphp
                @foreach($faqs as $i => [$q, $a])
                <div class="faq-item">
                    <button class="faq-btn {{ $i > 0 ? 'collapsed' : '' }}" data-bs-toggle="collapse" data-bs-target="#faq-{{ $i }}">
                        {{ $q }}
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="collapse {{ $i === 0 ? 'show' : '' }}" id="faq-{{ $i }}" data-bs-parent="#faqAccordion">
                        <div class="faq-answer">{{ $a }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="cta-bar">
        <div class="container">
            <div class="cta-box">
                <h2>{{ $isTr ? 'Restoranınızı Dijitale Taşımaya' : 'Ready to Take Your Restaurant' }}<br>{{ $isTr ? 'Hazır mısınız?' : 'Digital?' }}</h2>
                <p>{{ $isTr ? 'Hemen ücretsiz hesap oluşturun ve dijital menünüzü dakikalar içinde yayına alın.' : 'Create a free account now and publish your digital menu in minutes.' }}</p>
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a href="{{ route('register') }}" class="hero-btn-primary" style="position:relative">
                        <i class="bi bi-rocket-takeoff"></i> {{ $isTr ? 'Ücretsiz Hesap Oluştur' : 'Create Free Account' }}
                    </a>
                    <a href="{{ route('demo') }}" class="hero-btn-outline" style="position:relative">
                        <i class="bi bi-eye"></i> {{ $isTr ? 'Canlı Demo' : 'Live Demo' }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Kampanya Modal --}}
<div class="promo-overlay" id="promoOverlay" aria-hidden="true" data-nosnippet>
    <div class="promo-modal" id="promoModal" role="dialog" aria-modal="true" aria-labelledby="promoTitle">
        <button class="promo-close" id="promoClose" aria-label="Kapat">&times;</button>
        <div class="promo-badge-row">
            <span class="promo-badge-live"><i class="bi bi-lightning-charge-fill"></i> {{ $isTr ? 'SINIRLI TEKLİF' : 'LIMITED OFFER' }}</span>
        </div>
        <div class="promo-icon-wrap">
            <i class="bi bi-gift-fill"></i>
        </div>
        <h2 class="promo-title" id="promoTitle">
            {{ $isTr ? 'İlk 100 İşletmeye' : 'First 100 Businesses' }}<br>
            <span class="promo-accent">{{ $isTr ? 'Tamamen Ücretsiz!' : 'Completely Free!' }}</span>
        </h2>
        <p class="promo-desc">
            {{ $isTr
                ? 'Premium özelliklerin tamamına sınırsız erişim. QR menü, fotoğraflı ürünler, ekip yönetimi, slider ve çok daha fazlası — herhangi bir ücret ödemeden.'
                : 'Unlimited access to all premium features. QR menu, photo products, team management, sliders and much more — without paying a dime.' }}
        </p>
        <div class="promo-countdown" id="promoCountdown">
            <div class="promo-cd-item"><span class="promo-cd-num" id="cdDays">00</span><span class="promo-cd-label">{{ $isTr ? 'Gün' : 'Days' }}</span></div>
            <div class="promo-cd-sep">:</div>
            <div class="promo-cd-item"><span class="promo-cd-num" id="cdHours">00</span><span class="promo-cd-label">{{ $isTr ? 'Saat' : 'Hours' }}</span></div>
            <div class="promo-cd-sep">:</div>
            <div class="promo-cd-item"><span class="promo-cd-num" id="cdMins">00</span><span class="promo-cd-label">{{ $isTr ? 'Dakika' : 'Min' }}</span></div>
        </div>
        <a href="{{ route('register') }}" class="promo-cta">
            <i class="bi bi-rocket-takeoff"></i> {{ $isTr ? 'Hemen Ücretsiz Kayıt Ol' : 'Register for Free Now' }}
        </a>
        <div class="promo-footer-note">
            <i class="bi bi-shield-check"></i> {{ $isTr ? 'Kredi kartı gerekmez · 2 dakikada kurulum' : 'No credit card required · Setup in 2 minutes' }}
        </div>
    </div>
</div>

<style>
.promo-overlay{
    position:fixed;inset:0;z-index:9999;
    background:rgba(0,0,0,.65);backdrop-filter:blur(6px);
    display:flex;align-items:center;justify-content:center;
    padding:1rem;
    opacity:0;visibility:hidden;transition:opacity .4s,visibility .4s;
}
.promo-overlay.show{opacity:1;visibility:visible}
.promo-modal{
    position:relative;
    background:linear-gradient(165deg,#0e1326 0%,#151d35 50%,#0e1326 100%);
    border:1px solid rgba(255,107,53,.2);
    border-radius:24px;
    padding:2.5rem 2rem 2rem;
    max-width:440px;width:100%;
    text-align:center;
    box-shadow:0 24px 80px rgba(0,0,0,.5),0 0 80px rgba(255,107,53,.08);
    transform:scale(.9) translateY(20px);
    transition:transform .4s cubic-bezier(.34,1.56,.64,1);
}
.promo-overlay.show .promo-modal{transform:scale(1) translateY(0)}
.promo-close{
    position:absolute;top:1rem;right:1rem;
    background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
    color:rgba(255,255,255,.5);font-size:1.3rem;
    width:36px;height:36px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;transition:all .2s;line-height:1;
}
.promo-close:hover{background:rgba(255,255,255,.12);color:#fff}
.promo-badge-row{margin-bottom:1rem}
.promo-badge-live{
    display:inline-flex;align-items:center;gap:.35rem;
    padding:.3rem .85rem;border-radius:999px;
    font-size:.68rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;
    background:rgba(239,68,68,.15);color:#f87171;
    border:1px solid rgba(239,68,68,.25);
    animation:promoPulse 2s ease-in-out infinite;
}
@@keyframes promoPulse{0%,100%{opacity:1}50%{opacity:.7}}
.promo-icon-wrap{
    width:72px;height:72px;border-radius:20px;
    background:linear-gradient(135deg,rgba(255,107,53,.15),rgba(255,140,66,.08));
    border:1px solid rgba(255,107,53,.2);
    display:flex;align-items:center;justify-content:center;
    margin:0 auto 1.25rem;font-size:2rem;color:#FF8C42;
}
.promo-title{
    font-size:1.6rem;font-weight:900;color:#fff;
    line-height:1.2;letter-spacing:-.02em;margin-bottom:.75rem;
}
.promo-accent{
    background:linear-gradient(90deg,#FF6B35,#FFB347);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
}
.promo-desc{
    font-size:.85rem;color:rgba(255,255,255,.5);line-height:1.65;
    margin-bottom:1.25rem;max-width:360px;margin-left:auto;margin-right:auto;
}
.promo-countdown{
    display:flex;align-items:center;justify-content:center;gap:.5rem;
    margin-bottom:1.5rem;
}
.promo-cd-item{display:flex;flex-direction:column;align-items:center}
.promo-cd-num{
    font-size:1.5rem;font-weight:800;color:#fff;
    background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
    border-radius:10px;padding:.25rem .6rem;min-width:52px;
    font-variant-numeric:tabular-nums;
}
.promo-cd-label{font-size:.6rem;font-weight:700;color:rgba(255,255,255,.3);text-transform:uppercase;letter-spacing:.05em;margin-top:.25rem}
.promo-cd-sep{font-size:1.3rem;font-weight:800;color:rgba(255,255,255,.2);padding-bottom:1rem}
.promo-cta{
    display:inline-flex;align-items:center;gap:.5rem;
    padding:.85rem 2rem;border-radius:12px;
    background:linear-gradient(135deg,#FF6B35,#FF8C42);color:#fff;
    font-weight:700;font-size:.95rem;text-decoration:none;
    box-shadow:0 8px 32px rgba(255,107,53,.4);
    transition:all .2s;border:none;width:100%;justify-content:center;
}
.promo-cta:hover{color:#fff;transform:translateY(-2px);box-shadow:0 12px 40px rgba(255,107,53,.55)}
.promo-footer-note{
    margin-top:1rem;font-size:.72rem;color:rgba(255,255,255,.35);
    display:flex;align-items:center;justify-content:center;gap:.35rem;
}
@@media(max-width:480px){
    .promo-modal{padding:2rem 1.25rem 1.5rem;border-radius:18px}
    .promo-title{font-size:1.3rem}
    .promo-desc{font-size:.8rem}
    .promo-icon-wrap{width:56px;height:56px;font-size:1.5rem;border-radius:16px}
    .promo-cd-num{font-size:1.2rem;min-width:44px;padding:.2rem .45rem}
}
</style>

<script>
(function(){
    var CLOSED_KEY='promo_closed_v1';
    if(sessionStorage.getItem(CLOSED_KEY)) return;

    var overlay=document.getElementById('promoOverlay');
    var modal=document.getElementById('promoModal');

    // Kampanya süresi: 14 gün
    var WINDOW_KEY='promo_window_start_v1';
    var startTs=parseInt(localStorage.getItem(WINDOW_KEY) || '0',10);
    var now=Date.now();
    if(!startTs || isNaN(startTs)){
        startTs=now;
        localStorage.setItem(WINDOW_KEY,String(startTs));
    }
    var twoWeeksMs=14*24*60*60*1000;
    if(now-startTs>twoWeeksMs){
        // 2 haftalık süre bittiyse modali hiç gösterme
        return;
    }

    setTimeout(function(){
        overlay.classList.add('show');
        overlay.setAttribute('aria-hidden','false');
    },1500);

    function close(){
        overlay.classList.remove('show');
        overlay.setAttribute('aria-hidden','true');
        sessionStorage.setItem(CLOSED_KEY,'1');
    }
    document.getElementById('promoClose').addEventListener('click',close);
    overlay.addEventListener('click',function(e){if(e.target===overlay)close()});

    // Countdown — 2 haftalık kampanya penceresinin bitimine kadar (gün:saat:dakika)
    function startCountdown(){
        var endTs=startTs+twoWeeksMs;
        function tick(){
            var nowMs=Date.now();
            var diff=Math.max(0,endTs-nowMs);
            var totalMin=Math.floor(diff/60000);
            var totalDays=Math.floor(totalMin/(60*24));
            var remMin=totalMin-totalDays*60*24;
            var h=Math.floor(remMin/60);
            var m=remMin%60;
            document.getElementById('cdDays').textContent=String(totalDays).padStart(2,'0');
            document.getElementById('cdHours').textContent=String(h).padStart(2,'0');
            document.getElementById('cdMins').textContent=String(m).padStart(2,'0');
        }
        tick();
        setInterval(tick,1000);
    }
    startCountdown();
})();
</script>
@endsection
