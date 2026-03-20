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
                        <label class="form-label fw-semibold small">{{ __('products.price_tl') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">₺</span>
                            <input type="number" name="price" step="0.01" min="0"
                                   class="form-control @error('price') is-invalid @enderror"
                                   value="{{ old('price', $product->price) }}" required>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="pricing-rule-card">
                            <div class="rule-toggle-row">
                                <div class="pricing-rule-title mb-0">{{ __('products.weight_pricing_rule') }} <span class="text-muted fw-normal">({{ __('common.optional') }})</span></div>
                                <div class="form-check form-switch m-0">
                                    @php
                                        $hasWeightRule = old('base_weight_grams', data_get($product, 'base_weight_grams'))
                                            || old('extra_weight_step_grams', data_get($product, 'extra_weight_step_grams'))
                                            || old('extra_weight_step_price', data_get($product, 'extra_weight_step_price'));
                                    @endphp
                                    <input class="form-check-input" type="checkbox" role="switch" id="enableWeightRuleEdit" {{ $hasWeightRule ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="enableWeightRuleEdit">{{ __('products.enable_weight_pricing_rule') }}</label>
                                </div>
                            </div>
                            <div class="pricing-rule-grid rule-fields {{ $hasWeightRule ? '' : 'is-hidden' }}" id="weightRuleFieldsEdit">
                                <div>
                                    <label class="form-label fw-semibold small mb-1">{{ __('products.base_weight_grams') }}</label>
                                    <div class="input-group">
                                        <input type="number" name="base_weight_grams" step="1" min="1" class="form-control @error('base_weight_grams') is-invalid @enderror" value="{{ old('base_weight_grams', data_get($product, 'base_weight_grams')) }}" placeholder="500">
                                        <span class="input-group-text">g</span>
                                    </div>
                                    @error('base_weight_grams')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div>
                                    <label class="form-label fw-semibold small mb-1">{{ __('products.extra_weight_step_grams') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+</span>
                                        <input type="number" name="extra_weight_step_grams" step="1" min="1" class="form-control @error('extra_weight_step_grams') is-invalid @enderror" value="{{ old('extra_weight_step_grams', data_get($product, 'extra_weight_step_grams')) }}" placeholder="50">
                                        <span class="input-group-text">g</span>
                                    </div>
                                    @error('extra_weight_step_grams')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div>
                                    <label class="form-label fw-semibold small mb-1">{{ __('products.extra_weight_step_price') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+</span>
                                        <span class="input-group-text">₺</span>
                                        <input type="number" name="extra_weight_step_price" step="0.01" min="0.01" class="form-control @error('extra_weight_step_price') is-invalid @enderror" value="{{ old('extra_weight_step_price', data_get($product, 'extra_weight_step_price')) }}" placeholder="50.00">
                                    </div>
                                    @error('extra_weight_step_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="form-text mt-2">{{ __('products.weight_pricing_rule_hint') }}</div>
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

var enableWeightRuleEditEl = document.getElementById('enableWeightRuleEdit');
var weightRuleFieldsEditEl = document.getElementById('weightRuleFieldsEdit');
function toggleWeightRuleFieldsEdit() {
    if (!enableWeightRuleEditEl || !weightRuleFieldsEditEl) return;
    var enabled = !!enableWeightRuleEditEl.checked;
    weightRuleFieldsEditEl.classList.toggle('is-hidden', !enabled);
    if (!enabled) {
        ['base_weight_grams', 'extra_weight_step_grams', 'extra_weight_step_price'].forEach(function(name) {
            var input = document.querySelector('[name="' + name + '"]');
            if (input) input.value = '';
        });
    }
}
if (enableWeightRuleEditEl) {
    enableWeightRuleEditEl.addEventListener('change', toggleWeightRuleFieldsEdit);
    toggleWeightRuleFieldsEdit();
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
.pricing-rule-card { border:1px solid #e5e7eb; border-radius:12px; padding: .9rem; background: #fcfcff; }
.pricing-rule-title { font-size:.78rem; font-weight:700; color:#475569; margin-bottom:.55rem; }
.pricing-rule-grid { display:grid; grid-template-columns: repeat(3, minmax(120px, 1fr)); gap:.6rem; }
.pricing-rule-grid .input-group-text { min-width: 42px; justify-content: center; }
.rule-toggle-row { display:flex; align-items:center; justify-content:space-between; gap:.8rem; margin-bottom:.65rem; }
.rule-fields.is-hidden { display:none; }
@media (max-width: 768px) { .pricing-rule-grid { grid-template-columns: 1fr; } }
</style>
@endsection
