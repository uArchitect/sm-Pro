@php
    $locale = app()->getLocale();
    $title = $product->name . ' — ' . $tenant->restoran_adi;
    $description = __('public.product_description', [
        'restaurant' => $tenant->restoran_adi,
        'product' => $product->name,
        'category' => $product->category_name,
    ]);
    $canonical = request()->fullUrlWithoutQuery(['lang']);
    $currentUrl = $canonical . ($locale === config('app.fallback_locale', 'en') ? '' : '?lang=' . $locale);
    $shareImage = $product->image ? asset('uploads/' . $product->image) : ($tenant->logo ? asset('uploads/' . $tenant->logo) : asset('og-cover.svg'));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}">
<head>
    @if(config('services.google.gtm_id'))
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ config('services.google.gtm_id') }}');</script>
    <!-- End Google Tag Manager -->
    @endif
    @if(config('services.google.ga_id'))
    <!-- Google tag (gtag.js) -->
    <script src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.ga_id') }}"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    @if(config('services.google.ga_id_secondary'))
    gtag('config', '{{ config('services.google.ga_id_secondary') }}');
    @endif
    gtag('config', '{{ config('services.google.ga_id') }}');
    </script>
    @endif
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon-indigo.svg') }}">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="noindex, follow, max-image-preview:large">
    <link rel="canonical" href="{{ $currentUrl }}">
    <link rel="alternate" hreflang="tr" href="{{ $canonical }}?lang=tr">
    <link rel="alternate" hreflang="en" href="{{ $canonical }}?lang=en">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $currentUrl }}">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $shareImage }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; min-height: 100vh; }
        .product-card { border: none; border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); max-width: 480px; margin: 0 auto; overflow: hidden; }
        .product-header { background: linear-gradient(135deg, #1e2a3a 0%, #2d4059 100%); padding: 1.5rem; }
        .price-badge { font-size: 1.5rem; font-weight: 700; color: #f59e0b; }
        .product-image { width: 100%; height: 240px; object-fit: cover; background: #eef2f7; }
    </style>
</head>
<body class="d-flex flex-column align-items-center justify-content-start py-5 px-3">
    @if(config('services.google.gtm_id'))
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ config('services.google.gtm_id') }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    @endif

    <div class="product-card w-100">
        @if($product->image)
        <img src="{{ asset('uploads/' . $product->image) }}" alt="{{ $product->name }}" class="product-image" loading="lazy">
        @endif
        <div class="product-header text-center">
            <div class="text-white-50 small mb-1">
                <i class="bi bi-building me-1"></i>{{ $tenant->restoran_adi }}
            </div>
            <h4 class="text-white fw-bold mb-0">{{ $product->name }}</h4>
        </div>

        <div class="card-body p-4 bg-white">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <span class="badge bg-light text-dark border fs-6 px-3 py-2">
                    <i class="bi bi-grid me-1"></i>{{ $product->category_name }}
                </span>
                <div class="price-badge">
                    {{ number_format($product->price, 2, ',', '.') }} ₺
                </div>
            </div>

            @if($product->description)
            <hr class="my-3">
            <h6 class="fw-semibold text-muted small text-uppercase letter-spacing">{{ __('public.description') }}</h6>
            <p class="text-dark mb-0">{{ $product->description }}</p>
            @endif

            <div class="mt-4">
                <a href="{{ route('public.menu', ['tenantId' => $tenant->id, 'lang' => $locale]) }}" class="btn btn-outline-dark w-100">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('public.back_to_menu') }}
                </a>
            </div>
        </div>

        <div class="card-footer bg-light text-center py-3 border-0">
            <p class="text-muted small mb-0">
                <i class="bi bi-qr-code me-1"></i>
                {{ $tenant->restoran_adi }} — {{ __('public.powered_by') }}
            </p>
        </div>
    </div>

</body>
</html>
