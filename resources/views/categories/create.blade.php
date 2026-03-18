@extends('layouts.app')

@section('title', __('categories.add'))
@section('page-title', __('categories.add'))
@section('page-help', __('categories.page_help_create'))
@section('content')
<style>
.nav-tabs .nav-link { border-radius: 9px 9px 0 0; font-weight: 600; font-size: .85rem; padding: .5rem 1rem; color: var(--text-secondary); border: 1px solid transparent; }
.nav-tabs .nav-link:hover { border-color: #e5e7eb; color: var(--text-primary); }
.nav-tabs .nav-link.active { background: #fff; border-color: var(--border); border-bottom-color: #fff; color: var(--accent); }
.tab-content { border: 1px solid var(--border); border-top: none; border-radius: 0 0 12px 12px; padding: 1.25rem; background: #fff; }
/* SearchableSelect (toplu kategori üst kategori) */
.ss-wrapper { position: relative; width: 100%; }
.ss-native { position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; pointer-events: none; z-index: 0; }
.ss-trigger { width: 100%; text-align: left; cursor: pointer; display: flex; align-items: center; justify-content: space-between; gap: .5rem; background: #fff; border: 1.5px solid #e5e7eb; border-radius: 9px; padding: .5rem .75rem; font-size: .875rem; }
.ss-trigger:hover { border-color: #d1d5db; }
.ss-trigger:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
.ss-trigger-text { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.ss-trigger-text.ss-placeholder { color: #9ca3af; }
.ss-trigger-icon { color: #9ca3af; font-size: .7rem; flex-shrink: 0; }
.ss-dropdown { display: none; position: absolute; left: 0; right: 0; top: 100%; margin-top: 2px; z-index: 1050; background: #fff; border: 1px solid #e5e7eb; border-radius: 9px; box-shadow: 0 10px 40px rgba(0,0,0,.12); overflow: hidden; }
.ss-dropdown.ss-open { display: block; }
.ss-search { border: none !important; border-bottom: 1px solid #e5e7eb !important; border-radius: 0 !important; padding: .5rem .65rem !important; font-size: .82rem !important; }
.ss-search:focus { box-shadow: none !important; outline: none !important; }
.ss-list { max-height: 200px; overflow-y: auto; padding: .25rem 0; }
.ss-option { padding: .45rem .65rem; font-size: .82rem; cursor: pointer; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ss-option:hover, .ss-option.ss-highlight { background: rgba(79,70,229,.08); color: var(--accent); }
.ss-option.ss-selected { font-weight: 600; color: var(--accent); }
</style>

<ul class="nav nav-tabs mb-0" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="tab-single" data-bs-toggle="tab" href="#single" role="tab">{{ __('categories.tab_single') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="tab-bulk" data-bs-toggle="tab" href="#bulk" role="tab">{{ __('categories.tab_bulk') }}</a>
    </li>
</ul>

<div class="tab-content">
    {{-- Tek kategori --}}
    <div class="tab-pane fade show active" id="single" role="tabpanel">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">{{ __('categories.photo_optional') }} <span class="text-muted">{{ __('common.optional') }}</span></label>
                        <div class="img-upload-zone" id="imgZone" onclick="document.getElementById('imgInput').click()">
                            <img id="imgPreview" src="" class="img-preview d-none">
                            <div id="imgPlaceholder" class="text-center">
                                <i class="bi bi-cloud-upload fs-2" style="color:#d1d5db"></i>
                                <div class="mt-1 small" style="color:#9ca3af">{{ __('common.click_or_drop') }}</div>
                                <div style="font-size:.72rem;color:#d1d5db">{{ __('common.max_file_size') }}</div>
                            </div>
                        </div>
                        <input type="file" id="imgInput" name="image" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,image/jpeg,image/png,image/gif,image/webp,image/svg+xml" class="input-file-visible-submit @error('image') is-invalid @enderror">
                        @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <button type="button" class="btn btn-link btn-sm text-danger p-0 mt-1 d-none" id="removeBtn" onclick="removeImg()">
                            <i class="bi bi-x-circle me-1"></i>{{ __('common.remove_photo') }}
                        </button>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">{{ __('categories.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="{{ __('categories.name_placeholder') }}" required autofocus>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">{{ __('categories.parent_category') }} <span class="text-muted">{{ __('common.optional') }}</span></label>
                        <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">{{ __('categories.parent_none') }}</option>
                            @foreach($parents as $p)
                            <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">{{ __('categories.parent_hint') }}</div>
                        @error('parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex gap-2 form-actions-wrap">
                        <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg me-1"></i>{{ __('categories.add_btn') }}</button>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Toplu kategori --}}
    <div class="tab-pane fade" id="bulk" role="tabpanel">
        <p class="text-muted small mb-3">{{ __('categories.bulk_hint') }}</p>
        <form method="POST" action="{{ route('categories.store.bulk') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ __('categories.parent_category') }} <span class="text-muted">({{ __('categories.bulk_parent_all') }})</span></label>
                <select name="parent_id" class="form-select" id="bulkCategoryParentSelect">
                    <option value="">{{ __('categories.parent_none') }}</option>
                    @foreach($parents as $p)
                    <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <label class="form-label">{{ __('categories.name') }} <span class="text-danger">*</span></label>
            <div id="categoryNameRows">
                <div class="category-name-row mb-2 d-flex gap-2 align-items-center">
                    <input type="text" name="names[]" class="form-control" placeholder="{{ __('categories.name_placeholder') }}" value="{{ old('names.0') }}">
                    <button type="button" class="btn btn-outline-secondary btn-sm cat-remove" style="visibility:hidden" title="{{ __('categories.bulk_remove_row') }}"><i class="bi bi-dash-lg"></i></button>
                </div>
                @if(is_array(old('names')))
                    @foreach(old('names') as $idx => $oldName)
                        @if($idx > 0)
                        <div class="category-name-row mb-2 d-flex gap-2 align-items-center">
                            <input type="text" name="names[]" class="form-control" placeholder="{{ __('categories.name_placeholder') }}" value="{{ $oldName }}">
                            <button type="button" class="btn btn-outline-danger btn-sm cat-remove"><i class="bi bi-dash-lg"></i></button>
                        </div>
                        @endif
                    @endforeach
                @endif
            </div>
            @error('names')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" id="catAddMore" class="btn btn-outline-secondary btn-sm"><i class="bi bi-plus-lg me-1"></i>{{ __('categories.bulk_add_row') }}</button>
            </div>
            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg me-1"></i>{{ __('categories.bulk_save') }}</button>
                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/searchable-select.js') }}"></script>
<script>
@php $jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE; @endphp
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
    zone.addEventListener('dragover', function(e) { e.preventDefault(); zone.style.borderColor='#4F46E5'; });
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

var catPlaceholder = {!! json_encode(__('categories.name_placeholder'), $jsonFlags) !!};
var catRemoveTitle = {!! json_encode(__('categories.bulk_remove_row'), $jsonFlags) !!};
document.getElementById('catAddMore').addEventListener('click', function() {
    var container = document.getElementById('categoryNameRows');
    var div = document.createElement('div');
    div.className = 'category-name-row mb-2 d-flex gap-2 align-items-center';
    div.innerHTML = '<input type="text" name="names[]" class="form-control" placeholder="'+catPlaceholder+'">' +
        '<button type="button" class="btn btn-outline-danger btn-sm cat-remove" title="'+catRemoveTitle+'"><i class="bi bi-dash-lg"></i></button>';
    container.appendChild(div);
    div.querySelector('input').focus();
    updateCatRemoveVisibility();
});
document.getElementById('categoryNameRows').addEventListener('click', function(e) {
    if (e.target.closest('.cat-remove')) {
        e.target.closest('.category-name-row').remove();
        updateCatRemoveVisibility();
    }
});
function updateCatRemoveVisibility() {
    var rows = document.getElementById('categoryNameRows').querySelectorAll('.category-name-row');
    rows.forEach(function(r, i) {
        var btn = r.querySelector('.cat-remove');
        if (btn) btn.style.visibility = rows.length > 1 ? 'visible' : 'hidden';
    });
}
updateCatRemoveVisibility();

document.getElementById('tab-bulk') && document.getElementById('tab-bulk').addEventListener('shown.bs.tab', function() {
    var sel = document.getElementById('bulkCategoryParentSelect');
    if (sel && typeof SearchableSelect !== 'undefined') {
        SearchableSelect.enhance(sel, {
            placeholder: {!! json_encode(__('categories.parent_none'), $jsonFlags) !!},
            searchPlaceholder: {!! json_encode(__('categories.search_parent'), $jsonFlags) !!}
        });
    }
});
</script>
@endpush

<style>
.img-upload-zone { border:2px dashed #e5e7eb; border-radius:12px; padding:1.5rem; text-align:center; cursor:pointer; transition:border-color .15s,background .15s; min-height:120px; display:flex; align-items:center; justify-content:center; }
.img-upload-zone:hover { border-color:#4F46E5; background:#fff8f5; }
.img-preview { max-width:100%; max-height:180px; border-radius:8px; object-fit:contain; }
</style>
@endsection
