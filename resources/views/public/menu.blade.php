<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tenant->restoran_adi }} — Dijital Menü</title>
    <meta name="description" content="{{ $tenant->restoran_adi }} dijital menüsü.">
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        *{font-family:'Inter',sans-serif;box-sizing:border-box;margin:0;padding:0}
        body{background:#f5f5f7;color:#1d1d1f;-webkit-font-smoothing:antialiased}

        /* Header */
        .mh{background:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);padding:2rem 1.25rem 1.75rem;text-align:center;position:relative;overflow:hidden}
        .mh::before{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(circle at 30% 50%,rgba(255,107,53,.08) 0%,transparent 50%);pointer-events:none}
        .mh-logo{width:68px;height:68px;border-radius:18px;object-fit:cover;border:3px solid rgba(255,255,255,.15);margin:0 auto .75rem;display:block;box-shadow:0 8px 24px rgba(0,0,0,.25)}
        .mh-logo-default{width:68px;height:68px;border-radius:18px;background:linear-gradient(135deg,#FF6B35,#FF8C42);display:flex;align-items:center;justify-content:center;font-size:1.6rem;color:#fff;margin:0 auto .75rem;box-shadow:0 8px 24px rgba(255,107,53,.3)}
        .mh h1{font-size:1.35rem;font-weight:800;color:#fff;letter-spacing:-.02em;margin-bottom:.2rem;position:relative}
        .mh-sub{font-size:.78rem;color:rgba(255,255,255,.45);position:relative}
        .mh-info{display:flex;flex-wrap:wrap;justify-content:center;gap:.5rem .8rem;margin-top:.65rem;position:relative}
        .mh-info-item{display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;color:rgba(255,255,255,.5)}
        .mh-info-item i{font-size:.75rem;color:rgba(255,255,255,.35)}
        .mh-social{display:flex;justify-content:center;gap:.45rem;margin-top:.75rem;position:relative}
        .mh-social a{width:32px;height:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:.85rem;text-decoration:none;transition:transform .15s,opacity .15s}
        .mh-social a:hover{transform:scale(1.1);opacity:.85}
        .s-ig{background:linear-gradient(135deg,#833AB4,#FD1D1D,#F77737);color:#fff}
        .s-fb{background:#1877F2;color:#fff}
        .s-tw{background:#000;color:#fff}
        .s-wa{background:#25D366;color:#fff}
        .order-badge{display:inline-flex;align-items:center;gap:.3rem;background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.25);color:#4ade80;font-size:.68rem;font-weight:700;padding:.2rem .55rem;border-radius:999px;margin-top:.6rem;position:relative}
        .order-badge .dot{width:5px;height:5px;border-radius:50%;background:#4ade80;animation:pulse 2s infinite}
        @keyframes pulse{0%,100%{opacity:1}50%{opacity:.35}}

        /* Sticky Nav */
        .sn{position:sticky;top:0;z-index:50;background:rgba(245,245,247,.92);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border-bottom:1px solid rgba(0,0,0,.06);padding:.5rem 1rem}
        .sn-inner{display:flex;gap:.4rem;overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none;padding-bottom:2px}
        .sn-inner::-webkit-scrollbar{display:none}
        .sn-pill{display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .75rem;border-radius:999px;font-size:.75rem;font-weight:600;white-space:nowrap;background:#fff;color:#6b7280;border:1px solid #e5e7eb;text-decoration:none;transition:all .15s;flex-shrink:0}
        .sn-pill:hover,.sn-pill.active{background:rgba(255,107,53,.08);color:#FF6B35;border-color:rgba(255,107,53,.2)}
        .sn-pill img{width:18px;height:18px;border-radius:5px;object-fit:cover}

        /* Content */
        .mc{max-width:640px;margin:0 auto;padding:1rem 1rem 0}

        /* Category Block */
        .cat-block{margin-bottom:1.5rem}
        .cat-head{display:flex;align-items:center;gap:.6rem;margin-bottom:.65rem;padding:.1rem 0}
        .cat-img{width:40px;height:40px;border-radius:10px;object-fit:cover;flex-shrink:0;border:1px solid rgba(0,0,0,.06)}
        .cat-icon{width:40px;height:40px;border-radius:10px;background:rgba(255,107,53,.06);display:flex;align-items:center;justify-content:center;color:#FF6B35;font-size:1rem;flex-shrink:0}
        .cat-name{font-size:.92rem;font-weight:800;color:#1d1d1f;letter-spacing:-.01em}
        .cat-line{flex:1;height:1px;background:#e5e7eb}

        /* Sub-category */
        .sub-label{font-size:.78rem;font-weight:700;color:#6b7280;margin:.6rem 0 .4rem .5rem;display:flex;align-items:center;gap:.35rem}
        .sub-label::before{content:'';width:12px;height:1px;background:#d1d5db}

        /* Product Card */
        .pc{background:#fff;border:1px solid #eaecf0;border-radius:14px;padding:.75rem .85rem;display:flex;align-items:center;gap:.75rem;margin-bottom:.45rem;transition:box-shadow .15s,transform .15s}
        .pc:hover{box-shadow:0 4px 16px rgba(0,0,0,.06);transform:translateY(-1px)}
        .pc-img{width:54px;height:54px;border-radius:10px;object-fit:cover;flex-shrink:0}
        .pc-img-empty{width:54px;height:54px;border-radius:10px;background:#f9fafb;border:1px solid #f0f0f2;display:flex;align-items:center;justify-content:center;color:#d1d5db;font-size:1.1rem;flex-shrink:0}
        .pc-body{flex:1;min-width:0}
        .pc-name{font-size:.82rem;font-weight:700;color:#1d1d1f;line-height:1.3}
        .pc-desc{font-size:.72rem;color:#8e8e93;margin-top:.15rem;line-height:1.45;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
        .pc-price{font-size:.92rem;font-weight:800;color:#FF6B35;white-space:nowrap;flex-shrink:0}

        /* Empty State */
        .empty-state{text-align:center;padding:4rem 1rem;color:#98a2b3}
        .empty-state i{font-size:2.5rem;margin-bottom:.75rem;display:block;opacity:.4}

        /* Reviews */
        .rv{background:#fff;border-top:4px solid #f5f5f7;padding:1.5rem 0}
        .rv-inner{max-width:640px;margin:0 auto;padding:0 1rem}
        .rv-title{font-size:.92rem;font-weight:800;color:#1d1d1f;display:flex;align-items:center;gap:.4rem;margin-bottom:1rem}
        .rv-avg{text-align:center;margin-bottom:1.25rem;padding:1rem;background:#fafafa;border-radius:14px;border:1px solid #f0f0f2}
        .rv-avg-num{font-size:2.5rem;font-weight:900;color:#1d1d1f;line-height:1}
        .rv-avg-stars{color:#FBBF24;font-size:1.15rem;margin:.25rem 0}
        .rv-avg-count{font-size:.75rem;color:#8e8e93}
        .rv-card{background:#fafafa;border:1px solid #f0f0f2;border-radius:12px;padding:.85rem 1rem;margin-bottom:.4rem}
        .rv-card-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:.3rem}
        .rv-card-name{font-weight:700;font-size:.8rem;color:#1d1d1f}
        .rv-card-date{font-size:.68rem;color:#98a2b3}
        .rv-card-stars{color:#FBBF24;font-size:.75rem;margin-bottom:.25rem}
        .rv-card-text{font-size:.78rem;color:#4b5563;line-height:1.5}

        /* Review Form */
        .rv-form{background:linear-gradient(135deg,#1a1a2e,#16213e);border-radius:16px;padding:1.35rem;margin-top:1rem}
        .rv-form h6{color:#fff;font-weight:700;font-size:.88rem;margin-bottom:1rem}
        .rv-form .fl{color:rgba(255,255,255,.6);font-size:.75rem;font-weight:600;margin-bottom:.3rem;display:block}
        .rv-form input,.rv-form textarea{width:100%;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);color:#fff;border-radius:10px;padding:.5rem .75rem;font-size:.82rem;font-family:'Inter',sans-serif;margin-bottom:.75rem;outline:none;transition:border-color .15s}
        .rv-form input::placeholder,.rv-form textarea::placeholder{color:rgba(255,255,255,.25)}
        .rv-form input:focus,.rv-form textarea:focus{border-color:#FF6B35;box-shadow:0 0 0 3px rgba(255,107,53,.12)}
        .rv-form textarea{resize:vertical;min-height:70px}
        .rv-form-btn{width:100%;padding:.6rem;border:none;border-radius:10px;background:linear-gradient(135deg,#FF6B35,#FF8C42);color:#fff;font-weight:700;font-size:.85rem;cursor:pointer;transition:transform .15s,box-shadow .15s}
        .rv-form-btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(255,107,53,.3)}
        .star-input{display:flex;gap:.15rem;direction:rtl;justify-content:flex-end;margin-bottom:.75rem}
        .star-input input{display:none!important;width:auto;margin:0;padding:0;background:none;border:none}
        .star-input label{font-size:1.5rem;color:rgba(255,255,255,.18);cursor:pointer;transition:color .12s;margin:0;padding:0}
        .star-input label:hover,.star-input label:hover~label,.star-input input:checked~label{color:#FBBF24}
        .rv-alert{border-radius:10px;font-size:.8rem;font-weight:500;padding:.55rem .85rem;margin-bottom:.75rem}
        .rv-alert-ok{background:rgba(34,197,94,.12);color:#16a34a;border:1px solid rgba(34,197,94,.2)}
        .rv-alert-warn{background:rgba(251,191,36,.12);color:#b45309;border:1px solid rgba(251,191,36,.2)}

        /* Footer */
        .ft{background:#1a1a2e;text-align:center;padding:1rem;font-size:.72rem;color:rgba(255,255,255,.3)}
        .ft a{color:#FF8C42;text-decoration:none;font-weight:600}
    </style>
</head>
<body>

<!-- Header -->
<header class="mh">
    @if($tenant->logo)
        <img src="{{ asset('storage/'.$tenant->logo) }}" alt="{{ $tenant->restoran_adi }}" class="mh-logo">
    @else
        <div class="mh-logo-default"><i class="bi bi-shop"></i></div>
    @endif

    <h1>{{ $tenant->restoran_adi }}</h1>
    <div class="mh-sub">Dijital Menü</div>

    @if(($tenant->restoran_adresi ?? null) || ($tenant->restoran_telefonu ?? null))
    <div class="mh-info">
        @if($tenant->restoran_adresi)
        <span class="mh-info-item"><i class="bi bi-geo-alt-fill"></i> {{ $tenant->restoran_adresi }}</span>
        @endif
        @if($tenant->restoran_telefonu)
        <a href="tel:{{ $tenant->restoran_telefonu }}" class="mh-info-item" style="text-decoration:none">
            <i class="bi bi-telephone-fill"></i> {{ $tenant->restoran_telefonu }}
        </a>
        @endif
    </div>
    @endif

    @if(($tenant->instagram ?? null) || ($tenant->facebook ?? null) || ($tenant->twitter ?? null) || ($tenant->whatsapp ?? null))
    <div class="mh-social">
        @if($tenant->instagram)
        <a href="https://instagram.com/{{ $tenant->instagram }}" target="_blank" rel="noopener" class="s-ig" title="Instagram"><i class="bi bi-instagram"></i></a>
        @endif
        @if($tenant->facebook)
        <a href="https://facebook.com/{{ $tenant->facebook }}" target="_blank" rel="noopener" class="s-fb" title="Facebook"><i class="bi bi-facebook"></i></a>
        @endif
        @if($tenant->twitter)
        <a href="https://x.com/{{ $tenant->twitter }}" target="_blank" rel="noopener" class="s-tw" title="X"><i class="bi bi-twitter-x"></i></a>
        @endif
        @if($tenant->whatsapp)
        <a href="https://wa.me/90{{ preg_replace('/\D/', '', $tenant->whatsapp) }}" target="_blank" rel="noopener" class="s-wa" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
        @endif
    </div>
    @endif

    @if(!empty($tenant->ordering_enabled))
    <div class="order-badge"><span class="dot"></span> Sipariş Açık</div>
    @endif
</header>

<!-- Sticky Category Nav -->
@if($categories->isNotEmpty())
<nav class="sn">
    <div class="sn-inner">
        @foreach($categories as $cat)
        <a href="#cat-{{ $cat->id }}" class="sn-pill" data-cat="{{ $cat->id }}">
            @if($cat->image)
            <img src="{{ asset('storage/'.$cat->image) }}" alt="">
            @endif
            {{ $cat->name }}
        </a>
        @endforeach
        @if($reviewStats->total > 0)
        <a href="#reviews" class="sn-pill"><i class="bi bi-star-fill" style="font-size:.6rem;color:#FBBF24"></i> Değerlendirmeler</a>
        @endif
    </div>
</nav>
@endif

<!-- Menu Content -->
<main class="mc">
    @if($categories->isEmpty())
        <div class="empty-state">
            <i class="bi bi-journal-text"></i>
            <div style="font-weight:600">Menü henüz hazırlanmadı.</div>
        </div>
    @else
        @foreach($categories as $cat)
            @php
                $catProducts = $products->get($cat->id, collect());
                $subs = ($subCategories[$cat->id] ?? collect());
                $hasContent = $catProducts->isNotEmpty() || $subs->isNotEmpty();
            @endphp
            @if($hasContent)
            <section class="cat-block" id="cat-{{ $cat->id }}">
                <div class="cat-head">
                    @if($cat->image)
                        <img src="{{ asset('storage/'.$cat->image) }}" alt="{{ $cat->name }}" class="cat-img">
                    @else
                        <div class="cat-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
                    @endif
                    <span class="cat-name">{{ $cat->name }}</span>
                    <span class="cat-line"></span>
                </div>

                @foreach($catProducts as $product)
                <div class="pc">
                    @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="pc-img">
                    @else
                        <div class="pc-img-empty"><i class="bi bi-box-seam"></i></div>
                    @endif
                    <div class="pc-body">
                        <div class="pc-name">{{ $product->name }}</div>
                        @if($product->description)
                        <div class="pc-desc">{{ $product->description }}</div>
                        @endif
                    </div>
                    <div class="pc-price">{{ number_format($product->price, 2, ',', '.') }} ₺</div>
                </div>
                @endforeach

                @foreach($subs as $sub)
                    @php $subProducts = $products->get($sub->id, collect()); @endphp
                    @if($subProducts->isNotEmpty())
                    <div class="sub-label">
                        @if($sub->image)
                            <img src="{{ asset('storage/'.$sub->image) }}" alt="" style="width:20px;height:20px;border-radius:5px;object-fit:cover">
                        @endif
                        {{ $sub->name }}
                    </div>
                    @foreach($subProducts as $product)
                    <div class="pc">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="pc-img">
                        @else
                            <div class="pc-img-empty"><i class="bi bi-box-seam"></i></div>
                        @endif
                        <div class="pc-body">
                            <div class="pc-name">{{ $product->name }}</div>
                            @if($product->description)
                            <div class="pc-desc">{{ $product->description }}</div>
                            @endif
                        </div>
                        <div class="pc-price">{{ number_format($product->price, 2, ',', '.') }} ₺</div>
                    </div>
                    @endforeach
                    @endif
                @endforeach
            </section>
            @endif
        @endforeach
    @endif
</main>

<!-- Reviews Section -->
<section class="rv" id="reviews">
    <div class="rv-inner">
        <div class="rv-title">
            <i class="bi bi-star-fill" style="color:#FBBF24;font-size:.8rem"></i> Değerlendirmeler
        </div>

        @if($reviewStats->total > 0)
        <div class="rv-avg">
            <div class="rv-avg-num">{{ number_format($reviewStats->avg_rating, 1) }}</div>
            <div class="rv-avg-stars">
                @for($i = 1; $i <= 5; $i++)
                <i class="bi bi-star{{ $i <= round($reviewStats->avg_rating) ? '-fill' : '' }}"></i>
                @endfor
            </div>
            <div class="rv-avg-count">{{ $reviewStats->total }} değerlendirme</div>
        </div>
        @endif

        @foreach($reviews as $review)
        <div class="rv-card">
            <div class="rv-card-top">
                <div class="rv-card-name"><i class="bi bi-person-circle" style="color:#98a2b3;margin-right:.25rem"></i>{{ $review->customer_name ?: 'Anonim' }}</div>
                <div class="rv-card-date">{{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}</div>
            </div>
            <div class="rv-card-stars">
                @for($i = 1; $i <= 5; $i++)
                <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                @endfor
            </div>
            @if($review->comment)
            <div class="rv-card-text">{{ $review->comment }}</div>
            @endif
        </div>
        @endforeach

        <!-- Review Form -->
        <div class="rv-form">
            <h6><i class="bi bi-chat-heart" style="color:#FF6B35;margin-right:.35rem"></i> Deneyiminizi paylaşın</h6>

            @if(session('review_success'))
            <div class="rv-alert rv-alert-ok"><i class="bi bi-check-circle" style="margin-right:.25rem"></i> Değerlendirmeniz kaydedildi. Teşekkürler!</div>
            @endif
            @if(session('review_error') === 'already_reviewed')
            <div class="rv-alert rv-alert-warn"><i class="bi bi-info-circle" style="margin-right:.25rem"></i> Bugün zaten bir değerlendirme yaptınız.</div>
            @endif

            <form method="POST" action="{{ route('public.review', $tenant->id) }}">
                @csrf
                <label class="fl">Adınız <span style="color:rgba(255,255,255,.25)">(opsiyonel)</span></label>
                <input type="text" name="customer_name" maxlength="100" placeholder="Adınız...">

                <label class="fl">Puanınız</label>
                <div class="star-input">
                    @for($i = 5; $i >= 1; $i--)
                    <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" {{ $i === 5 ? 'checked' : '' }}>
                    <label for="star{{ $i }}"><i class="bi bi-star-fill"></i></label>
                    @endfor
                </div>

                <label class="fl">Yorumunuz <span style="color:rgba(255,255,255,.25)">(opsiyonel)</span></label>
                <textarea name="comment" rows="3" maxlength="1000" placeholder="Deneyiminizi anlatın..."></textarea>

                <button type="submit" class="rv-form-btn"><i class="bi bi-send" style="margin-right:.35rem"></i> Değerlendirmeyi Gönder</button>
            </form>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="ft">
    <i class="bi bi-qr-code" style="margin-right:.25rem"></i>
    <a href="{{ route('home') }}">Sipariş Masanda</a> ile güçlendirildi
</footer>

<script>
/* Smooth scroll for category pills */
document.querySelectorAll('.sn-pill').forEach(p => p.addEventListener('click', e => {
    e.preventDefault();
    const t = document.querySelector(p.getAttribute('href'));
    if (t) {
        const offset = document.querySelector('.sn')?.offsetHeight || 48;
        const y = t.getBoundingClientRect().top + window.pageYOffset - offset - 8;
        window.scrollTo({top: y, behavior: 'smooth'});
    }
}));

/* Active pill on scroll */
const sections = document.querySelectorAll('.cat-block');
const pills = document.querySelectorAll('.sn-pill[data-cat]');
if (sections.length && pills.length) {
    const navH = document.querySelector('.sn')?.offsetHeight || 48;
    let ticking = false;
    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(() => {
                let current = '';
                sections.forEach(s => {
                    if (s.getBoundingClientRect().top <= navH + 60) current = s.id.replace('cat-', '');
                });
                pills.forEach(p => {
                    p.classList.toggle('active', p.dataset.cat === current);
                });
                ticking = false;
            });
            ticking = true;
        }
    });
}

/* Scroll to reviews on submit */
@if(session('review_success') || session('review_error'))
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('reviews');
    if (el) setTimeout(() => el.scrollIntoView({behavior:'smooth'}), 200);
});
@endif
</script>
</body>
</html>
