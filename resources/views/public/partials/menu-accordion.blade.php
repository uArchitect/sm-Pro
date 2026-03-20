{{-- Accordion layout: collapsible category sections --}}
<div id="menuAccordion">
@foreach($categories as $cat)
    @php
        $catProducts = $products->get($cat->id, collect());
        $subs = $subCategories[$cat->id] ?? collect();
        $totalInCat = $catProducts->count();
        foreach ($subs as $sub) { $totalInCat += ($products->get($sub->id, collect()))->count(); }
    @endphp
    <section class="cat-section" data-cat-id="{{ $cat->id }}" id="cat-{{ $cat->id }}">
        <button type="button" class="cat-header" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $cat->id }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse-{{ $cat->id }}">
            @if($cat->image)
                <img src="{{ asset('uploads/'.$cat->image) }}" class="cat-header-img" alt="{{ $cat->name }}" loading="lazy">
            @else
                <div class="cat-header-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
            @endif
            <span class="cat-header-name">{{ $cat->name }}</span>
            <span class="cat-header-count cat-count" data-cat-id="{{ $cat->id }}">{{ __('public.products_count', ['count' => $totalInCat]) }}</span>
            <i class="bi bi-chevron-down cat-chevron"></i>
        </button>
        <div class="collapse {{ $loop->first ? 'show' : '' }}" id="collapse-{{ $cat->id }}" data-bs-parent="#menuAccordion">
        <div class="cat-section-body">
        @if($totalInCat === 0)
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
                @elseif(!empty(data_get($product, 'base_weight_grams')))
                    <div style="font-size:.66rem;font-weight:600;opacity:.75">{{ number_format((float) data_get($product, 'base_weight_grams'), 0, ',', '.') }} g</div>
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
                    @elseif(!empty(data_get($product, 'base_weight_grams')))
                        <div style="font-size:.66rem;font-weight:600;opacity:.75">{{ number_format((float) data_get($product, 'base_weight_grams'), 0, ',', '.') }} g</div>
                    @endif
                </div>
            </a>
            @endforeach
            @endif
        @endforeach
        @endif
        </div>
        </div>
    </section>
@endforeach
</div>
<div class="no-results d-none" id="noResults">
    <i class="bi bi-search"></i>
    <div class="fw-bold">{{ $locale === 'tr' ? 'Sonuç bulunamadı' : 'No results found' }}</div>
    <div class="small mt-1">{{ $locale === 'tr' ? 'Farklı bir arama deneyin' : 'Try a different search' }}</div>
</div>
