@extends('layouts.public')

@php
    $locale = app()->getLocale();
    $isTr = $locale === 'tr';
    $cleanTitle = strip_tags($post->title);
    $metaTitle = strip_tags($post->meta_title ?: $cleanTitle) . ($isTr ? ' | Sipariş Masanda Blog' : ' | Siparis Masanda Blog');
    $metaDesc = $post->meta_description ?: Str::limit(strip_tags($post->body), 160);
    $ogImage = $post->featured_image ? asset('uploads/'.$post->featured_image) : asset('og-cover.svg');

    $schemaTitle      = $cleanTitle;
    $schemaDesc       = $metaDesc;
    $schemaAuthor     = $post->author_name ?? 'Sipariş Masanda';
    $schemaBreadcrumb = $isTr ? 'Ana Sayfa' : 'Home';
    $schemaPub        = \Carbon\Carbon::parse($post->published_at)->toIso8601String();
    $schemaMod        = \Carbon\Carbon::parse($post->updated_at)->toIso8601String();
    $schemaUrl        = locale_route('blog.show', $post->slug);
    $schemaBlog       = locale_route('blog');
    $schemaHome       = locale_route('home');
    $schemaLogo       = asset('og-cover.svg');
    $schemaCrumbTitle = Str::limit($cleanTitle, 50);
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
    "headline": {!! json_encode($schemaTitle) !!},
    "description": {!! json_encode($schemaDesc) !!},
    "image": {!! json_encode($ogImage) !!},
    "datePublished": {!! json_encode($schemaPub) !!},
    "dateModified": {!! json_encode($schemaMod) !!},
    "author": {
        "@@type": "Person",
        "name": {!! json_encode($schemaAuthor) !!}
    },
    "publisher": {
        "@@type": "Organization",
        "name": "Sipariş Masanda",
        "logo": { "@@type": "ImageObject", "url": {!! json_encode($schemaLogo) !!} }
    },
    "mainEntityOfPage": { "@@type": "WebPage", "@@id": {!! json_encode($schemaUrl) !!} }
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {"@@type": "ListItem", "position": 1, "name": {!! json_encode($schemaBreadcrumb) !!}, "item": {!! json_encode($schemaHome) !!}},
        {"@@type": "ListItem", "position": 2, "name": "Blog", "item": {!! json_encode($schemaBlog) !!}},
        {"@@type": "ListItem", "position": 3, "name": {!! json_encode($schemaCrumbTitle) !!}, "item": {!! json_encode($schemaUrl) !!}}
    ]
}
</script>
@endsection

@section('content')
<section class="page-hero">
    <div class="container" style="max-width:720px">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small mb-0" style="background:transparent;color:#64748b">
                <li class="breadcrumb-item"><a href="{{ locale_route('home') }}" style="color:#64748b;text-decoration:none">{{ $isTr ? 'Ana Sayfa' : 'Home' }}</a></li>
                <li class="breadcrumb-item"><a href="{{ locale_route('blog') }}" style="color:#64748b;text-decoration:none">{{ $isTr ? 'Blog' : 'Blog' }}</a></li>
                <li class="breadcrumb-item active" style="color:#1e293b" aria-current="page">{{ Str::limit($cleanTitle, 40) }}</li>
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
.blog-show-thumb{background:linear-gradient(145deg,rgba(79,70,229,.06) 0%,rgba(108,92,231,.04) 100%);border:1px solid #e2e8f0;border-radius:1rem;display:flex;align-items:center;justify-content:center}
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
.blog-body hr { border:0; height:1px; background:linear-gradient(90deg,transparent,#e2e8f0,transparent); margin:1.75rem 0; }
.blog-body a { color:#6366F1; text-decoration:underline; text-underline-offset:2px; }
.blog-body a:hover { color:#818CF8; }
.blog-body img { max-width:100%; height:auto; border-radius:8px; }
.blog-body code { background:#f1f5f9; padding:.15rem .4rem; border-radius:6px; font-size:.9em; color:#334155; }
.blog-body pre { background:#1e293b; color:#e2e8f0; padding:1rem; border-radius:10px; overflow-x:auto; margin:1rem 0; font-size:.88rem; }
.blog-body table { width:100%; border-collapse:collapse; margin:1rem 0; font-size:.92rem; }
.blog-body th, .blog-body td { border:1px solid #e2e8f0; padding:.5rem .65rem; text-align:left; }
.blog-body th { background:#eef2ff; color:#1e293b; }
/* İçindekiler smooth scroll + navbar offset */
html { scroll-behavior: smooth; }
.blog-body h2, .blog-body h3, .blog-body h4 { scroll-margin-top: 90px; }
@endsection

@section('scripts')
<script>
(function () {
    /* 1) Başlıklara otomatik id üret (eğer yoksa) */
    function normalizeText(text) {
        return (text || '')
            .trim()
            .toLowerCase()
            .replace(/ş/g,'s').replace(/ğ/g,'g').replace(/ü/g,'u')
            .replace(/ö/g,'o').replace(/ı/g,'i').replace(/ç/g,'c')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function toSlug(text) {
        return normalizeText(text)
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    function stemToken(token) {
        return (token || '').replace(/(lar|ler)$/g, '');
    }

    function parseHashHref(href) {
        if (!href) return '';
        var hashIndex = href.indexOf('#');
        if (hashIndex === -1) return '';
        return href.slice(hashIndex + 1);
    }

    var body = document.querySelector('.blog-body');
    if (!body) return;

    var headings = body.querySelectorAll('h2, h3, h4');
    var usedSlugs = {};
    var headingIndex = [];

    headings.forEach(function (h) {
        var textSlug = toSlug(h.textContent);
        if (!h.id) {
            var base = textSlug || 'baslik';
            var slug = base;
            var i = 2;
            while (usedSlugs[slug]) { slug = base + '-' + i++; }
            usedSlugs[slug] = true;
            h.id = slug;
        } else {
            usedSlugs[h.id] = true;
        }

        headingIndex.push({
            el: h,
            id: h.id,
            slug: textSlug,
            norm: normalizeText(h.textContent).replace(/\s+/g, ''),
        });
    });

    function resolveHeadingByToken(rawToken) {
        var decoded = decodeURIComponent((rawToken || '').trim());
        if (!decoded) return null;

        var exact = document.getElementById(decoded);
        if (exact) return exact;

        var slugToken = toSlug(decoded);
        if (!slugToken) return null;

        exact = document.getElementById(slugToken);
        if (exact) return exact;

        var normToken = slugToken.replace(/-/g, '');
        var stem = stemToken(normToken);

        var bySlug = headingIndex.find(function (h) {
            return h.id === slugToken || h.slug === slugToken;
        });
        if (bySlug) return bySlug.el;

        var byContains = headingIndex.find(function (h) {
            return h.norm.indexOf(normToken) !== -1 || normToken.indexOf(h.norm) !== -1;
        });
        if (byContains) return byContains.el;

        if (stem && stem.length >= 3) {
            var byStem = headingIndex.find(function (h) {
                return h.norm.indexOf(stem) !== -1;
            });
            if (byStem) return byStem.el;
        }

        return null;
    }

    function scrollToHeading(target) {
        if (!target) return;
        window.scrollTo({
            top: target.getBoundingClientRect().top + window.scrollY - 80,
            behavior: 'smooth'
        });
    }

    /* 2) Sayfa açıkken URL'de hash varsa offset ile scroll yap */
    if (window.location.hash) {
        setTimeout(function () {
            var target = resolveHeadingByToken(window.location.hash.slice(1));
            scrollToHeading(target);
        }, 100);
    }

    /* 3) Sayfadaki tüm #anchor linklerine smooth scroll + offset uygula */
    document.querySelectorAll('a[href*="#"]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var href = a.getAttribute('href') || '';
            var rawToken = parseHashHref(href);
            if (!rawToken) return;

            /* Sadece aynı sayfadaki hash linkleri ele al */
            if (href.indexOf('http') === 0) {
                try {
                    var u = new URL(href, window.location.origin);
                    if (u.pathname !== window.location.pathname || u.origin !== window.location.origin) {
                        return;
                    }
                } catch (_) {
                    return;
                }
            }

            var target = resolveHeadingByToken(rawToken);
            if (!target) return;

            e.preventDefault();
            scrollToHeading(target);
            history.replaceState(null, '', '#' + target.id);
        });
    });
})();
</script>
@endsection
