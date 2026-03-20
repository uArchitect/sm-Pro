{{-- Tabs layout: horizontal category tabs, single category visible at a time --}}
<style>
    .tabs-nav { display:flex; overflow-x:auto; gap:.35rem; padding:.5rem .75rem; background:var(--card); border-bottom:1px solid var(--border); -webkit-overflow-scrolling:touch; scrollbar-width:none; }
    .tabs-nav::-webkit-scrollbar { display:none; }
    .tabs-nav .tab-btn {
        flex-shrink:0; border:none; background:transparent; padding:.5rem 1rem; border-radius:var(--radius-pill);
        font-size:.78rem; font-weight:600; color:var(--text2); cursor:pointer; white-space:nowrap;
        transition:all .2s; display:flex; align-items:center; gap:.4rem;
    }
    .tabs-nav .tab-btn.active { background:var(--accent); color:#fff; box-shadow:0 2px 8px rgba(0,0,0,.12); }
    .tabs-nav .tab-btn img { width:22px; height:22px; border-radius:6px; object-fit:cover; }
    .tabs-nav .tab-btn .tab-icon { width:22px; height:22px; border-radius:6px; background:var(--accent-soft); color:var(--accent); display:inline-flex; align-items:center; justify-content:center; font-size:.6rem; }
    .tabs-nav .tab-btn.active .tab-icon { background:rgba(255,255,255,.2); color:#fff; }

    .tab-panel { display:none; padding:.75rem; }
    .tab-panel.active { display:block; }
    .tab-panel .tab-cat-title {
        font-size:.82rem; font-weight:700; color:var(--text); margin-bottom:.6rem; padding-left:.25rem;
        display:flex; align-items:center; gap:.5rem;
    }
    .tab-panel .tab-cat-title .badge { font-size:.65rem; padding:.2rem .5rem; background:var(--accent-soft); color:var(--accent); border-radius:var(--radius-pill); font-weight:700; }
</style>

<div class="tabs-nav" id="menuTabs">
    @foreach($categories as $cat)
    <button type="button" class="tab-btn {{ $loop->first ? 'active' : '' }}" data-tab="{{ $cat->id }}" data-cat-id="{{ $cat->id }}">
        @if($cat->image)
            <img src="{{ asset('uploads/'.$cat->image) }}" alt="{{ $cat->name }}" loading="lazy">
        @else
            <span class="tab-icon"><i class="bi bi-grid-3x3-gap-fill"></i></span>
        @endif
        {{ $cat->name }}
    </button>
    @endforeach
</div>

@foreach($categories as $cat)
    @php
        $catProducts = $products->get($cat->id, collect());
        $subs = $subCategories[$cat->id] ?? collect();
    @endphp
    <div class="tab-panel {{ $loop->first ? 'active' : '' }}" id="tabPanel-{{ $cat->id }}">
        @if($catProducts->isEmpty() && $subs->isEmpty())
            <p class="cat-empty-msg"><i class="bi bi-inbox me-2"></i>{{ $locale === 'tr' ? 'Henüz ürün eklenmemiş.' : 'No products added yet.' }}</p>
        @else
            @foreach($catProducts as $product)
            <a href="{{ route('public.product', ['tenantId' => $tenant->id, 'productId' => $product->id, 'lang' => app()->getLocale()]) }}" class="prod {{ !($product->is_available ?? true) ? 'prod-sold-out' : '' }}" data-cat-id="{{ $cat->id }}" data-name="{{ strtolower($product->name) }}" data-desc="{{ strtolower($product->description ?? '') }}">
                <div style="position:relative;flex-shrink:0;">
                    @if($product->image)
                        <img src="{{ asset('uploads/'.$product->image) }}" alt="{{ $product->name }}" class="prod-img" loading="lazy">
                    @else
                        <div class="prod-img-empty"><i class="bi bi-box-seam"></i></div>
                    @endif
                    @if(!($product->is_available ?? true))
                    <span class="sold-out-badge">{{ $locale === 'tr' ? 'Tükendi' : 'Sold Out' }}</span>
                    @endif
                </div>
                <div class="prod-body">
                    <div class="prod-name">{{ $product->name }}</div>
                    @if($product->description)
                    <div class="prod-desc">{{ $product->description }}</div>
                    @endif
                </div>
                <div class="prod-price">
                    {{ number_format($product->price, 2, ',', '.') }} ₺
                @if(!empty(data_get($product, 'weight_grams')))
                    <div style="font-size:.66rem;font-weight:600;opacity:.75">{{ number_format((float) data_get($product, 'weight_grams'), 0, ',', '.') }} g</div>
                    @endif
                </div>
            </a>
            @endforeach
            @foreach($subs as $sub)
                @php $subProducts = $products->get($sub->id, collect()); @endphp
                @if($subProducts->isNotEmpty())
                <div class="sub-header">
                    @if($sub->image)
                        <img src="{{ asset('uploads/'.$sub->image) }}" alt="{{ $sub->name }}" loading="lazy">
                    @else
                        <span class="sub-header-icon"><i class="bi bi-dash-lg"></i></span>
                    @endif
                    {{ $sub->name }}
                </div>
                @foreach($subProducts as $product)
                <a href="{{ route('public.product', ['tenantId' => $tenant->id, 'productId' => $product->id, 'lang' => app()->getLocale()]) }}" class="prod {{ !($product->is_available ?? true) ? 'prod-sold-out' : '' }}" data-cat-id="{{ $sub->id }}" data-parent-cat="{{ $cat->id }}" data-name="{{ strtolower($product->name) }}" data-desc="{{ strtolower($product->description ?? '') }}">
                    <div style="position:relative;flex-shrink:0;">
                        @if($product->image)
                            <img src="{{ asset('uploads/'.$product->image) }}" alt="{{ $product->name }}" class="prod-img" loading="lazy">
                        @else
                            <div class="prod-img-empty"><i class="bi bi-box-seam"></i></div>
                        @endif
                        @if(!($product->is_available ?? true))
                        <span class="sold-out-badge">{{ $locale === 'tr' ? 'Tükendi' : 'Sold Out' }}</span>
                        @endif
                    </div>
                    <div class="prod-body">
                        <div class="prod-name">{{ $product->name }}</div>
                        @if($product->description)
                        <div class="prod-desc">{{ $product->description }}</div>
                        @endif
                    </div>
                    <div class="prod-price">
                        {{ number_format($product->price, 2, ',', '.') }} ₺
                    @if(!empty(data_get($product, 'weight_grams')))
                        <div style="font-size:.66rem;font-weight:600;opacity:.75">{{ number_format((float) data_get($product, 'weight_grams'), 0, ',', '.') }} g</div>
                        @endif
                    </div>
                </a>
                @endforeach
                @endif
            @endforeach
        @endif
    </div>
@endforeach

<div class="no-results d-none" id="noResults">
    <i class="bi bi-search"></i>
    <div class="fw-bold">{{ $locale === 'tr' ? 'Sonuç bulunamadı' : 'No results found' }}</div>
    <div class="small mt-1">{{ $locale === 'tr' ? 'Farklı bir arama deneyin' : 'Try a different search' }}</div>
</div>

<script>
(function(){
    var tabs = document.querySelectorAll('#menuTabs .tab-btn');
    var panels = document.querySelectorAll('.tab-panel');
    tabs.forEach(function(btn){
        btn.addEventListener('click', function(){
            var tabId = this.getAttribute('data-tab');
            tabs.forEach(function(t){ t.classList.remove('active'); });
            panels.forEach(function(p){ p.classList.remove('active'); });
            this.classList.add('active');
            var target = document.getElementById('tabPanel-' + tabId);
            if(target) target.classList.add('active');
        });
    });
})();
</script>
