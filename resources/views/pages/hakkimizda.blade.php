@extends('layouts.public')

@php
    $locale = app()->getLocale();
    $isTr = $locale === 'tr';
@endphp

@section('title', $isTr
    ? 'Hakkımızda | Sipariş Masanda — Dijital QR Menü Platformu'
    : 'About Us | Siparis Masanda — Digital QR Menu Platform')

@section('meta_description', $isTr
    ? 'Sipariş Masanda ekibini tanı. Türkiye\'nin restoran ve kafeleri için geliştirilen dijital menü platformunun hikayesi.'
    : 'Meet the Siparis Masanda team. The story of the digital menu platform built for restaurants and cafes.')

@section('meta_keywords', $isTr
    ? 'dijital menü platformu, Türkiye restoran teknolojisi, qr menü sistemi, sipariş masanda hakkında'
    : 'digital menu platform, restaurant technology, qr menu system, about siparis masanda')

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {"@@type": "ListItem", "position": 1, "name": "{{ $isTr ? 'Ana Sayfa' : 'Home' }}", "item": "{{ url('/') }}"},
        {"@@type": "ListItem", "position": 2, "name": "{{ $isTr ? 'Hakkımızda' : 'About' }}", "item": "{{ route('about') }}"}
    ]
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "name": "Sipariş Masanda",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('og-cover.svg') }}",
    "description": "{{ $isTr ? 'Restoran ve kafeler için dijital QR menü platformu' : 'Digital QR menu platform for restaurants and cafes' }}",
    "foundingDate": "2025",
    "address": {
        "@@type": "PostalAddress",
        "addressCountry": "TR"
    }
}
</script>
@endsection

@section('styles')
        .about-section{padding:2rem 0 5rem}
        .about-block{margin-bottom:3.5rem}
        .about-block h2{font-size:1.3rem;font-weight:800;color:#fff;margin-bottom:.6rem}
        .about-block p{font-size:.9rem;color:rgba(255,255,255,.5);line-height:1.75;max-width:640px}
        .value-card{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:1.5rem;text-align:center;height:100%;transition:all .25s}
        .value-card:hover{background:rgba(255,255,255,.05);border-color:rgba(255,107,53,.15);transform:translateY(-3px)}
        .value-icon{width:48px;height:48px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;margin:0 auto .75rem}
        .value-card h3{font-size:.9rem;font-weight:700;color:#fff;margin-bottom:.3rem}
        .value-card p{font-size:.8rem;color:rgba(255,255,255,.4);line-height:1.6;margin:0}
@endsection

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1>{{ $isTr ? 'Biz Kimiz?' : 'Who We Are' }} <span class="accent">{{ $isTr ? 'Sipariş Masanda\'nın Hikayesi' : 'The Story of Siparis Masanda' }}</span></h1>
            <p class="page-hero-sub">{{ $isTr ? 'Türkiye\'nin restoran ve kafeleri için geliştirilen dijital QR menü platformu.' : 'A digital QR menu platform built for restaurants and cafes.' }}</p>
        </div>
    </section>

    <section class="about-section">
        <div class="container" style="max-width:800px">
            <div class="about-block">
                <h2>{{ $isTr ? 'Neden Kurduk?' : 'Why We Started' }}</h2>
                <p>{{ $isTr
                    ? 'Restoranlarda kağıt menülerin sürekli güncellenmesi, basım maliyetleri ve hijyen endişeleri gibi sorunlar bizi harekete geçirdi. Türkiye\'deki restoran teknolojisi alanında, küçük ve orta ölçekli işletmelerin de kolayca kullanabileceği bir dijital menü platformu eksikliği vardı. Sipariş Masanda, bu boşluğu doldurmak için 2025 yılında kuruldu.'
                    : 'The constant need to update paper menus, printing costs, and hygiene concerns at restaurants motivated us. There was a gap in restaurant technology for a digital menu platform that small and medium-sized businesses could easily use. Siparis Masanda was founded in 2025 to fill this gap.' }}</p>
            </div>

            <div class="about-block">
                <h2>{{ $isTr ? 'Vizyonumuz' : 'Our Vision' }}</h2>
                <p>{{ $isTr
                    ? 'Her restoranın, kafenin ve yeme-içme işletmesinin teknolojiye erişimini demokratikleştirmek. Dijital menü platformumuz ile işletmelerin müşterilerine modern, hızlı ve temassız bir menü deneyimi sunmasını sağlıyoruz. QR menü sistemimiz ile Türkiye\'deki restoran dijitalleşmesine öncülük ediyoruz.'
                    : 'Democratizing access to technology for every restaurant, cafe, and food business. With our digital menu platform, we enable businesses to offer their customers a modern, fast, and contactless menu experience. We are leading restaurant digitalization with our QR menu system.' }}</p>
            </div>

            <div class="about-block">
                <h2>{{ $isTr ? 'Değerlerimiz' : 'Our Values' }}</h2>
                <div class="row g-3 mt-2">
                    <div class="col-6 col-md-4">
                        <div class="value-card">
                            <div class="value-icon" style="background:rgba(255,107,53,.12);color:#FF6B35"><i class="bi bi-hand-thumbs-up"></i></div>
                            <h3>{{ $isTr ? 'Basitlik' : 'Simplicity' }}</h3>
                            <p>{{ $isTr ? 'Teknik bilgi gerektirmeyen, herkesin kullanabileceği arayüzler.' : 'Interfaces that require no technical knowledge and anyone can use.' }}</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="value-card">
                            <div class="value-icon" style="background:rgba(16,185,129,.12);color:#10b981"><i class="bi bi-shield-check"></i></div>
                            <h3>{{ $isTr ? 'Güvenilirlik' : 'Reliability' }}</h3>
                            <p>{{ $isTr ? 'Kesintisiz hizmet ve veri güvenliği.' : 'Uninterrupted service and data security.' }}</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="value-card">
                            <div class="value-icon" style="background:rgba(99,102,241,.12);color:#6366f1"><i class="bi bi-heart"></i></div>
                            <h3>{{ $isTr ? 'Erişilebilirlik' : 'Accessibility' }}</h3>
                            <p>{{ $isTr ? 'Ücretsiz plan ile herkes için dijital menü.' : 'Digital menu for everyone with a free plan.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-bar" style="padding-top:0">
        <div class="container">
            <div class="cta-box">
                <h2>{{ $isTr ? 'Siz de Dijitale Geçin' : 'Go Digital Today' }}</h2>
                <p>{{ $isTr ? 'Restoranınız için ücretsiz dijital menü oluşturun.' : 'Create a free digital menu for your restaurant.' }}</p>
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a href="{{ route('register') }}" class="hero-btn-primary" style="position:relative">
                        <i class="bi bi-rocket-takeoff"></i> {{ $isTr ? 'Ücretsiz Başla' : 'Start Free' }}
                    </a>
                    <a href="{{ route('features') }}" class="hero-btn-outline" style="position:relative">
                        <i class="bi bi-stars"></i> {{ $isTr ? 'Özellikleri Gör' : 'View Features' }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
