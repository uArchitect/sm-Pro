@extends('layouts.public')

@php
    $locale = app()->getLocale();
    $isTr = $locale === 'tr';
    $metaTitle = $post->meta_title ?: $post->title . ($isTr ? ' | Sipariş Masanda Blog' : ' | Siparis Masanda Blog');
    $metaDesc = $post->meta_description ?: Str::limit(strip_tags($post->body), 160);
    $ogImage = $post->featured_image ? asset('uploads/'.$post->featured_image) : asset('og-cover.svg');
@endphp

@section('title', $metaTitle)
@section('meta_description', $metaDesc)
@section('canonical', route('blog.show', $post->slug))
@section('og_type', 'article')
@section('og_image', $ogImage)

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Article",
    "headline": "{{ addslashes($post->title) }}",
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
    "mainEntityOfPage": { "@@type": "WebPage", "@@id": "{{ route('blog.show', $post->slug) }}" }
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {"@@type": "ListItem", "position": 1, "name": "{{ $isTr ? 'Ana Sayfa' : 'Home' }}", "item": "{{ url('/') }}"},
        {"@@type": "ListItem", "position": 2, "name": "{{ $isTr ? 'Blog' : 'Blog' }}", "item": "{{ route('blog') }}"},
        {"@@type": "ListItem", "position": 3, "name": "{{ addslashes(Str::limit($post->title, 50)) }}", "item": "{{ route('blog.show', $post->slug) }}"}
    ]
}
</script>
@endsection

@section('content')
<section class="page-hero">
    <div class="container" style="max-width:720px">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small mb-0" style="background:transparent;color:rgba(255,255,255,.5)">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white-50">{{ $isTr ? 'Ana Sayfa' : 'Home' }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('blog') }}" class="text-white-50">{{ $isTr ? 'Blog' : 'Blog' }}</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">{{ Str::limit($post->title, 40) }}</li>
            </ol>
        </nav>
        <h1 class="mb-2" style="font-size:clamp(1.5rem,3vw,2rem);line-height:1.25">{{ $post->title }}</h1>
        <p class="page-hero-sub mb-0">
            <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($post->published_at)->locale($locale)->isoFormat('D MMMM Y') }}
        </p>
    </div>
</section>

<section class="pb-5">
    <div class="container" style="max-width:720px">
        <div class="mb-4 rounded-3 overflow-hidden d-flex align-items-center justify-content-center blog-show-thumb">
            @if($post->featured_image)
                <img src="{{ asset('uploads/'.$post->featured_image) }}" alt="{{ $post->title }}" class="blog-show-thumb-img">
            @else
                <span class="blog-show-thumb-icon"><i class="bi bi-file-text"></i></span>
            @endif
        </div>
        <div class="blog-body glass-card p-4 p-md-5">
            {!! $post->body !!}
        </div>
        <div class="mt-4 text-center">
            <a href="{{ route('blog') }}" class="hero-btn-outline btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ $isTr ? 'Tüm Yazılar' : 'All Posts' }}
            </a>
        </div>
    </div>
</section>
@endsection

@section('styles')
.blog-show-thumb{background:linear-gradient(145deg,rgba(255,107,53,.06) 0%,rgba(108,92,231,.05) 100%);border:1px solid rgba(255,255,255,.05);border-radius:1rem;display:flex;align-items:center;justify-content:center}
.blog-show-thumb-img{width:100%;height:100%;object-fit:cover;display:block}
.blog-show-thumb-icon{width:72px;height:72px;border-radius:18px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;font-size:2rem;color:rgba(255,255,255,.2)}
.blog-body { font-size:1rem; line-height:1.85; color:rgba(255,255,255,.88); word-wrap:break-word; overflow-wrap:break-word; }
.blog-body > *:first-child { margin-top:0; }
.blog-body h2 { font-size:clamp(1.15rem,2.5vw,1.35rem); font-weight:800; margin:2rem 0 .85rem; padding-bottom:.5rem; color:#fff; border-bottom:1px solid rgba(255,107,53,.2); line-height:1.35; }
.blog-body h3 { font-size:1.1rem; font-weight:700; margin:1.5rem 0 .65rem; color:#fff; }
.blog-body h4 { font-size:1rem; font-weight:700; margin:1.25rem 0 .5rem; color:rgba(255,255,255,.95); }
.blog-body p { margin-bottom:1.1rem; }
.blog-body p:last-child { margin-bottom:0; }
.blog-body strong, .blog-body b { color:#fff; font-weight:700; }
.blog-body ul, .blog-body ol { margin:0 0 1.25rem; padding-left:1.35rem; }
.blog-body ul { list-style:disc; }
.blog-body ol { list-style:decimal; }
.blog-body li { margin-bottom:.45rem; padding-left:.25rem; }
.blog-body li::marker { color:rgba(255,140,66,.85); }
.blog-body blockquote {
    margin:1.5rem 0; padding:1.15rem 1.25rem 1.15rem 1.35rem;
    border-left:4px solid #FF6B35;
    background:rgba(255,107,53,.08);
    border-radius:0 12px 12px 0;
    font-style:italic;
    color:rgba(255,255,255,.92);
    box-shadow:inset 0 0 0 1px rgba(255,107,53,.12);
}
.blog-body blockquote p { margin:0; font-size:.98rem; line-height:1.75; }
.blog-body blockquote p + p { margin-top:.75rem; }
.blog-body blockquote cite, .blog-body blockquote footer { display:block; margin-top:.75rem; font-size:.85rem; font-style:normal; color:rgba(255,255,255,.55); }
.blog-body hr { border:0; height:1px; background:linear-gradient(90deg,transparent,rgba(255,255,255,.12),transparent); margin:1.75rem 0; }
.blog-body a { color:#FF8C42; text-decoration:underline; text-underline-offset:2px; }
.blog-body a:hover { color:#FFB347; }
.blog-body img { max-width:100%; height:auto; border-radius:8px; }
.blog-body code { background:rgba(255,255,255,.08); padding:.15rem .4rem; border-radius:6px; font-size:.9em; }
.blog-body pre { background:rgba(0,0,0,.25); padding:1rem; border-radius:10px; overflow-x:auto; margin:1rem 0; font-size:.88rem; }
.blog-body table { width:100%; border-collapse:collapse; margin:1rem 0; font-size:.92rem; }
.blog-body th, .blog-body td { border:1px solid rgba(255,255,255,.1); padding:.5rem .65rem; text-align:left; }
.blog-body th { background:rgba(255,107,53,.1); color:#fff; }
@endsection
