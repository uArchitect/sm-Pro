@extends('layouts.public')

@php
    $locale = app()->getLocale();
    $isTr = $locale === 'tr';
    $cleanTitle = strip_tags($post->title);
    $metaTitle = strip_tags($post->meta_title ?: $cleanTitle) . ($isTr ? ' | Sipariş Masanda Blog' : ' | Siparis Masanda Blog');
    $metaDesc = $post->meta_description ?: Str::limit(strip_tags($post->body), 160);
    $ogImage = $post->featured_image ? asset('uploads/'.$post->featured_image) : asset('og-cover.svg');
@endphp

@section('title', $metaTitle)
@section('meta_description', $metaDesc)
@section('meta_keywords', $isTr
    ? 'dijital menü, qr menü, restoran menü sistemi, sipariş masanda blog, ' . Str::limit($cleanTitle, 50)
    : 'digital menu, qr menu, restaurant menu system, siparis masanda blog, ' . Str::limit($cleanTitle, 50))
@section('canonical', locale_route('blog.show', $post->slug))
@section('og_type', 'article')
@section('og_image', $ogImage)
@section('head_extra')
<meta property="article:published_time" content="{{ \Carbon\Carbon::parse($post->published_at)->toIso8601String() }}">
<meta property="article:modified_time" content="{{ \Carbon\Carbon::parse($post->updated_at)->toIso8601String() }}">
@if(!empty($post->author_name))
<meta property="article:author" content="{{ $post->author_name }}">
@endif
@endsection

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Article",
    "headline": "{{ addslashes($cleanTitle) }}",
    "description": "{{ addslashes($metaDesc) }}",
    "image": "{{ $ogImage }}",
    "datePublished": "{{ \Carbon\Carbon::parse($post->published_at)->toIso8601String() }}",
    "dateModified": "{{ \Carbon\Carbon::parse($post->updated_at)->toIso8601String() }}",
    "author": {
        "@@type": "Person",
        "name": "{{ addslashes($post->author_name ?? 'Sipariş Masanda') }}"
    },
    "publisher": {
        "@@type": "Organization",
        "name": "Sipariş Masanda",
        "logo": { "@@type": "ImageObject", "url": "{{ asset('og-cover.svg') }}" }
    },
    "mainEntityOfPage": { "@@type": "WebPage", "@@id": "{{ locale_route('blog.show', $post->slug) }}" }
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {"@@type": "ListItem", "position": 1, "name": "{{ $isTr ? 'Ana Sayfa' : 'Home' }}", "item": "{{ locale_route('home') }}"},
        {"@@type": "ListItem", "position": 2, "name": "{{ $isTr ? 'Blog' : 'Blog' }}", "item": "{{ locale_route('blog') }}"},
        {"@@type": "ListItem", "position": 3, "name": "{{ addslashes(Str::limit($cleanTitle, 50)) }}", "item": "{{ locale_route('blog.show', $post->slug) }}"}
    ]
}
</script>
@endsection

@section('content')
<section class="page-hero">
    <div class="container" style="max-width:720px">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small mb-0" style="background:transparent;color:#64748b">
                <li class="breadcrumb-item"><a href="{{ locale_route('home') }}" class="text-white-50">{{ $isTr ? 'Ana Sayfa' : 'Home' }}</a></li>
                <li class="breadcrumb-item"><a href="{{ locale_route('blog') }}" class="text-white-50">{{ $isTr ? 'Blog' : 'Blog' }}</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">{{ Str::limit($cleanTitle, 40) }}</li>
            </ol>
        </nav>
        <h1 class="mb-2" style="font-size:clamp(1.5rem,3vw,2rem);line-height:1.25">{{ $cleanTitle }}</h1>
        <p class="page-hero-sub mb-0">
            <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($post->published_at)->locale($locale)->isoFormat('D MMMM Y') }}
        </p>
    </div>
</section>

<section class="pb-5">
    <div class="container" style="max-width:720px">
        <div class="mb-4 rounded-3 overflow-hidden d-flex align-items-center justify-content-center blog-show-thumb">
            @if($post->featured_image)
                <img src="{{ asset('uploads/'.$post->featured_image) }}" alt="{{ $cleanTitle }}" class="blog-show-thumb-img" loading="eager" fetchpriority="high">
            @else
                <span class="blog-show-thumb-icon"><i class="bi bi-file-text"></i></span>
            @endif
        </div>
        <div class="blog-body glass-card p-4 p-md-5">
            {!! $post->body !!}
        </div>
        <div class="cta-box mt-5 p-4" style="border-radius:16px">
            <h3 style="font-size:1.1rem;font-weight:800;margin-bottom:.35rem;position:relative">{{ $isTr ? 'Restoranınız İçin QR Menü Oluşturun' : 'Create a QR Menu for Your Restaurant' }}</h3>
            <p style="font-size:.85rem;margin-bottom:1rem;position:relative">{{ $isTr ? 'Ücretsiz plan ile dakikalar içinde dijital menünüzü hazırlayın.' : 'Prepare your digital menu in minutes with the free plan.' }}</p>
            <div class="d-flex flex-wrap gap-2 justify-content-center" style="position:relative">
                <a href="{{ route('register') }}" class="hero-btn-primary" style="font-size:.85rem;padding:.6rem 1.25rem">
                    <i class="bi bi-rocket-takeoff"></i> {{ $isTr ? 'Ücretsiz Başla' : 'Start Free' }}
                </a>
                <a href="{{ locale_route('pricing') }}" class="hero-btn-outline" style="font-size:.85rem;padding:.6rem 1.25rem">
                    <i class="bi bi-tag"></i> {{ $isTr ? 'Fiyatları Gör' : 'View Pricing' }}
                </a>
            </div>
        </div>
        <div class="mt-4 text-center">
            <a href="{{ locale_route('blog') }}" class="hero-btn-outline btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ $isTr ? 'Tüm Yazılar' : 'All Posts' }}
            </a>
        </div>
    </div>
</section>
@endsection

@section('styles')
.blog-show-thumb{background:linear-gradient(145deg,rgba(79,70,229,.06) 0%,rgba(108,92,231,.05) 100%);border:1px solid rgba(255,255,255,.05);border-radius:1rem;display:flex;align-items:center;justify-content:center}
.blog-show-thumb-img{width:100%;height:100%;object-fit:cover;display:block}
.blog-show-thumb-icon{width:72px;height:72px;border-radius:18px;background:#eef2ff;border:1px solid #e0e7ff;display:flex;align-items:center;justify-content:center;font-size:2rem;color:#4338CA}
.blog-body { font-size:1rem; line-height:1.85; color:#334155; word-wrap:break-word; overflow-wrap:break-word; }
.blog-body > *:first-child { margin-top:0; }
.blog-body h2 { font-size:clamp(1.15rem,2.5vw,1.35rem); font-weight:800; margin:2rem 0 .85rem; padding-bottom:.5rem; color:#1e293b; border-bottom:1px solid #e2e8f0; line-height:1.35; }
.blog-body h3 { font-size:1.1rem; font-weight:700; margin:1.5rem 0 .65rem; color:#1e293b; }
.blog-body h4 { font-size:1rem; font-weight:700; margin:1.25rem 0 .5rem; color:#334155; }
.blog-body p { margin-bottom:1.1rem; }
.blog-body p:last-child { margin-bottom:0; }
.blog-body strong, .blog-body b { color:#1e293b; font-weight:700; }
.blog-body ul, .blog-body ol { margin:0 0 1.25rem; padding-left:1.35rem; }
.blog-body ul { list-style:disc; }
.blog-body ol { list-style:decimal; }
.blog-body li { margin-bottom:.45rem; padding-left:.25rem; }
.blog-body li::marker { color:rgba(99,102,241,.85); }
.blog-body blockquote {
    margin:1.5rem 0; padding:1.15rem 1.25rem 1.15rem 1.35rem;
    border-left:4px solid #4F46E5;
    background:rgba(79,70,229,.08);
    border-radius:0 12px 12px 0;
    font-style:italic;
    color:#334155;
    box-shadow:inset 0 0 0 1px rgba(79,70,229,.1);
}
.blog-body blockquote p { margin:0; font-size:.98rem; line-height:1.75; }
.blog-body blockquote p + p { margin-top:.75rem; }
.blog-body blockquote cite, .blog-body blockquote footer { display:block; margin-top:.75rem; font-size:.85rem; font-style:normal; color:#64748b; }
.blog-body hr { border:0; height:1px; background:linear-gradient(90deg,transparent,rgba(255,255,255,.12),transparent); margin:1.75rem 0; }
.blog-body a { color:#6366F1; text-decoration:underline; text-underline-offset:2px; }
.blog-body a:hover { color:#818CF8; }
.blog-body img { max-width:100%; height:auto; border-radius:8px; }
.blog-body code { background:rgba(255,255,255,.08); padding:.15rem .4rem; border-radius:6px; font-size:.9em; }
.blog-body pre { background:rgba(0,0,0,.25); padding:1rem; border-radius:10px; overflow-x:auto; margin:1rem 0; font-size:.88rem; }
.blog-body table { width:100%; border-collapse:collapse; margin:1rem 0; font-size:.92rem; }
.blog-body th, .blog-body td { border:1px solid rgba(255,255,255,.1); padding:.5rem .65rem; text-align:left; }
.blog-body th { background:#eef2ff; color:#1e293b; }
@endsection
