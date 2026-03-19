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
        body {
            background: radial-gradient(circle at top, #eef2ff 0%, #f8fafc 45%, #f8fafc 100%);
            min-height: 100vh;
            color: #0f172a;
            margin: 0;
        }
        .product-card {
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            box-shadow: 0 12px 40px rgba(15, 23, 42, 0.10);
            max-width: 520px;
            margin: 0 auto;
            overflow: hidden;
            background: #fff;
            min-height: calc(100vh - 3rem);
            display: flex;
            flex-direction: column;
        }
        .product-header {
            background: linear-gradient(135deg, #312e81 0%, #4f46e5 55%, #6366f1 100%);
            padding: 1.25rem 1.25rem 1.2rem;
            text-align: center;
        }
        .image-wrap {
            display: flex;
            justify-content: center;
            padding: 1rem 1rem 0;
        }
        .product-image {
            width: min(82vw, 320px);
            height: min(82vw, 320px);
            object-fit: cover;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 26px rgba(0,0,0,.12);
            background: #eef2f7;
        }
        .product-image-fallback {
            width: min(82vw, 320px);
            height: min(82vw, 320px);
            border-radius: 16px;
            border: 1px solid #c7d2fe;
            box-shadow: 0 6px 18px rgba(79,70,229,.18);
            background: linear-gradient(145deg, #eef2ff, #e0e7ff);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4f46e5;
            font-size: 3rem;
        }
        .price-cta {
            border: none;
            border-radius: 14px;
            padding: .85rem 1rem;
            font-size: 1rem;
            font-weight: 800;
            color: #fff;
            width: 100%;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            box-shadow: 0 10px 24px rgba(79, 70, 229, .35);
            cursor: default;
        }
        .desc-wrap {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: .9rem .95rem;
        }
        .card-body {
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        .product-main {
            margin-top: auto;
        }
    </style>
</head>
<body class="d-flex flex-column align-items-center py-4 px-3">
    @if(config('services.google.gtm_id'))
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ config('services.google.gtm_id') }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    @endif

    <div class="product-card w-100">
        <div class="product-header">
            <h4 class="text-white fw-bold mb-0">{{ $product->name }}</h4>
        </div>
        @if($product->image)
        <div class="image-wrap">
            <img src="{{ asset('uploads/' . $product->image) }}" alt="{{ $product->name }}" class="product-image" loading="lazy">
        </div>
        @else
        <div class="d-flex justify-content-center pt-3">
            <div class="product-image-fallback" aria-hidden="true">
                <i class="bi bi-image"></i>
            </div>
        </div>
        @endif

        <div class="card-body px-4 pb-4 pt-3 bg-white">
            <div class="product-main">

            @if($product->description)
            <div class="desc-wrap mb-3">
                <h6 class="fw-semibold text-muted small text-uppercase mb-2">{{ __('public.description') }}</h6>
                <p class="text-dark mb-0 text-center">{{ $product->description }}</p>
            </div>
            @endif

            <div class="mb-3">
                <button type="button" class="price-cta">
                    {{ number_format($product->price, 2, ',', '.') }} ₺
                </button>
            </div>

            <div class="mt-2">
                <a href="{{ route('public.menu', ['tenantId' => $tenant->id, 'lang' => $locale]) }}" class="btn btn-outline-dark w-100">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('public.back_to_menu') }}
                </a>
            </div>
            </div>
        </div>

        <div class="card-footer bg-light text-center py-3 border-0">
            <p class="text-muted small mb-0">
                <i class="bi bi-qr-code me-1"></i>
                {{ __('public.powered_by') }}
            </p>
        </div>
    </div>

</body>
</html>
