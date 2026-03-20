@extends('layouts.app')

@section('title', __('products.edit_product'))
@section('page-title', __('products.edit_product'))
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-pencil" style="color:#6366f1"></i>{{ __('products.edit_product') }}
            </div>
            <div class="sm-card-body">
                <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">{{ __('products.photo_optional') }}</label>
                        <div class="img-upload-zone" id="imgZone" onclick="document.getElementById('imgInput').click()">
                            <img id="imgPreview" src="{{ $product->image ? asset('uploads/'.$product->image) : '' }}"
                                 class="img-preview {{ $product->image ? '' : 'd-none' }}">
                            <div id="imgPlaceholder" class="text-center {{ $product->image ? 'd-none' : '' }}">
                                <i class="bi bi-cloud-upload fs-2" style="color:#d1d5db"></i>
                                <div class="mt-1 small" style="color:#9ca3af">{{ __('common.click_or_drop') }}</div>
                                <div style="font-size:.72rem;color:#d1d5db">{{ __('common.max_file_size') }}</div>
                            </div>
                        </div>
                        <input type="file" id="imgInput" name="image" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,image/jpeg,image/png,image/gif,image/webp,image/svg+xml" class="input-file-visible-submit">
                        @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <input type="hidden" name="remove_image" id="removeImageField" value="0">
                        <button type="button" class="btn btn-link btn-sm text-danger p-0 mt-1 {{ $product->image ? '' : 'd-none' }}" id="removeBtn" onclick="removeImg()">
                            <i class="bi bi-x-circle me-1"></i>{{ __('common.remove_photo') }}
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('products.category') }} <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">{{ __('products.select_category') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('products.name') }}</label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $product->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('products.description') }} <span class="text-muted">{{ __('common.optional') }}</span></label>
                        <textarea name="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="{{ __('products.description_placeholder') }}">{{ old('description', $product->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <div class="pricing-pair">
                            <div class="pricing-pair-head">
                                <div class="pricing-pair-title">{{ __('products.price_tl') }} & {{ __('products.weight_grams') }}</div>
                                <button type="button" id="toggleWeightBtnEdit" class="btn btn-sm btn-outline-primary">
                                    @if(old('weight_grams', data_get($product, 'weight_grams') ?? data_get($product, 'base_weight_grams')))
                                        <i class="bi bi-x-circle me-1"></i>{{ __('products.remove_weight') }}
                                    @else
                                        <i class="bi bi-plus-circle me-1"></i>{{ __('products.add_weight') }}
                                    @endif
                                </button>
                            </div>
                            <div class="row g-2 align-items-end">
                                <div class="col-12" id="priceColEdit">
                                    <label class="form-label fw-semibold small">{{ __('products.price_tl') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₺</span>
                                        <input type="number" name="price" step="0.01" min="0"
                                               class="form-control @error('price') is-invalid @enderror"
                                               value="{{ old('price', $product->price) }}" required>
                                    </div>
                                    @error('price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 weight-col {{ (old('weight_grams', data_get($product, 'weight_grams') ?? data_get($product, 'base_weight_grams'))) ? '' : 'is-hidden' }}" id="weightColEdit">
                                    <label class="form-label fw-semibold small">{{ __('products.weight_grams') }} <span class="text-muted">{{ __('common.optional') }}</span></label>
                                    <div class="input-group">
                                        <input type="number" name="weight_grams" step="1" min="1" class="form-control @error('weight_grams') is-invalid @enderror" value="{{ old('weight_grams', data_get($product, 'weight_grams') ?? data_get($product, 'base_weight_grams')) }}" placeholder="500">
                                        <span class="input-group-text">g</span>
                                    </div>
                                    @error('weight_grams')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="form-text mt-2">{{ __('products.weight_simple_hint') }}</div>
                            <div id="pairRowsEdit" class="mt-2 d-none">
                                <div class="small fw-semibold text-muted mb-1">{{ __('products.weight_price_options') }}</div>
                                <div id="pairRowsContainerEdit"></div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addPairRowEdit"><i class="bi bi-plus-lg me-1"></i>{{ __('products.add_option_row') }}</button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 form-actions-wrap">
                        <button type="submit" class="btn btn-accent">
                            <i class="bi bi-check-lg me-1"></i>{{ __('products.update') }}
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('imgInput').addEventListener('change', function() {
    if (!this.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('imgPreview').src = e.target.result;
        document.getElementById('imgPreview').classList.remove('d-none');
        document.getElementById('imgPlaceholder').classList.add('d-none');
        document.getElementById('removeBtn').classList.remove('d-none');
        document.getElementById('removeImageField').value = '0';
    };
    reader.readAsDataURL(this.files[0]);
});
const zone = document.getElementById('imgZone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor='#4F46E5'; });
zone.addEventListener('dragleave', () => zone.style.borderColor='');
zone.addEventListener('drop', e => {
    e.preventDefault(); zone.style.borderColor='';
    const dt = new DataTransfer();
    dt.items.add(e.dataTransfer.files[0]);
    document.getElementById('imgInput').files = dt.files;
    document.getElementById('imgInput').dispatchEvent(new Event('change'));
});
function removeImg() {
    document.getElementById('imgInput').value = '';
    document.getElementById('imgPreview').classList.add('d-none');
    document.getElementById('imgPlaceholder').classList.remove('d-none');
    document.getElementById('removeBtn').classList.add('d-none');
    document.getElementById('removeImageField').value = '1';
}

@php
    $existingWeightPriceOptions = old('price_weight_pairs');
    if ($existingWeightPriceOptions === null) {
        $existingWeightPriceOptions = json_decode(data_get($product, 'weight_price_options') ?? '[]', true) ?: [];
    }
@endphp

var toggleWeightBtnEdit = document.getElementById('toggleWeightBtnEdit');
var weightColEdit = document.getElementById('weightColEdit');
var priceColEdit = document.getElementById('priceColEdit');
if (toggleWeightBtnEdit && weightColEdit && priceColEdit) {
    var pairRowsEdit = document.getElementById('pairRowsEdit');
    var pairRowsContainerEdit = document.getElementById('pairRowsContainerEdit');
    var addPairRowEdit = document.getElementById('addPairRowEdit');
    var pairIdxEdit = 0;
    var existingPairsEdit = @json($existingWeightPriceOptions);

    function createPairRowEdit(price, grams) {
        if (!pairRowsContainerEdit) return;
        var row = document.createElement('div');
        row.className = 'pair-row row g-2 align-items-end';
        row.innerHTML =
            '<div class="col-md-5"><label class="form-label fw-semibold small mb-1">{{ __('products.option_price') }}</label><div class="input-group"><span class="input-group-text">₺</span><input type="number" step="0.01" min="0" name="price_weight_pairs[' + pairIdxEdit + '][price]" class="form-control" value="' + (price || '') + '" placeholder="0.00"></div></div>' +
            '<div class="col-md-5"><label class="form-label fw-semibold small mb-1">{{ __('products.option_weight') }}</label><div class="input-group"><input type="number" step="1" min="1" name="price_weight_pairs[' + pairIdxEdit + '][weight_grams]" class="form-control" value="' + (grams || '') + '" placeholder="500"><span class="input-group-text">g</span></div></div>' +
            '<div class="col-md-2"><button type="button" class="btn btn-sm btn-outline-danger w-100 remove-pair-row pair-remove" title="{{ __('products.remove_weight_price_option') }}" aria-label="{{ __('products.remove_weight_price_option') }}"><i class="bi bi-trash"></i></button></div>';
        pairIdxEdit++;
        pairRowsContainerEdit.appendChild(row);
        updateSimpleWeightLock();
    }

    function syncEditWeightState() {
        var visible = !weightColEdit.classList.contains('is-hidden');
        priceColEdit.className = visible ? 'col-md-6' : 'col-12';
        toggleWeightBtnEdit.innerHTML = visible
            ? '<i class="bi bi-x-circle me-1"></i>' + @json(__('products.remove_weight'))
            : '<i class="bi bi-plus-circle me-1"></i>' + @json(__('products.add_weight'));
        if (pairRowsEdit) pairRowsEdit.classList.toggle('d-none', !visible);
        updateSimpleWeightLock();
    }

    function updateSimpleWeightLock() {
        var hasPairs = pairRowsContainerEdit && pairRowsContainerEdit.children.length > 0;
        var wInput = weightColEdit.querySelector('input[name="weight_grams"]');
        if (!wInput) return;
        if (hasPairs) {
            var firstPairWeight = pairRowsContainerEdit.querySelector('input[name*="[weight_grams]"]');
            if (firstPairWeight && firstPairWeight.value) wInput.value = firstPairWeight.value;
            wInput.readOnly = true;
            wInput.style.background = '#f3f4f6';
            wInput.title = @json(__('products.weight_controlled_by_pairs') ?? 'Gramaj, aşağıdaki seçeneklerden yönetilmektedir.');
        } else {
            wInput.readOnly = false;
            wInput.style.background = '';
            wInput.title = '';
        }
    }
    toggleWeightBtnEdit.addEventListener('click', function() {
        weightColEdit.classList.toggle('is-hidden');
        if (weightColEdit.classList.contains('is-hidden')) {
            var wInput = weightColEdit.querySelector('input[name="weight_grams"]');
            if (wInput) wInput.value = '';
            if (pairRowsContainerEdit) pairRowsContainerEdit.innerHTML = '';
        }
        syncEditWeightState();
    });
    if (addPairRowEdit) {
        addPairRowEdit.addEventListener('click', function() { createPairRowEdit('', ''); });
    }
    if (pairRowsContainerEdit) {
        pairRowsContainerEdit.addEventListener('click', function(e) {
            var btn = e.target.closest('.remove-pair-row');
            if (btn) {
                btn.closest('.pair-row')?.remove();
                updateSimpleWeightLock();
            }
        });
        pairRowsContainerEdit.addEventListener('input', function(e) {
            if (e.target.name && e.target.name.includes('[weight_grams]')) {
                var wInput = weightColEdit.querySelector('input[name="weight_grams"]');
                var firstRow = pairRowsContainerEdit.querySelector('input[name*="[weight_grams]"]');
                if (wInput && firstRow && e.target === firstRow) wInput.value = firstRow.value;
            }
        });
    }
    if (Array.isArray(existingPairsEdit) && existingPairsEdit.length) {
        existingPairsEdit.forEach(function(row) {
            createPairRowEdit(row && row.price ? row.price : '', row && row.weight_grams ? row.weight_grams : '');
        });
    }
    syncEditWeightState();
}

</script>
@endpush

<style>
.img-upload-zone {
    border:2px dashed #e5e7eb; border-radius:12px; padding:1.5rem;
    text-align:center; cursor:pointer; transition:border-color .15s,background .15s;
    min-height:120px; display:flex; align-items:center; justify-content:center;
}
.img-upload-zone:hover { border-color:#4F46E5; background:#fff8f5; }
.img-preview { max-width:100%; max-height:180px; border-radius:8px; object-fit:contain; }
.pricing-pair { border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fcfcff; box-shadow: inset 0 1px 0 rgba(255,255,255,.75); }
.pricing-pair-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:.6rem; }
.pricing-pair-title { font-size:.78rem; font-weight:700; color:#475569; letter-spacing:.01em; }
.weight-col.is-hidden { display:none; }
.pair-row { border:1px solid #e5e7eb; border-radius:10px; padding:.65rem; margin-top:.55rem; background:#fff; }
.pair-remove { white-space: nowrap; }
</style>
@endsection
