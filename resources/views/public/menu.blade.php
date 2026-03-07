<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tenant->restoran_adi }} — Dijital Menü</title>
    <meta name="description" content="{{ $tenant->restoran_adi }} dijital menüsü">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f8f9fb; color: #1a1a2e; }

        .menu-header {
            background: linear-gradient(135deg, #0f1923 0%, #1e2d40 100%);
            padding: 2.5rem 1rem 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .menu-header::before {
            content: '';
            position: absolute; inset: 0;
            background-image: linear-gradient(rgba(255,107,53,.05) 1px,transparent 1px),
                              linear-gradient(90deg,rgba(255,107,53,.05) 1px,transparent 1px);
            background-size: 40px 40px;
        }
        .menu-header .logo-mark {
            width: 60px; height: 60px; border-radius: 16px;
            background: linear-gradient(135deg,#FF6B35,#FF8C42);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: #fff; margin: 0 auto 1rem;
            box-shadow: 0 8px 28px rgba(255,107,53,.45);
            position: relative; z-index: 1;
        }
        .menu-header h1 {
            font-size: clamp(1.5rem,4vw,2.2rem); font-weight: 800; color: #fff;
            position: relative; z-index: 1; margin-bottom: .35rem;
        }
        .menu-header .subtitle {
            color: rgba(255,255,255,.45); font-size: .9rem;
            position: relative; z-index: 1;
        }

        .sticky-nav {
            position: sticky; top: 0; z-index: 50;
            background: #fff; border-bottom: 1px solid #e5e7eb;
            padding: .6rem 1rem;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
        }
        .cat-pill {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .4rem .9rem; border-radius: 100px;
            font-size: .8rem; font-weight: 600; white-space: nowrap;
            background: #f3f4f6; color: #374151;
            text-decoration: none; transition: all .15s; border: 1px solid transparent;
        }
        .cat-pill:hover, .cat-pill.active {
            background: rgba(255,107,53,.1); color: #FF6B35; border-color: rgba(255,107,53,.25);
        }

        .section-title {
            font-size: 1rem; font-weight: 800; color: #111827;
            margin-bottom: 1rem; padding-top: .5rem;
            display: flex; align-items: center; gap: .5rem;
        }
        .section-title::after {
            content: ''; flex: 1; height: 1px; background: #e5e7eb;
        }

        .product-card {
            background: #fff; border: 1px solid #e5e7eb;
            border-radius: 14px; padding: .85rem 1rem;
            display: flex; align-items: center; gap: .9rem;
            transition: box-shadow .15s, transform .15s;
        }
        .product-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08); transform: translateY(-1px); }
        .product-thumb {
            width: 60px; height: 60px; border-radius: 10px;
            object-fit: cover; flex-shrink: 0;
        }
        .product-thumb-empty {
            width: 60px; height: 60px; border-radius: 10px;
            background: rgba(255,107,53,.08); color: #FF6B35;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; flex-shrink: 0;
        }
        .product-name { font-weight: 700; font-size: .9rem; color: #111827; line-height: 1.3; }
        .product-desc { font-size: .8rem; color: #6b7280; margin-top: .2rem; line-height: 1.5; }
        .product-price {
            font-size: 1rem; font-weight: 800; color: #FF6B35;
            white-space: nowrap;
        }

        .footer-bar {
            background: #0f1923; color: rgba(255,255,255,.35);
            text-align: center; font-size: .75rem; padding: 1.25rem;
        }
        .footer-bar a { color: #FF8C42; text-decoration: none; }
    </style>
</head>
<body>

<div class="menu-header">
    <div class="logo-mark"><i class="bi bi-qr-code-scan"></i></div>
    <h1>{{ $tenant->restoran_adi }}</h1>
    <div class="subtitle">{{ $tenant->firma_adi }} &nbsp;·&nbsp; Dijital Menü</div>
</div>

@if($categories->isNotEmpty())
<div class="sticky-nav">
    <div class="d-flex gap-2 overflow-auto pb-1" style="-webkit-overflow-scrolling:touch;">
        @foreach($categories as $cat)
            <a href="#cat-{{ $cat->id }}" class="cat-pill">
                <i class="bi bi-circle-fill" style="font-size:.45rem"></i>{{ $cat->name }}
            </a>
        @endforeach
    </div>
</div>
@endif

<div class="container py-4" style="max-width:680px">

    @if($categories->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-box-seam fs-1 d-block mb-3 opacity-25"></i>
            <div class="fw-semibold">Menü henüz hazırlanmadı.</div>
        </div>
    @else
        @foreach($categories as $cat)
            @php $catProducts = $products->get($cat->id, collect()); @endphp
            @if($catProducts->isNotEmpty())
            <div id="cat-{{ $cat->id }}" class="mb-4">
                <div class="section-title">
                    <i class="bi bi-grid-3x3-gap-fill" style="color:#FF6B35;font-size:.8rem"></i>
                    {{ $cat->name }}
                </div>
                <div class="d-flex flex-column gap-2">
                    @foreach($catProducts as $product)
                    <div class="product-card">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" class="product-thumb" alt="{{ $product->name }}">
                        @else
                            <div class="product-thumb-empty"><i class="bi bi-box-seam"></i></div>
                        @endif
                        <div class="flex-grow-1">
                            <div class="product-name">{{ $product->name }}</div>
                            @if($product->description)
                            <div class="product-desc">{{ $product->description }}</div>
                            @endif
                        </div>
                        <div class="product-price">{{ number_format($product->price, 2, ',', '.') }} ₺</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    @endif
</div>

<div class="footer-bar">
    <i class="bi bi-qr-code me-1"></i>
    <a href="{{ route('home') }}">Sipariş Masanda</a> ile güçlendirildi
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Smooth scroll + active nav pill
const pills = document.querySelectorAll('.cat-pill');
pills.forEach(p => p.addEventListener('click', e => {
    e.preventDefault();
    const target = document.querySelector(p.getAttribute('href'));
    if (target) target.scrollIntoView({behavior:'smooth', block:'start'});
}));
</script>
</body>
</html>
