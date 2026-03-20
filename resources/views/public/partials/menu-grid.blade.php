{{-- Grid layout: 2-column product card grid with category filter pills --}}
<style>
    .grid-filters { display:flex; overflow-x:auto; gap:.35rem; padding:.5rem .75rem; -webkit-overflow-scrolling:touch; scrollbar-width:none; }
    .grid-filters::-webkit-scrollbar { display:none; }
    .grid-filter-btn {
        flex-shrink:0; border:1px solid var(--border); background:var(--card); padding:.4rem .85rem; border-radius:var(--radius-pill);
        font-size:.75rem; font-weight:600; color:var(--text2); cursor:pointer; white-space:nowrap; transition:all .2s;
    }
    .grid-filter-btn.active { background:var(--accent); color:#fff; border-color:var(--accent); }
    .grid-filter-btn img { width:18px; height:18px; border-radius:5px; object-fit:cover; margin-right:.3rem; vertical-align:middle; }

    .product-grid { display:grid; grid-template-columns:1fr 1fr; gap:.6rem; padding:.75rem; }
    .grid-card {
        background:var(--card); border:1px solid var(--border-light); border-radius:var(--radius);
        overflow:hidden; box-shadow:var(--shadow); transition:transform .2s, box-shadow .2s;
        text-decoration:none; color:inherit; display:flex; flex-direction:column;
    }
    .grid-card:hover { transform:translateY(-2px); box-shadow:var(--shadow-md); }
    .grid-card-img { width:100%; aspect-ratio:4/3; object-fit:cover; }
    .grid-card-placeholder {
        width:100%; aspect-ratio:4/3; background:linear-gradient(145deg,#f1f5f9,#e2e8f0);
        display:flex; align-items:center; justify-content:center; color:var(--text3); font-size:1.8rem;
    }
    .grid-card-body { padding:.65rem .7rem; flex:1; display:flex; flex-direction:column; }
    .grid-card-name { font-size:.8rem; font-weight:700; color:var(--text); line-height:1.3; margin-bottom:.2rem; }
    .grid-card-desc {
        font-size:.7rem; color:var(--text3); line-height:1.4; margin-bottom:.35rem;
        display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; flex:1;
    }
    .grid-card-price { font-size:.88rem; font-weight:800; color:var(--accent); }

    .grid-cat-label {
        grid-column:1/-1; font-size:.78rem; font-weight:700; color:var(--text);
        padding:.5rem .1rem .2rem; display:flex; align-items:center; gap:.4rem;
        border-bottom:1px solid var(--border-light); margin-bottom:.1rem;
    }
    .grid-cat-label img { width:22px; height:22px; border-radius:6px; object-fit:cover; }
    .grid-cat-label .gci { width:22px; height:22px; border-radius:6px; background:var(--accent-soft); color:var(--accent); display:inline-flex; align-items:center; justify-content:center; font-size:.6rem; }
</style>

<div class="grid-filters" id="gridFilters">
    <button type="button" class="grid-filter-btn active" data-filter-cat="" data-cat-id="">{{ __('public.all') }}</button>
    @foreach($categories as $cat)
    <button type="button" class="grid-filter-btn" data-filter-cat="{{ $cat->id }}" data-cat-id="{{ $cat->id }}">
        @if($cat->image)<img src="{{ asset('uploads/'.$cat->image) }}" alt="" loading="lazy">@endif{{ $cat->name }}
    </button>
    @endforeach
</div>

<div class="product-grid" id="productGrid">
@foreach($categories as $cat)
    @php
        $catProducts = $products->get($cat->id, collect());
        $subs = $subCategories[$cat->id] ?? collect();
    @endphp
    <div class="grid-cat-label" data-grid-cat="{{ $cat->id }}">
        @if($cat->image)
            <img src="{{ asset('uploads/'.$cat->image) }}" alt="{{ $cat->name }}" loading="lazy">
        @else
            <span class="gci"><i class="bi bi-grid-3x3-gap-fill"></i></span>
        @endif
        {{ $cat->name }}
    </div>
    @foreach($catProducts as $product)
    <a href="{{ route('public.product', ['tenantId' => $tenant->id, 'productId' => $product->id, 'lang' => app()->getLocale()]) }}" class="grid-card {{ !($product->is_available ?? true) ? 'prod-sold-out' : '' }}" data-grid-cat="{{ $cat->id }}" data-name="{{ strtolower($product->name) }}" data-desc="{{ strtolower($product->description ?? '') }}">
        <div style="position:relative;">
            @if($product->image)
                <img src="{{ asset('uploads/'.$product->image) }}" alt="{{ $product->name }}" class="grid-card-img" loading="lazy">
            @else
                <div class="grid-card-placeholder"><i class="bi bi-box-seam"></i></div>
            @endif
            @if(!($product->is_available ?? true))
            <span class="sold-out-badge">{{ $locale === 'tr' ? 'Tükendi' : 'Sold Out' }}</span>
            @endif
        </div>
        <div class="grid-card-body">
            <div class="grid-card-name">{{ $product->name }}</div>
            @if($product->description)
            <div class="grid-card-desc">{{ $product->description }}</div>
            @endif
            <div class="grid-card-price">
                {{ number_format($product->price, 2, ',', '.') }} ₺
                @if(!empty(data_get($product, 'weight_grams')))
                    <div style="font-size:.66rem;font-weight:600;opacity:.75">{{ number_format((float) data_get($product, 'weight_grams'), 0, ',', '.') }} g</div>
                @elseif(!empty(data_get($product, 'base_weight_grams')))
                    <div style="font-size:.66rem;font-weight:600;opacity:.75">{{ number_format((float) data_get($product, 'base_weight_grams'), 0, ',', '.') }} g</div>
                @endif
            </div>
        </div>
    </a>
    @endforeach
    @foreach($subs as $sub)
        @php $subProducts = $products->get($sub->id, collect()); @endphp
        @if($subProducts->isNotEmpty())
        <div class="grid-cat-label" data-grid-cat="{{ $cat->id }}" style="font-size:.72rem;color:var(--text2);border-bottom:none;padding-top:.25rem;">
            <i class="bi bi-dash" style="color:var(--text3)"></i> {{ $sub->name }}
        </div>
        @foreach($subProducts as $product)
        <a href="{{ route('public.product', ['tenantId' => $tenant->id, 'productId' => $product->id, 'lang' => app()->getLocale()]) }}" class="grid-card {{ !($product->is_available ?? true) ? 'prod-sold-out' : '' }}" data-grid-cat="{{ $cat->id }}" data-name="{{ strtolower($product->name) }}" data-desc="{{ strtolower($product->description ?? '') }}">
            <div style="position:relative;">
                @if($product->image)
                    <img src="{{ asset('uploads/'.$product->image) }}" alt="{{ $product->name }}" class="grid-card-img" loading="lazy">
                @else
                    <div class="grid-card-placeholder"><i class="bi bi-box-seam"></i></div>
                @endif
                @if(!($product->is_available ?? true))
                <span class="sold-out-badge">{{ $locale === 'tr' ? 'Tükendi' : 'Sold Out' }}</span>
                @endif
            </div>
            <div class="grid-card-body">
                <div class="grid-card-name">{{ $product->name }}</div>
                @if($product->description)
                <div class="grid-card-desc">{{ $product->description }}</div>
                @endif
                <div class="grid-card-price">
                    {{ number_format($product->price, 2, ',', '.') }} ₺
                    @if(!empty(data_get($product, 'weight_grams')))
                        <div style="font-size:.66rem;font-weight:600;opacity:.75">{{ number_format((float) data_get($product, 'weight_grams'), 0, ',', '.') }} g</div>
                    @elseif(!empty(data_get($product, 'base_weight_grams')))
                        <div style="font-size:.66rem;font-weight:600;opacity:.75">{{ number_format((float) data_get($product, 'base_weight_grams'), 0, ',', '.') }} g</div>
                    @endif
                </div>
            </div>
        </a>
        @endforeach
        @endif
    @endforeach
@endforeach
</div>

<div class="no-results d-none" id="noResults">
    <i class="bi bi-search"></i>
    <div class="fw-bold">{{ $locale === 'tr' ? 'Sonuç bulunamadı' : 'No results found' }}</div>
    <div class="small mt-1">{{ $locale === 'tr' ? 'Farklı bir arama deneyin' : 'Try a different search' }}</div>
</div>

<script>
(function(){
    var btns = document.querySelectorAll('#gridFilters .grid-filter-btn');
    var items = document.querySelectorAll('#productGrid [data-grid-cat]');
    btns.forEach(function(btn){
        btn.addEventListener('click', function(){
            var catId = this.getAttribute('data-filter-cat');
            btns.forEach(function(b){ b.classList.remove('active'); });
            this.classList.add('active');
            items.forEach(function(el){
                if(!catId || el.getAttribute('data-grid-cat') === catId) {
                    el.style.display = '';
                } else {
                    el.style.display = 'none';
                }
            });
            document.querySelectorAll('.cat-pill, .drawer-item').forEach(function(b){
                b.classList.toggle('active', (b.getAttribute('data-cat-id') || '') === (catId || ''));
            });
        });
    });
})();
</script>
