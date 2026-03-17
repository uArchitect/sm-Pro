@extends('layouts.public')

@php
    $locale = app()->getLocale();
    $isTr = $locale === 'tr';
@endphp

@section('title', $isTr ? 'Blog | Sipariş Masanda' : 'Blog | Siparis Masanda')
@section('meta_description', $isTr
    ? 'Dijital menü, QR menü ve restoran yönetimi hakkında ipuçları ve güncel yazılar.'
    : 'Tips and articles about digital menu, QR menu and restaurant management.')
@section('meta_keywords', $isTr
    ? 'dijital menü blog, qr menü ipuçları, restoran yönetimi, dijital menü yazıları, sipariş masanda blog'
    : 'digital menu blog, qr menu tips, restaurant management, digital menu articles, siparis masanda blog')
@section('canonical', route('blog'))

@section('styles')
.blog-card-excerpt{font-size:.8rem;line-height:1.6;color:rgba(255,255,255,.85)}
.blog-card-date{color:rgba(255,255,255,.75)}
.blog-card-thumb{background:linear-gradient(145deg,rgba(255,107,53,.08) 0%,rgba(108,92,231,.06) 100%);border-bottom:1px solid rgba(255,255,255,.05);display:flex;align-items:center;justify-content:center;transition:background .2s}
.blog-card-thumb:hover{background:linear-gradient(145deg,rgba(255,107,53,.12) 0%,rgba(108,92,231,.08) 100%)}
.blog-card-thumb-img{width:100%;height:100%;object-fit:cover;display:block;min-height:200px}
.blog-card-thumb-icon{width:64px;height:64px;border-radius:16px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;font-size:1.75rem;color:rgba(255,255,255,.25)}
.blog-card-thumb:hover .blog-card-thumb-icon{color:rgba(255,107,53,.5);border-color:rgba(255,107,53,.2)}
@endsection

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Blog",
    "name": "{{ $isTr ? 'Sipariş Masanda Blog' : 'Siparis Masanda Blog' }}",
    "url": "{{ route('blog') }}",
    "publisher": {
        "@@type": "Organization",
        "name": "Sipariş Masanda",
        "logo": { "@@type": "ImageObject", "url": "{{ asset('og-cover.svg') }}" }
    }
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {"@@type": "ListItem", "position": 1, "name": "{{ $isTr ? 'Ana Sayfa' : 'Home' }}", "item": "{{ url('/') }}"},
        {"@@type": "ListItem", "position": 2, "name": "{{ $isTr ? 'Blog' : 'Blog' }}", "item": "{{ route('blog') }}"}
    ]
}
</script>
@endsection

@section('content')
<section class="page-hero">
    <div class="container">
        <h1>{{ $isTr ? 'Blog' : 'Blog' }} <span class="accent">{{ $isTr ? 'Yazılar ve İpuçları' : 'Articles & Tips' }}</span></h1>
        <p class="page-hero-sub">{{ $isTr ? 'Dijital menü ve restoran yönetimi hakkında güncel içerikler.' : 'Latest content on digital menu and restaurant management.' }}</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        @if($posts->isEmpty())
            <div class="text-center py-5">
                <p class="text-white opacity-90">{{ $isTr ? 'Henüz yayınlanmış yazı yok.' : 'No published posts yet.' }}</p>
                <a href="{{ route('register') }}" class="hero-btn-outline mt-2">{{ $isTr ? 'Ücretsiz Başla' : 'Start Free' }}</a>
            </div>
        @else
            <div class="row g-4">
                @foreach($posts as $post)
                <div class="col-md-6 col-lg-4">
                    <article class="glass-card h-100 d-flex flex-column">
                        <a href="{{ route('blog.show', $post->slug) }}" class="d-block rounded-top overflow-hidden text-decoration-none blog-card-thumb" style="margin:-1.75rem -1.75rem 1rem -1.75rem">
                            @if($post->featured_image)
                                <img src="{{ asset('uploads/'.$post->featured_image) }}" alt="{{ $post->title }}" class="blog-card-thumb-img" loading="lazy">
                            @else
                                <span class="blog-card-thumb-icon"><i class="bi bi-file-text"></i></span>
                            @endif
                        </a>
                        <div class="flex-grow-1">
                            <h2 class="h6 fw-700 mb-2" style="line-height:1.35">
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none text-white">{{ Str::limit($post->title, 60) }}</a>
                            </h2>
                            <p class="small mb-2 blog-card-excerpt">
                                {{ Str::limit(strip_tags($post->meta_description ?: $post->body), 120) }}
                            </p>
                            <div class="small blog-card-date">
                                <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($post->published_at)->format('d.m.Y') }}
                            </div>
                        </div>
                        <a href="{{ route('blog.show', $post->slug) }}" class="mt-3 btn btn-sm btn-outline-light align-self-start">
                            {{ $isTr ? 'Oku' : 'Read' }} <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </article>
                </div>
                @endforeach
            </div>
            @if($posts->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $posts->links() }}
                </div>
            @endif
        @endif
    </div>
</section>
@endsection
