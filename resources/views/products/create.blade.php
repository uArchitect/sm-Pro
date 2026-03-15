@extends('layouts.app')

@section('title', __('products.add'))
@section('page-title', __('products.add'))
@section('content')
<style>
.nav-tabs .nav-link { border-radius: 9px 9px 0 0; font-weight: 600; font-size: .85rem; padding: .5rem 1rem; color: var(--text-secondary); border: 1px solid transparent; }
.nav-tabs .nav-link:hover { border-color: #e5e7eb; color: var(--text-primary); }
.nav-tabs .nav-link.active { background: #fff; border-color: var(--border); border-bottom-color: #fff; color: var(--accent); }
.tab-content { border: 1px solid var(--border); border-top: none; border-radius: 0 0 12px 12px; padding: 1.25rem; background: #fff; }
.bulk-table { font-size: .85rem; }
.bulk-table th { font-weight: 700; font-size: .72rem; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); }
.bulk-table input, .bulk-table textarea { font-size: .82rem; }
.bulk-table textarea { resize: vertical; min-height: 38px; }

/* SearchableSelect — dropdown her zaman trigger'ın altında açılsın */
#bulkProductRows td:first-child,
#bulkProductRows th:first-child { overflow: visible; }
.ss-wrapper { position: relative; width: 100%; transform: translateZ(0); min-height: 2rem; }
.ss-native { position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; pointer-events: none; z-index: 0; }
.ss-trigger { width: 100%; text-align: left; cursor: pointer; display: flex; align-items: center; justify-content: space-between; gap: .5rem; background: #fff; border: 1.5px solid #e5e7eb; border-radius: 9px; padding: .35rem .65rem; font-size: .82rem; }
.ss-trigger:hover { border-color: #d1d5db; }
.ss-trigger:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(255,107,53,.12); }
.ss-trigger-text { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.ss-trigger-text.ss-placeholder { color: #9ca3af; }
.ss-trigger-icon { color: #9ca3af; font-size: .7rem; flex-shrink: 0; }
.ss-dropdown { display: none; position: absolute; left: 0; right: 0; top: 100%; bottom: auto; margin-top: 2px; z-index: 1050; background: #fff; border: 1px solid #e5e7eb; border-radius: 9px; box-shadow: 0 10px 40px rgba(0,0,0,.12); overflow: hidden; }
.ss-dropdown.ss-open { display: block; }
.ss-search { border: none !important; border-bottom: 1px solid #e5e7eb !important; border-radius: 0 !important; padding: .5rem .65rem !important; font-size: .82rem !important; }
.ss-search:focus { box-shadow: none !important; outline: none !important; }
.ss-list { max-height: 200px; overflow-y: auto; padding: .25rem 0; }
.ss-option { padding: .45rem .65rem; font-size: .82rem; cursor: pointer; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ss-option:hover, .ss-option.ss-highlight { background: rgba(255,107,53,.08); color: var(--accent); }
.ss-option.ss-selected { font-weight: 600; color: var(--accent); }
</style>

<ul class="nav nav-tabs mb-0" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="tab-single" data-bs-toggle="tab" href="#single" role="tab">{{ __('products.tab_single') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="tab-bulk" data-bs-toggle="tab" href="#bulk" role="tab">{{ __('products.tab_bulk') }}</a>
    </li>
</ul>

<div class="tab-content">
    {{-- Tek ürün --}}
    <div class="tab-pane fade show active" id="single" role="tabpanel">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="sm-card-body p-0">
                    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" novalidate>
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">{{ __('products.photo_optional') }} <span class="text-muted">{{ __('common.optional') }}</span></label>
                            <div class="img-upload-zone" id="imgZone" onclick="document.getElementById('imgInput').click()">
                                <img id="imgPreview" src="" class="img-preview d-none">
                                <div id="imgPlaceholder" class="text-center">
                                    <i class="bi bi-cloud-upload fs-2" style="color:#d1d5db"></i>
                                    <div class="mt-1 small" style="color:#9ca3af">{{ __('common.click_or_drop') }}</div>
                                    <div style="font-size:.72rem;color:#d1d5db">{{ __('common.max_file_size') }}</div>
                                </div>
                            </div>
                            <input type="file" id="imgInput" name="image" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,image/jpeg,image/png,image/gif,image/webp,image/svg+xml" class="input-file-visible-submit">
                            @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <button type="button" class="btn btn-link btn-sm text-danger p-0 mt-1 d-none" id="removeBtn" onclick="removeImg()">
                                <i class="bi bi-x-circle me-1"></i>{{ __('common.remove_photo') }}
                            </button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">{{ __('products.category') }} <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">{{ __('products.select_category') }}</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if($categories->isEmpty())
                                <div class="form-text text-warning"><i class="bi bi-exclamation-triangle me-1"></i>{!! __('products.add_category_first', ['link' => route('categories.create')]) !!}</div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">{{ __('products.name') }}</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="{{ __('products.name_placeholder') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">{{ __('products.description') }} <span class="text-muted">{{ __('common.optional') }}</span></label>
                            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="{{ __('products.description_placeholder') }}">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">{{ __('products.price_tl') }}</label>
                            <div class="input-group" style="max-width:140px">
                                <span class="input-group-text">₺</span>
                                <input type="number" name="price" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" placeholder="0.00" required>
                                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="d-flex gap-2 form-actions-wrap">
                            <button type="submit" class="btn btn-accent" {{ $categories->isEmpty() ? 'disabled' : '' }}><i class="bi bi-check-lg me-1"></i>{{ __('common.save') }}</button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Toplu ürün --}}
    <div class="tab-pane fade" id="bulk" role="tabpanel">
        <p class="text-muted small mb-3">{{ __('products.bulk_hint') }}</p>
        @if($categories->isEmpty())
        <div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>{!! __('products.add_category_first', ['link' => route('categories.create')]) !!}</div>
        @else
        <form method="POST" action="{{ route('products.store.bulk') }}" id="bulkProductForm">
            @csrf
            <div class="table-responsive">
                <table class="table bulk-table align-middle mb-0" id="bulkProductsTable">
                    <thead>
                        <tr>
                            <th style="width:180px">{{ __('products.category') }}</th>
                            <th>{{ __('products.name') }}</th>
                            <th style="width:28%">{{ __('products.description') }} <span class="text-muted">({{ __('common.optional') }})</span></th>
                            <th style="width:100px">{{ __('products.price_tl') }}</th>
                            <th style="width:44px"></th>
                        </tr>
                    </thead>
                    <tbody id="bulkProductRows">
                        @foreach(old('products', [['category_id' => '', 'name' => '', 'description' => '', 'price' => '']]) as $idx => $p)
                        <tr class="bulk-row">
                            <td>
                                <select name="products[{{ $idx }}][category_id]" class="form-select form-select-sm" required>
                                    <option value="">{{ __('products.select_category') }}</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('products.'.$idx.'.category_id', $p['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="products[{{ $idx }}][name]" class="form-control form-control-sm" placeholder="{{ __('products.name_placeholder') }}" value="{{ old('products.'.$idx.'.name', $p['name'] ?? '') }}"></td>
                            <td><textarea name="products[{{ $idx }}][description]" class="form-control form-control-sm" rows="1" placeholder="{{ __('products.description_placeholder') }}">{{ old('products.'.$idx.'.description', $p['description'] ?? '') }}</textarea></td>
                            <td><input type="number" name="products[{{ $idx }}][price]" class="form-control form-control-sm" step="0.01" min="0" placeholder="0" value="{{ old('products.'.$idx.'.price', $p['price'] ?? '') }}"></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger bulk-remove" title="{{ __('products.bulk_remove_row') }}"><i class="bi bi-dash-lg"></i></button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" id="bulkAddRow" class="btn btn-outline-secondary btn-sm"><i class="bi bi-plus-lg me-1"></i>{{ __('products.bulk_add_row') }}</button>
            </div>
            @error('products')<div class="invalid-feedback d-block mt-2">{{ $message }}</div>@enderror
            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-accent" id="bulkSubmitBtn"><i class="bi bi-check-lg me-1"></i>{{ __('products.bulk_save') }}</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
            </div>
        </form>
        @endif
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/searchable-select.js') }}"></script>
<script>
document.getElementById('imgInput').addEventListener('change', function() {
    if (!this.files[0]) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('imgPreview').src = e.target.result;
        document.getElementById('imgPreview').classList.remove('d-none');
        document.getElementById('imgPlaceholder').classList.add('d-none');
        document.getElementById('removeBtn').classList.remove('d-none');
    };
    reader.readAsDataURL(this.files[0]);
});
var zone = document.getElementById('imgZone');
if (zone) {
    zone.addEventListener('dragover', function(e) { e.preventDefault(); zone.style.borderColor='#FF6B35'; });
    zone.addEventListener('dragleave', function() { zone.style.borderColor=''; });
    zone.addEventListener('drop', function(e) {
        e.preventDefault(); zone.style.borderColor='';
        var dt = new DataTransfer();
        dt.items.add(e.dataTransfer.files[0]);
        document.getElementById('imgInput').files = dt.files;
        document.getElementById('imgInput').dispatchEvent(new Event('change'));
    });
}
function removeImg() {
    document.getElementById('imgInput').value = '';
    document.getElementById('imgPreview').classList.add('d-none');
    document.getElementById('imgPlaceholder').classList.remove('d-none');
    document.getElementById('removeBtn').classList.add('d-none');
}

@php
    $jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
@endphp
var bulkIdx = {{ count(old('products', [[]])) }};
var bulkCategoriesOptions = {!! json_encode($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values(), $jsonFlags) !!};
function escHtml(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
var selectCategoryPlaceholder = {!! json_encode(__('products.select_category'), $jsonFlags) !!};
var selectCategoryHtml = '<option value="">' + selectCategoryPlaceholder + '</option>' + bulkCategoriesOptions.map(function(c) { return '<option value="'+c.id+'">'+escHtml(c.name)+'</option>'; }).join('');
var bulkPlaceholderName = {!! json_encode(__('products.name_placeholder'), $jsonFlags) !!};
var bulkPlaceholderDesc = {!! json_encode(__('products.description_placeholder'), $jsonFlags) !!};
var bulkRemoveTitle = {!! json_encode(__('products.bulk_remove_row'), $jsonFlags) !!};

document.getElementById('bulkAddRow') && document.getElementById('bulkAddRow').addEventListener('click', function() {
    var tbody = document.getElementById('bulkProductRows');
    var tr = document.createElement('tr');
    tr.className = 'bulk-row';
    tr.innerHTML = '<td><select name="products['+bulkIdx+'][category_id]" class="form-select form-select-sm" required>'+selectCategoryHtml+'</select></td>' +
        '<td><input type="text" name="products['+bulkIdx+'][name]" class="form-control form-control-sm" placeholder="'+bulkPlaceholderName+'"></td>' +
        '<td><textarea name="products['+bulkIdx+'][description]" class="form-control form-control-sm" rows="1" placeholder="'+bulkPlaceholderDesc+'"></textarea></td>' +
        '<td><input type="number" name="products['+bulkIdx+'][price]" class="form-control form-control-sm" step="0.01" min="0" placeholder="0"></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger bulk-remove" title="'+bulkRemoveTitle+'"><i class="bi bi-dash-lg"></i></button></td>';
    tbody.appendChild(tr);
    bulkIdx++;
    var newSelect = tr.querySelector('select');
    if (newSelect && typeof SearchableSelect !== 'undefined') {
        SearchableSelect.enhance(newSelect, {
            placeholder: {!! json_encode(__('products.select_category'), $jsonFlags) !!},
            searchPlaceholder: {!! json_encode(__('products.search_category'), $jsonFlags) !!}
        });
    }
});

function initBulkProductSearchableSelects() {
    var container = document.getElementById('bulkProductRows');
    if (!container || typeof SearchableSelect === 'undefined') return;
    SearchableSelect.enhanceAll(container, {
        placeholder: {!! json_encode(__('products.select_category'), $jsonFlags) !!},
        searchPlaceholder: {!! json_encode(__('products.search_category'), $jsonFlags) !!}
    });
}
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('bulkProductRows') && document.querySelector('#bulk select.form-select')) initBulkProductSearchableSelects();
});
document.getElementById('tab-bulk') && document.getElementById('tab-bulk').addEventListener('shown.bs.tab', function() {
    initBulkProductSearchableSelects();
});

document.getElementById('bulkProductRows') && document.getElementById('bulkProductRows').addEventListener('click', function(e) {
    if (e.target.closest('.bulk-remove')) {
        var row = e.target.closest('tr');
        if (document.getElementById('bulkProductRows').querySelectorAll('tr').length > 1) row.remove();
    }
});
</script>
@endpush

<style>
.img-upload-zone { border:2px dashed #e5e7eb; border-radius:12px; padding:1.5rem; text-align:center; cursor:pointer; transition:border-color .15s,background .15s; min-height:120px; display:flex; align-items:center; justify-content:center; }
.img-upload-zone:hover { border-color:#FF6B35; background:#fff8f5; }
.img-preview { max-width:100%; max-height:180px; border-radius:8px; object-fit:contain; }
</style>
@endsection
