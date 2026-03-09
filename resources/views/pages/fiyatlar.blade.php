@extends('layouts.public')

@php
    $locale = app()->getLocale();
    $isTr = $locale === 'tr';
@endphp

@section('title', $isTr
    ? 'QR Menü Fiyatları ve Paketleri | Ücretsiz Başla | Sipariş Masanda'
    : 'QR Menu Pricing & Plans | Start Free | Siparis Masanda')

@section('meta_description', $isTr
    ? 'Restoran için en uygun dijital menü fiyatları. Ücretsiz plan dahil tüm paketleri karşılaştır. Kredi kartı gerekmez, hemen başla.'
    : 'The best digital menu prices for restaurants. Compare all plans including the free plan. No credit card required, start now.')

@section('meta_keywords', $isTr
    ? 'qr menü fiyatları, dijital menü ücreti, ücretsiz qr menü, aylık ücret, restoran menü sistemi fiyat, paket karşılaştırma'
    : 'qr menu pricing, digital menu cost, free qr menu, monthly fee, restaurant menu system price, plan comparison')

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {"@@type": "ListItem", "position": 1, "name": "{{ $isTr ? 'Ana Sayfa' : 'Home' }}", "item": "{{ url('/') }}"},
        {"@@type": "ListItem", "position": 2, "name": "{{ $isTr ? 'Fiyatlar' : 'Pricing' }}", "item": "{{ route('pricing') }}"}
    ]
}
</script>
@endsection

@section('styles')
        .pricing-section{padding:2rem 0 5rem}
        .price-card{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:20px;padding:2rem 1.75rem;text-align:center;transition:all .25s;height:100%;display:flex;flex-direction:column}
        .price-card:hover{transform:translateY(-4px);box-shadow:0 16px 48px rgba(0,0,0,.2)}
        .price-card.featured{border-color:rgba(255,107,53,.3);background:rgba(255,107,53,.04)}
        .price-badge{display:inline-flex;padding:.2rem .6rem;border-radius:999px;font-size:.65rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:.75rem}
        .price-name{font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:.25rem}
        .price-amount{font-size:2.5rem;font-weight:900;color:#fff;line-height:1;margin:.75rem 0 .25rem}
        .price-amount .currency{font-size:1rem;font-weight:600;vertical-align:super}
        .price-amount .period{font-size:.8rem;font-weight:500;color:rgba(255,255,255,.35)}
        .price-desc{font-size:.82rem;color:rgba(255,255,255,.4);margin-bottom:1.25rem}
        .price-features{list-style:none;padding:0;margin:0 0 1.5rem;text-align:left;flex:1}
        .price-features li{font-size:.82rem;color:rgba(255,255,255,.6);padding:.4rem 0;display:flex;align-items:flex-start;gap:.5rem}
        .price-features li i{color:#10b981;font-size:.7rem;margin-top:.25rem;flex-shrink:0}
        .price-features li.disabled{color:rgba(255,255,255,.2)}
        .price-features li.disabled i{color:rgba(255,255,255,.15)}
        .price-cta{margin-top:auto}
        .no-cc{font-size:.72rem;color:rgba(255,255,255,.3);margin-top:.5rem}

        .faq-section{padding:5rem 0}
        .faq-item{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:14px;margin-bottom:.6rem;overflow:hidden}
        .faq-btn{width:100%;background:none;border:none;padding:1rem 1.25rem;text-align:left;color:#fff;font-family:inherit;font-size:.88rem;font-weight:600;cursor:pointer;display:flex;justify-content:space-between;align-items:center;gap:.75rem}
        .faq-btn:hover{background:rgba(255,255,255,.02)}
        .faq-btn i{color:#FF6B35;transition:transform .25s;font-size:.85rem;flex-shrink:0}
        .faq-btn:not(.collapsed) i{transform:rotate(180deg)}
        .faq-answer{padding:0 1.25rem 1rem;font-size:.84rem;color:rgba(255,255,255,.5);line-height:1.7}
@endsection

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1>{{ $isTr ? 'Restoranın İçin En Uygun' : 'The Best' }} <span class="accent">{{ $isTr ? 'QR Menü Paketi' : 'QR Menu Plan' }}</span></h1>
            <p class="page-hero-sub">{{ $isTr ? 'Ücretsiz planla hemen başla. İşletmen büyüdükçe ihtiyacına göre yükselt. QR menü fiyatları şeffaf, sürpriz yok.' : 'Start with the free plan. Upgrade as your business grows. QR menu pricing is transparent, no surprises.' }}</p>
        </div>
    </section>

    <section class="pricing-section">
        <div class="container">
            <div class="row g-4 justify-content-center" style="max-width:860px;margin:0 auto">
                {{-- Free / Basic --}}
                <div class="col-md-6">
                    <div class="price-card">
                        <div><span class="price-badge" style="background:rgba(16,185,129,.12);color:#10b981">{{ $isTr ? 'Ücretsiz' : 'Free' }}</span></div>
                        <div class="price-name">Basic</div>
                        <div class="price-amount">
                            <span class="currency">₺</span>0
                            <span class="period">/ {{ $isTr ? 'ay' : 'mo' }}</span>
                        </div>
                        <div class="price-desc">{{ $isTr ? 'Dijital menüye başlamak için gereken her şey. Ücretsiz QR menü oluştur.' : 'Everything you need to start your digital menu. Create a free QR menu.' }}</div>
                        <ul class="price-features">
                            <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Sınırsız kategori ve ürün' : 'Unlimited categories & products' }}</li>
                            <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'QR kod oluşturma ve indirme' : 'QR code generation & download' }}</li>
                            <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Fotoğraflı dijital menü' : 'Digital menu with photos' }}</li>
                            <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Müşteri değerlendirmeleri' : 'Customer reviews' }}</li>
                            <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Mobil uyumlu menü tasarımı' : 'Mobile-friendly menu design' }}</li>
                            <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Çoklu kullanıcı desteği' : 'Multi-user support' }}</li>
                            <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Sosyal medya entegrasyonu' : 'Social media integration' }}</li>
                            <li class="disabled"><i class="bi bi-x-circle-fill"></i> {{ $isTr ? 'Slider görselleri' : 'Slider images' }}</li>
                            <li class="disabled"><i class="bi bi-x-circle-fill"></i> {{ $isTr ? 'Etkinlik duyuruları' : 'Event announcements' }}</li>
                        </ul>
                        <div class="price-cta">
                            <a href="{{ route('register') }}" class="hero-btn-outline w-100 justify-content-center" style="padding:.7rem 1.5rem">
                                <i class="bi bi-rocket-takeoff"></i> {{ $isTr ? 'Ücretsiz Başla' : 'Start Free' }}
                            </a>
                            <div class="no-cc"><i class="bi bi-shield-check me-1"></i> {{ $isTr ? 'Kredi kartı gerekmez' : 'No credit card required' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Premium --}}
                <div class="col-md-6">
                    <div class="price-card featured">
                        <div><span class="price-badge" style="background:rgba(255,107,53,.15);color:#FF8C42">{{ $isTr ? 'Popüler' : 'Popular' }}</span></div>
                        <div class="price-name">Premium</div>
                        <div class="price-amount">
                            <span style="font-size:1.1rem;color:rgba(255,255,255,.4)">{{ $isTr ? 'İletişime geçin' : 'Contact us' }}</span>
                        </div>
                        <div class="price-desc">{{ $isTr ? 'İşletmenizi öne çıkaran premium dijital menü özellikleri.' : 'Premium digital menu features that make your business stand out.' }}</div>
                        <ul class="price-features">
                            <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Basic planın tüm özellikleri' : 'All Basic plan features' }}</li>
                            <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Slider görsel yönetimi' : 'Slider image management' }}</li>
                            <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Etkinlik ve duyuru yönetimi' : 'Event & announcement management' }}</li>
                            <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Öncelikli destek' : 'Priority support' }}</li>
                            <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Özel tasarım seçenekleri' : 'Custom design options' }}</li>
                        </ul>
                        <div class="price-cta">
                            <a href="{{ route('contact') }}" class="hero-btn-primary w-100 justify-content-center" style="padding:.7rem 1.5rem">
                                <i class="bi bi-chat-dots"></i> {{ $isTr ? 'İletişime Geç' : 'Contact Us' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Pricing FAQ --}}
    <section class="faq-section">
        <div class="container" style="max-width:720px">
            <div class="text-center mb-4">
                <h2 class="section-title" style="font-size:1.5rem">{{ $isTr ? 'Sık Sorulan Fiyat Soruları' : 'Pricing FAQ' }}</h2>
            </div>
            <div id="priceFaq">
                @php
                $priceFaqs = $isTr ? [
                    ['Ücretsiz plan gerçekten ücretsiz mi?', 'Evet, Basic plan tamamen ücretsizdir. Kredi kartı bilgisi istemiyoruz. Sınırsız kategori ve ürün ekleyebilir, dijital menü ücreti ödemeden QR menünüzü oluşturabilirsiniz.'],
                    ['Ücretli plana ne zaman geçmeliyim?', 'Basic plan çoğu restoran için yeterlidir. Slider görselleri, etkinlik duyuruları ve öncelikli destek gibi ek özellikler istiyorsanız Premium plana geçebilirsiniz.'],
                    ['Aylık ücret dışında gizli maliyet var mı?', 'Hayır, restoran menü sistemi fiyatlarımız şeffaftır. Paket karşılaştırma tablosunda gördüğünüz özellikler tam olarak aldığınız hizmettir.'],
                    ['Dilediğim zaman iptal edebilir miyim?', 'Evet, herhangi bir taahhüt yoktur. İstediğiniz zaman ücretsiz plana geri dönebilirsiniz.'],
                ] : [
                    ['Is the free plan really free?', 'Yes, the Basic plan is completely free. We don\'t ask for credit card information. You can add unlimited categories and products and create your QR menu without any digital menu cost.'],
                    ['When should I upgrade to a paid plan?', 'The Basic plan is sufficient for most restaurants. You can upgrade to Premium if you want additional features like slider images, event announcements, and priority support.'],
                    ['Are there any hidden costs beyond the monthly fee?', 'No, our restaurant menu system prices are transparent. The features you see in the plan comparison table are exactly what you get.'],
                    ['Can I cancel at any time?', 'Yes, there is no commitment. You can switch back to the free plan at any time.'],
                ];
                @endphp
                @foreach($priceFaqs as $i => [$q, $a])
                <div class="faq-item">
                    <button class="faq-btn collapsed" data-bs-toggle="collapse" data-bs-target="#pf-{{ $i }}">{{ $q }}<i class="bi bi-chevron-down"></i></button>
                    <div class="collapse" id="pf-{{ $i }}" data-bs-parent="#priceFaq">
                        <div class="faq-answer">{{ $a }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="cta-bar" style="padding-top:0">
        <div class="container">
            <div class="cta-box">
                <h2>{{ $isTr ? 'Hemen Ücretsiz Başlayın' : 'Start Free Today' }}</h2>
                <p>{{ $isTr ? 'Kredi kartı gerekmez. Dakikalar içinde dijital menünüzü oluşturun.' : 'No credit card required. Create your digital menu in minutes.' }}</p>
                <a href="{{ route('register') }}" class="hero-btn-primary" style="position:relative">
                    <i class="bi bi-rocket-takeoff"></i> {{ $isTr ? 'Ücretsiz Hesap Oluştur' : 'Create Free Account' }}
                </a>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
