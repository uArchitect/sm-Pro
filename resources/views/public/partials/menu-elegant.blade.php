{{-- Elegant layout: magazine-style with full-width category banners and horizontal product cards --}}
<style>
    .elegant-nav { display:flex; overflow-x:auto; gap:.3rem; padding:.6rem .75rem; background:var(--card); border-bottom:1px solid var(--border-light); -webkit-overflow-scrolling:touch; scrollbar-width:none; position:sticky; top:0; z-index:50; }
    .elegant-nav::-webkit-scrollbar { display:none; }
    .elegant-nav a {
        flex-shrink:0; text-decoration:none; font-size:.72rem; font-weight:600; color:var(--text3); padding:.35rem .7rem;
        border-radius:var(--radius-pill); transition:all .2s; white-space:nowrap;
    }
    .elegant-nav a:hover, .elegant-nav a.active { color:var(--accent); background:var(--accent-soft); }

    .elegant-section { padding:0 .75rem; margin-bottom:1.5rem; }
    .elegant-banner {
        position:relative; border-radius:var(--radius); overflow:hidden; margin-bottom:.75rem;
        background:linear-gradient(135deg, var(--accent), var(--accent2)); min-height:90px;
        display:flex; align-items:flex-end; padding:.75rem 1rem;
    }
    .elegant-banner-img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:.35; }
    .elegant-banner-content { position:relative; z-index:1; }
    .elegant-banner-title { font-size:1.1rem; font-weight:800; color:#fff; text-shadow:0 1px 6px rgba(0,0,0,.3); }
    .elegant-banner-count { font-size:.7rem; font-weight:600; color:rgba(255,255,255,.8); margin-top:.1rem; }

    .elegant-product {
        display:flex; gap:.75rem; padding:.75rem; margin-bottom:.5rem; background:var(--card);
        border-radius:var(--radius); border:1px solid var(--border-light); box-shadow:var(--shadow);
        text-decoration:none; color:inherit; transition:transform .2s, box-shadow .2s;
    }
    .elegant-product:hover { transform:translateY(-1px); box-shadow:var(--shadow-md); }
    .elegant-product-img { width:80px; height:80px; border-radius:var(--radius-sm); object-fit:cover; flex-shrink:0; }
    .elegant-product-placeholder {
        width:80px; height:80px; border-radius:var(--radius-sm); flex-shrink:0;
        background:linear-gradient(145deg,#f1f5f9,#e2e8f0); display:flex; align-items:center;
        justify-content:center; color:var(--text3); font-size:1.5rem;
    }
    .elegant-product-body { flex:1; min-width:0; display:flex; flex-direction:column; justify-content:center; }
    .elegant-product-name { font-size:.88rem; font-weight:700; color:var(--text); line-height:1.3; }
    .elegant-product-desc {
        font-size:.74rem; color:var(--text3); line-height:1.5; margin-top:.15rem;
        display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
    }
    .elegant-product-price { font-size:.95rem; font-weight:800; color:var(--accent); margin-top:.25rem; }

    .elegant-sub-title {
        font-size:.74rem; font-weight:700; color:var(--text2); padding:.4rem 0 .3rem .2rem;
        display:flex; align-items:center; gap:.35rem;
    }
    .elegant-sub-title::before { content:''; width:14px; height:2px; background:var(--accent); border-radius:1px; }
</style>

{{-- Quick-scroll navigation --}}
<nav class="elegant-nav" id="elegantNav">
    @foreach($categories as $cat)
    <a href="#elegant-{{ $cat->id }}" class="{{ $loop->first ? 'active' : '' }}">{{ $cat->name }}</a>
    @endforeach
</nav>

@foreach($categories as $cat)
    @php
        $catProducts = $products->get($cat->id, collect());
        $subs = $subCategories[$cat->id] ?? collect();
        $totalInCat = $catProducts->count();
        foreach ($subs as $sub) { $totalInCat += ($products->get($sub->id, collect()))->count(); }
    @endphp
    <section class="elegant-section cat-section" id="elegant-{{ $cat->id }}" data-cat-id="{{ $cat->id }}">
        <div class="elegant-banner">
            @if($cat->image)
                <img src="{{ asset('uploads/'.$cat->image) }}" alt="{{ $cat->name }}" class="elegant-banner-img" loading="lazy">
            @endif
            <div class="elegant-banner-content">
                <div class="elegant-banner-title">{{ $cat->name }}</div>
                <div class="elegant-banner-count">{{ __('public.products_count', ['count' => $totalInCat]) }}</div>
            </div>
        </div>

        @if($totalInCat === 0)
            <p class="cat-empty-msg"><i class="bi bi-inbox me-2"></i>{{ $locale === 'tr' ? 'Henüz ürün eklenmemiş.' : 'No products added yet.' }}</p>
        @else
            @foreach($catProducts as $product)
            <a href="{{ route('public.product', ['tenantId' => $tenant->id, 'productId' => $product->id, 'lang' => app()->getLocale()]) }}" class="elegant-product prod {{ !($product->is_available ?? true) ? 'prod-sold-out' : '' }}" data-cat-id="{{ $cat->id }}" data-name="{{ strtolower($product->name) }}" data-desc="{{ strtolower($product->description ?? '') }}">
                <div style="position:relative;flex-shrink:0;">
                    @if($product->image)
                        <img src="{{ asset('uploads/'.$product->image) }}" alt="{{ $product->name }}" class="elegant-product-img" loading="lazy">
                    @else
                        <div class="elegant-product-placeholder"><i class="bi bi-box-seam"></i></div>
                    @endif
                    @if(!($product->is_available ?? true))
                    <span class="sold-out-badge">{{ $locale === 'tr' ? 'Tükendi' : 'Sold Out' }}</span>
                    @endif
                </div>
                <div class="elegant-product-body">
                    <div class="elegant-product-name">{{ $product->name }}</div>
                    @if($product->description)
                    <div class="elegant-product-desc">{{ $product->description }}</div>
                    @endif
                    <div class="elegant-product-price">
                        {{ number_format($product->price, 2, ',', '.') }} ₺
                                    @if(!empty(data_get($product, 'weight_grams')))
                                        <div style="font-size:.66rem;font-weight:600;opacity:.75">{{ number_format((float) data_get($product, 'weight_grams'), 0, ',', '.') }} g</div>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach

            @foreach($subs as $sub)
                @php $subProducts = $products->get($sub->id, collect()); @endphp
                @if($subProducts->isNotEmpty())
                <div class="elegant-sub-title">{{ $sub->name }}</div>
                @foreach($subProducts as $product)
                <a href="{{ route('public.product', ['tenantId' => $tenant->id, 'productId' => $product->id, 'lang' => app()->getLocale()]) }}" class="elegant-product prod {{ !($product->is_available ?? true) ? 'prod-sold-out' : '' }}" data-cat-id="{{ $sub->id }}" data-parent-cat="{{ $cat->id }}" data-name="{{ strtolower($product->name) }}" data-desc="{{ strtolower($product->description ?? '') }}">
                    <div style="position:relative;flex-shrink:0;">
                        @if($product->image)
                            <img src="{{ asset('uploads/'.$product->image) }}" alt="{{ $product->name }}" class="elegant-product-img" loading="lazy">
                        @else
                            <div class="elegant-product-placeholder"><i class="bi bi-box-seam"></i></div>
                        @endif
                        @if(!($product->is_available ?? true))
                        <span class="sold-out-badge">{{ $locale === 'tr' ? 'Tükendi' : 'Sold Out' }}</span>
                        @endif
                    </div>
                    <div class="elegant-product-body">
                        <div class="elegant-product-name">{{ $product->name }}</div>
                        @if($product->description)
                        <div class="elegant-product-desc">{{ $product->description }}</div>
                        @endif
                        <div class="elegant-product-price">
                            {{ number_format($product->price, 2, ',', '.') }} ₺
                            @if(!empty(data_get($product, 'weight_grams')))
                                <div style="font-size:.66rem;font-weight:600;opacity:.75">{{ number_format((float) data_get($product, 'weight_grams'), 0, ',', '.') }} g</div>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
                @endif
            @endforeach
        @endif
    </section>
@endforeach

<div class="no-results d-none" id="noResults">
    <i class="bi bi-search"></i>
    <div class="fw-bold">{{ $locale === 'tr' ? 'Sonuç bulunamadı' : 'No results found' }}</div>
    <div class="small mt-1">{{ $locale === 'tr' ? 'Farklı bir arama deneyin' : 'Try a different search' }}</div>
</div>

<script>
(function(){
    var navLinks = document.querySelectorAll('#elegantNav a');
    var sections = document.querySelectorAll('.elegant-section');

    navLinks.forEach(function(link){
        link.addEventListener('click', function(e){
            e.preventDefault();
            var target = document.querySelector(this.getAttribute('href'));
            if(target) {
                target.scrollIntoView({ behavior:'smooth', block:'start' });
                navLinks.forEach(function(l){ l.classList.remove('active'); });
                this.classList.add('active');
            }
        });
    });

    var observer = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
            if(entry.isIntersecting){
                var id = entry.target.id;
                navLinks.forEach(function(l){
                    l.classList.toggle('active', l.getAttribute('href') === '#' + id);
                });
            }
        });
    }, { rootMargin:'-30% 0px -60% 0px' });

    sections.forEach(function(sec){ observer.observe(sec); });
})();
</script>
