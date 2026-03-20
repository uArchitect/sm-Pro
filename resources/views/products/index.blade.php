@extends('layouts.app')

@section('title', __('products.title'))
@section('page-title', __('products.title'))
@section('page-help', __('products.page_help'))

@section('content')
<style>
.drag-handle { cursor:grab; color:#d1d5db; font-size:1rem; padding:0 6px; }
.drag-handle:hover { color:#4F46E5; }
.sortable-ghost { opacity:.4; background:#fff8f5; }
.prod-thumb { width:44px; height:44px; border-radius:10px; object-fit:cover; cursor:pointer; transition:all .15s; }
.prod-thumb:hover { opacity:.7; box-shadow:0 0 0 2px rgba(79,70,229,.4); }
.prod-thumb-empty { width:44px; height:44px; border-radius:10px; background:#f3f4f6; display:flex; align-items:center; justify-content:center; color:#d1d5db; font-size:1.1rem; cursor:pointer; transition:all .15s; }
.prod-thumb-empty:hover { background:#fff0eb; color:#4F46E5; border:1px dashed rgba(79,70,229,.4); }
.cat-chip { font-size:.72rem; background:rgba(99,102,241,.08); color:#6366f1; border-radius:6px; padding:.2rem .55rem; font-weight:600; border:1px solid rgba(99,102,241,.15); cursor:pointer; transition:all .15s; display:inline-block; }
.cat-chip:hover { background:rgba(99,102,241,.16); border-color:rgba(99,102,241,.3); }
.price-tag { font-weight:700; font-size:.9rem; color:#4F46E5; cursor:pointer; border-radius:4px; padding:2px 6px; margin:-2px -6px; transition:background .15s; display:inline-block; }
.price-tag:hover { background:rgba(79,70,229,.08); }
.inline-save-toast { position:fixed; bottom:1.5rem; right:1.5rem; z-index:9999; }
.ie-name { cursor:pointer; border-radius:4px; padding:2px 6px; margin:-2px -6px; transition:background .15s; }
.ie-name:hover { background:rgba(79,70,229,.06); }
.ie-desc { color:#98a2b3; font-size:.78rem; margin-top:.15rem; cursor:pointer; border-radius:4px; padding:1px 6px; margin-left:-6px; transition:background .15s; }
.ie-desc:hover { background:rgba(0,0,0,.03); }
.ie-desc-empty { color:#d1d5db; font-style:italic; }
.ie-input { border:1.5px solid var(--accent); border-radius:6px; padding:4px 8px; font-size:inherit; font-family:inherit; font-weight:inherit; width:100%; outline:none; background:#fff; box-shadow:0 0 0 3px rgba(79,70,229,.12); }
.ie-select { border:1.5px solid var(--accent); border-radius:6px; padding:3px 6px; font-size:.78rem; font-weight:600; outline:none; background:#fff; box-shadow:0 0 0 3px rgba(79,70,229,.12); max-width:180px; cursor:pointer; }
.img-loading { opacity:.3; pointer-events:none; }
.sm-search { border:1.5px solid #e5e7eb; border-radius:9px; padding:.4rem .75rem .4rem 2rem; font-size:.82rem; font-family:'Inter',sans-serif; transition:border-color .15s,box-shadow .15s; width:200px; background:#fff; }
.sm-search:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(79,70,229,.12); outline:none; }
.search-wrap { position:relative; }
.search-wrap i { position:absolute; left:.7rem; top:50%; transform:translateY(-50%); color:#98a2b3; font-size:.8rem; pointer-events:none; }
.views-badge { font-size:.68rem; color:#98a2b3; display:inline-flex; align-items:center; gap:.2rem; margin-top:.1rem; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted small">{{ __('products.total', ['count' => $products->count()]) }} <span class="ms-2 opacity-75">· {{ __('products.reorder_hint') }}</span></span>
    <div class="d-flex gap-2 align-items-center">
        <div class="search-wrap">
            <i class="bi bi-search"></i>
            <input type="text" id="prodSearch" class="sm-search" placeholder="{{ __('products.search') }}">
        </div>
        <a href="{{ route('products.create') }}" class="btn btn-accent btn-sm">
            <i class="bi bi-plus-circle me-1"></i>{{ __('products.add') }}
        </a>
    </div>
</div>

<div class="sm-card">
    <div class="table-responsive">
        <table class="table sm-table align-middle mb-0" id="productsTable">
            <thead>
                <tr>
                    <th style="width:36px"></th>
                    <th style="width:56px"></th>
                    <th>{{ __('products.name') }}</th>
                    <th style="width:80px" class="text-center">Stok</th>
                    <th style="width:150px">{{ __('products.category') }}</th>
                    <th style="width:110px">{{ __('products.price') }}</th>
                    <th class="text-end pe-4" style="width:60px"></th>
                </tr>
            </thead>
            <tbody id="sortableProds">
                @forelse($products as $product)
                <tr data-id="{{ $product->id }}">
                    <td class="ps-3"><i class="bi bi-grip-vertical drag-handle"></i></td>
                    <td>
                        @if($product->image)
                            <img src="{{ asset('uploads/'.$product->image) }}" class="prod-thumb"
                                 data-img="{{ $product->id }}" onclick="triggerImgUpload({{ $product->id }})"
                                 title="{{ __('common.photo') }}">
                        @else
                            <div class="prod-thumb-empty" data-img="{{ $product->id }}"
                                 onclick="triggerImgUpload({{ $product->id }})" title="{{ __('common.photo') }}">
                                <i class="bi bi-camera"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="ie-name" onclick="startEdit(this)" data-field="name"
                             data-id="{{ $product->id }}" data-value="{{ e($product->name) }}">
                            <span style="font-weight:600">{{ $product->name }}</span>
                        </div>
                        <div class="ie-desc {{ $product->description ? '' : 'ie-desc-empty' }}"
                             onclick="startEdit(this)" data-field="description" data-id="{{ $product->id }}">
                            {{ $product->description ? Str::limit($product->description, 60) : __('products.add_desc') }}
                        </div>
                        @if(($viewCounts[$product->id] ?? 0) > 0)
                        <div class="views-badge"><i class="bi bi-eye"></i> {{ $viewCounts[$product->id] }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="form-check form-switch d-inline-block mb-0" style="padding-left:2.5em">
                            <input class="form-check-input toggle-avail-switch" type="checkbox" role="switch"
                                   data-id="{{ $product->id }}"
                                   {{ ($product->is_available ?? 1) ? 'checked' : '' }}
                                   title="{{ ($product->is_available ?? 1) ? 'Stokta var — tıkla: tükendi işaretle' : 'Tükendi — tıkla: stoğa al' }}"
                                   style="cursor:pointer;width:2.2em;height:1.15em">
                        </div>
                    </td>
                    <td>
                        <span class="cat-chip" onclick="startCatEdit(this)" data-field="category_id"
                              data-id="{{ $product->id }}" data-value="{{ $product->category_id }}">
                            {{ $product->category_name }}
                        </span>
                    </td>
                    <td>
                        <span class="price-tag" onclick="startEdit(this)" data-field="price"
                              data-id="{{ $product->id }}" data-value="{{ $product->price }}">
                            {{ number_format($product->price, 2, ',', '.') }} ₺
                        </span>
                        @if(!empty(data_get($product, 'base_weight_grams')) && !empty(data_get($product, 'extra_weight_step_grams')) && !empty(data_get($product, 'extra_weight_step_price')))
                        <div style="font-size:.7rem;color:#98a2b3;margin-top:.15rem">
                            {{ number_format((float) data_get($product, 'base_weight_grams'), 0, ',', '.') }} g + {{ number_format((float) data_get($product, 'extra_weight_step_grams'), 0, ',', '.') }} g = +{{ number_format((float) data_get($product, 'extra_weight_step_price'), 2, ',', '.') }} ₺
                        </div>
                        @elseif(!empty(data_get($product, 'weight_grams')))
                        <div style="font-size:.7rem;color:#98a2b3;margin-top:.15rem">
                            {{ number_format((float) data_get($product, 'weight_grams'), 0, ',', '.') }} g
                        </div>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex gap-1 justify-content-end">
                            <form method="POST" action="{{ route('products.duplicate', $product->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="{{ __('products.duplicate') }}"><i class="bi bi-copy"></i></button>
                            </form>
                            <form method="POST" action="{{ route('products.destroy', $product->id) }}"
                                  onsubmit="return confirm({{ json_encode(__('products.delete_confirm', ['name' => $product->name])) }})">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($products->isEmpty())
<div class="text-center text-muted py-5">
    <i class="bi bi-box-seam fs-1 d-block mb-2 opacity-25"></i>
    {{ __('products.no_products') }}
    <br><a href="{{ route('products.create') }}" class="btn btn-accent btn-sm mt-2">{{ __('products.add_first') }}</a>
</div>
@endif

<input type="file" id="imgUploadInput" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,image/jpeg,image/png,image/gif,image/webp,image/svg+xml" class="d-none">

<div class="inline-save-toast">
    <div id="saveToast" class="toast align-items-center text-bg-success border-0" role="alert" data-bs-autohide="true" data-bs-delay="2000">
        <div class="d-flex">
            <div class="toast-body"><i class="bi bi-check-circle me-2"></i>{{ __('common.saved') }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    <div id="prodErrorToast" class="toast align-items-center text-bg-danger border-0 mt-2" role="alert" data-bs-autohide="true" data-bs-delay="4000">
        <div class="d-flex">
            <div class="toast-body" id="prodErrorToastBody"><i class="bi bi-exclamation-circle me-2"></i></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
<script>
const CSRF = '{{ csrf_token() }}';
const categories = @json($categories);
const fullDescs = @json($products->pluck('description', 'id'));
let uploadingForId = null;
let dtInstance = null;

function initProductsDataTable() {
    if (!$('#productsTable tbody tr[data-id]').length) return null;
    return $('#productsTable').DataTable({
        ordering: false,
        paging: true,
        pageLength: 25,
        dom: 't<"d-flex justify-content-between align-items-center mt-3"ip>',
        language: {
            info: '_START_–_END_ / _TOTAL_',
            infoEmpty: '',
            infoFiltered: '',
            zeroRecords: '<div class="text-center py-4 text-muted"><i class="bi bi-search fs-3 d-block mb-2 opacity-25"></i>{{ __("products.no_products") }}</div>',
            paginate: { previous: '‹', next: '›' }
        },
        columnDefs: [
            { targets: [0, 1, 3, 6], searchable: false, orderable: false }
        ]
    });
}

$(function() {
    dtInstance = initProductsDataTable();
    $('#prodSearch').on('keyup', function() { if (dtInstance) dtInstance.search(this.value).draw(); });
});

Sortable.create(document.getElementById('sortableProds'), {
    handle: '.drag-handle',
    animation: 150,
    ghostClass: 'sortable-ghost',
    onEnd() {
        const order = [...document.querySelectorAll('#sortableProds tr[data-id]')].map(r => r.dataset.id);
        fetch('{{ route("products.reorder") }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
            body: JSON.stringify({order})
        }).then(r => r.json()).then(data => {
            if (data.success) {
                bootstrap.Toast.getOrCreateInstance(document.getElementById('saveToast')).show();
                // DataTables kendi satır sırasını kullanıp tekrar çizince sıra eski haline dönüyordu.
                // Destroy edip DOM'daki (yeni) sırayla yeniden başlatıyoruz.
                if (dtInstance) {
                    dtInstance.destroy();
                    dtInstance = null;
                }
                dtInstance = initProductsDataTable();
            }
        });
    }
});

function startEdit(el) {
    if (el.querySelector('input,textarea,select')) return;
    const field = el.dataset.field;
    const id    = el.dataset.id;
    const origHTML = el.innerHTML;
    let rawVal;
    if (field === 'description') {
        rawVal = fullDescs[id] || '';
    } else {
        rawVal = el.dataset.value || '';
    }

    const input = document.createElement('input');
    input.className = 'ie-input';
    if (field === 'price') {
        input.type = 'number'; input.step = '0.01'; input.min = '0'; input.style.width = '100px';
    } else {
        input.type = 'text';
    }
    input.value = rawVal;
    if (field === 'description') {
        input.placeholder = '{{ __("products.add_desc") }}';
        input.style.fontSize = '.78rem';
    }

    el.innerHTML = '';
    el.appendChild(input);
    input.focus();
    input.select();

    let done = false;
    const finish = async (save) => {
        if (done) return;
        done = true;
        const nv = input.value.trim();
        if (!save || (field !== 'description' && !nv) || nv === rawVal) {
            el.innerHTML = origHTML;
            return;
        }
        el.innerHTML = '<span class="spinner-border spinner-border-sm text-muted"></span>';
        const fd = new FormData();
        fd.append('_token', CSRF);
        fd.append(field, nv);
        try {
            const res = await fetch(`/products/${id}/inline-update`, {method:'POST', body:fd});
            const data = await res.json();
            if (data.success) {
                if (field === 'name') {
                    el.dataset.value = data.name;
                    el.innerHTML = '<span style="font-weight:600">' + esc(data.name) + '</span>';
                } else if (field === 'price') {
                    el.dataset.value = data.raw_price;
                    el.textContent = data.price + ' ₺';
                } else if (field === 'description') {
                    fullDescs[id] = data.description || '';
                    el.className = 'ie-desc ' + (data.description ? '' : 'ie-desc-empty');
                    el.textContent = data.description_short || '{{ __("products.add_desc") }}';
                }
                afterEdit();
            } else { el.innerHTML = origHTML; }
        } catch(e) { el.innerHTML = origHTML; }
    };

    input.addEventListener('blur', () => finish(true));
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); input.blur(); }
        if (e.key === 'Escape') { e.preventDefault(); finish(false); }
    });
}

function startCatEdit(el) {
    if (el.querySelector('select')) return;
    const id = el.dataset.id;
    const curVal = el.dataset.value;
    const origHTML = el.innerHTML;

    const sel = document.createElement('select');
    sel.className = 'ie-select';
    categories.forEach(c => {
        const o = document.createElement('option');
        o.value = c.id; o.textContent = c.name;
        if (c.id == curVal) o.selected = true;
        sel.appendChild(o);
    });

    el.innerHTML = '';
    el.appendChild(sel);
    sel.focus();

    let done = false;
    const finish = async (save) => {
        if (done) return;
        done = true;
        if (!save || sel.value == curVal) { el.innerHTML = origHTML; return; }
        el.innerHTML = '<span class="spinner-border spinner-border-sm text-muted"></span>';
        const fd = new FormData();
        fd.append('_token', CSRF);
        fd.append('category_id', sel.value);
        try {
            const res = await fetch(`/products/${id}/inline-update`, {method:'POST', body:fd});
            const data = await res.json();
            if (data.success) {
                el.dataset.value = sel.value;
                el.textContent = data.category_name;
                afterEdit();
            } else { el.innerHTML = origHTML; }
        } catch(e) { el.innerHTML = origHTML; }
    };
    sel.addEventListener('change', () => finish(true));
    sel.addEventListener('blur', () => { if(!done){done=true; el.innerHTML=origHTML;} });
}

function triggerImgUpload(pid) {
    uploadingForId = pid;
    const inp = document.getElementById('imgUploadInput');
    inp.value = '';
    inp.click();
}

document.getElementById('imgUploadInput').addEventListener('change', async function() {
    if (!this.files[0] || !uploadingForId) return;
    const id = uploadingForId;
    const imgEl = document.querySelector(`[data-img="${id}"]`);
    imgEl.classList.add('img-loading');

    const fd = new FormData();
    fd.append('_token', CSRF);
    fd.append('image', this.files[0]);
    try {
        const res = await fetch(`/products/${id}/inline-update`, { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        if (res.ok && data.success && data.image_url) {
            if (imgEl.tagName === 'IMG') {
                imgEl.src = data.image_url + '?t=' + Date.now();
            } else {
                const img = document.createElement('img');
                img.src = data.image_url;
                img.className = 'prod-thumb';
                img.dataset.img = id;
                img.onclick = () => triggerImgUpload(id);
                img.title = '{{ __("common.photo") }}';
                imgEl.replaceWith(img);
            }
            afterEdit();
        } else {
            const msg = (data.errors && data.errors.image) ? data.errors.image[0] : (data.message || '{{ __("common.error") }}');
            showProdErrorToast(msg);
        }
    } catch(e) {
        showProdErrorToast('{{ __("common.error") }}');
    } finally { imgEl.classList.remove('img-loading'); }
    this.value = '';
});

function afterEdit() {
    bootstrap.Toast.getOrCreateInstance(document.getElementById('saveToast')).show();
    if (dtInstance) dtInstance.rows().invalidate().draw(false);
}
function showProdErrorToast(msg) {
    document.getElementById('prodErrorToastBody').innerHTML = '<i class="bi bi-exclamation-circle me-2"></i>' + (msg || '{{ __("common.error") }}');
    bootstrap.Toast.getOrCreateInstance(document.getElementById('prodErrorToast')).show();
}

function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

document.querySelectorAll('.toggle-avail-switch').forEach(sw => {
    sw.addEventListener('change', async function() {
        const id = this.dataset.id;
        const fd = new FormData();
        fd.append('_token', CSRF);
        this.disabled = true;
        try {
            const res  = await fetch(`/products/${id}/toggle-availability`, { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                this.checked = data.is_available;
                this.title = data.is_available ? 'Stokta var — tıkla: tükendi işaretle' : 'Tükendi — tıkla: stoğa al';
                afterEdit();
            } else {
                this.checked = !this.checked; // geri al
            }
        } catch(e) {
            this.checked = !this.checked; // hata durumunda geri al
        } finally {
            this.disabled = false;
        }
    });
});
</script>
@endpush
@endsection
