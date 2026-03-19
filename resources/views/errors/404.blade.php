@extends('layouts.public')

@php
    $locale = app()->getLocale();
    $isTr = $locale === 'tr';
@endphp

@section('title', $isTr ? 'Sayfa Bulunamadı (404) | Sipariş Masanda' : 'Page Not Found (404) | Sipariş Masanda')
@section('meta_description', $isTr
    ? 'Aradığınız sayfa bulunamadı. Ana sayfaya dönün veya dijital menü özelliklerini inceleyin.'
    : 'The page you are looking for was not found. Return to the homepage or explore our digital menu features.')
@section('canonical', url()->current())

{{-- 404 sayfaları indekslenmesin, linkler takip edilsin --}}
@section('meta_robots')
<meta name="robots" content="noindex, follow">
@endsection

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "{{ $isTr ? 'Sayfa Bulunamadı' : 'Page Not Found' }}",
    "description": "{{ $isTr ? 'Aradığınız sayfa mevcut değil.' : 'The requested page does not exist.' }}",
    "url": "{{ url()->current() }}",
    "mainEntity": {
        "@@type": "Organization",
        "name": "Sipariş Masanda",
        "url": "{{ url('/') }}"
    }
}
</script>
@endsection

@section('content')
<section class="page-hero">
    <div class="container text-center">
        <div class="mb-4" style="font-size:4rem;font-weight:800;color:rgba(79,70,229,.12);line-height:1">404</div>
        <h1 class="mb-3">{{ $isTr ? 'Sayfa Bulunamadı' : 'Page Not Found' }}</h1>
        <p class="page-hero-sub mx-auto" style="max-width:480px">
            {{ $isTr
                ? 'Aradığınız sayfa kaldırılmış, adresi değişmiş veya geçici olarak kullanılamıyor olabilir.'
                : 'The page you are looking for may have been removed, had its address changed, or is temporarily unavailable.' }}
        </p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="glass-card p-4 p-md-5 text-center">
                    <p class="mb-4" style="color:#475569">
                        {{ $isTr ? 'Ana sayfaya dönerek veya aşağıdaki linkleri kullanarak devam edebilirsiniz.' : 'You can continue by returning to the homepage or using the links below.' }}
                    </p>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="{{ locale_route('home') }}" class="hero-btn-primary">
                            <i class="bi bi-house me-1"></i>{{ $isTr ? 'Ana Sayfa' : 'Home' }}
                        </a>
                        <a href="{{ locale_route('features') }}" class="hero-btn-outline">{{ $isTr ? 'Özellikler' : 'Features' }}</a>
                        <a href="{{ locale_route('pricing') }}" class="hero-btn-outline">{{ $isTr ? 'Fiyatlar' : 'Pricing' }}</a>
                        <a href="{{ locale_route('blog') }}" class="hero-btn-outline">Blog</a>
                        <a href="{{ locale_route('contact') }}" class="hero-btn-outline">{{ $isTr ? 'İletişim' : 'Contact' }}</a>
                    </div>
                    <p class="small mt-4 mb-0" style="color:#94a3b8">
                        <a href="{{ locale_route('sitemap') }}" style="color:#64748b;text-decoration:none">{{ $isTr ? 'Site haritası' : 'Sitemap' }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
