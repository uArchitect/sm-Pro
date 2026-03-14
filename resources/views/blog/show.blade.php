@extends('layouts.public')

@php
    $locale = app()->getLocale();
    $isTr = $locale === 'tr';
    $metaTitle = $post->meta_title ?: $post->title . ($isTr ? ' | Sipariş Masanda Blog' : ' | Siparis Masanda Blog');
    $metaDesc = $post->meta_description ?: Str::limit(strip_tags($post->body), 160);
    $ogImage = $post->featured_image ? asset('storage/'.$post->featured_image) : asset('og-cover.svg');
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
        @if($post->featured_image)
            <img src="{{ asset('storage/'.$post->featured_image) }}" alt="" class="img-fluid rounded-3 w-100 mb-4" style="max-height:400px;object-fit:cover">
        @endif
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
.blog-body { font-size:1rem; line-height:1.85; color:rgba(255,255,255,.85); }
.blog-body h2,.blog-body h3 { font-size:1.2rem; font-weight:700; margin-top:1.5rem; margin-bottom:.75rem; color:#fff; }
.blog-body p { margin-bottom:1rem; }
.blog-body ul,.blog-body ol { margin-bottom:1rem; padding-left:1.5rem; }
.blog-body a { color:#FF8C42; text-decoration:underline; }
.blog-body a:hover { color:#FFB347; }
.blog-body img { max-width:100%; height:auto; border-radius:8px; }
@endsection
