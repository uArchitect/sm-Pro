@extends('layouts.app')

@section('title', 'Ürünler')
@section('page-title', 'Ürünler')

@section('content')
<style>
.drag-handle { cursor:grab; color:#d1d5db; font-size:1rem; padding:0 6px; }
.drag-handle:hover { color:#FF6B35; }
.sortable-ghost { opacity:.4; background:#fff8f5; }
.prod-thumb { width:44px; height:44px; border-radius:10px; object-fit:cover; }
.prod-thumb-empty { width:44px; height:44px; border-radius:10px; background:#f3f4f6; display:flex; align-items:center; justify-content:center; color:#d1d5db; font-size:1.1rem; }
.cat-chip { font-size:.72rem; background:rgba(99,102,241,.08); color:#6366f1; border-radius:6px; padding:.2rem .55rem; font-weight:600; border:1px solid rgba(99,102,241,.15); }
.price-tag { font-weight:700; font-size:.9rem; color:#FF6B35; }
.inline-save-toast { position:fixed; bottom:1.5rem; right:1.5rem; z-index:9999; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted small">{{ __('products.total', ['count' => $products->count()]) }}</span>
    <a href="{{ route('products.create') }}" class="btn btn-accent btn-sm">
        <i class="bi bi-plus-circle me-1"></i>{{ __('products.add') }}
    </a>
</div>

<div class="sm-card">
    <div class="table-responsive">
        <table class="table sm-table align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:36px"></th>
                    <th style="width:56px"></th>
                    <th>{{ __('products.name') }}</th>
                    <th style="width:150px">{{ __('products.category') }}</th>
                    <th style="width:110px">{{ __('products.price') }}</th>
                    <th class="text-end pe-4" style="width:140px">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody id="sortableProds">
                @forelse($products as $product)
                <tr data-id="{{ $product->id }}">
                    <td class="ps-3"><i class="bi bi-grip-vertical drag-handle"></i></td>
                    <td>
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" class="prod-thumb" data-img="{{ $product->id }}">
                        @else
                            <div class="prod-thumb-empty" data-img="{{ $product->id }}"><i class="bi bi-box-seam"></i></div>
                        @endif
                    </td>
                    <td>
                        <span class="fw-600 prod-name" data-name="{{ $product->id }}" style="font-weight:600">{{ $product->name }}</span>
                        @if($product->description)
                        <div class="text-muted" style="font-size:.78rem;margin-top:.15rem">{{ Str::limit($product->description, 50) }}</div>
                        @endif
                    </td>
                    <td><span class="cat-chip prod-cat" data-cat="{{ $product->id }}">{{ $product->category_name }}</span></td>
                    <td><span class="price-tag prod-price" data-price="{{ $product->id }}">{{ number_format($product->price, 2, ',', '.') }} ₺</span></td>
                    <td class="text-end pe-4">
                        <div class="d-flex gap-1 justify-content-end">
                            <button class="btn btn-sm btn-outline-secondary"
                                onclick="openEditModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->category_id }}, {{ $product->image ? "'".asset('storage/'.$product->image)."'" : 'null' }})"
                                title="{{ __('common.edit') }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('products.destroy', $product->id) }}"
                                  onsubmit="return confirm({{ json_encode(__('products.delete_confirm', ['name' => $product->name])) }})">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-box-seam fs-1 d-block mb-2 opacity-25"></i>
                        {{ __('products.no_products') }}
                        <br><a href="{{ route('products.create') }}" class="btn btn-accent btn-sm mt-2">{{ __('products.add_first') }}</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Inline Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">
            <div class="modal-header" style="background:#0f1923;border:none;padding:1.1rem 1.4rem">
                <h6 class="modal-title text-white mb-0" style="font-weight:700">
                    <i class="bi bi-pencil-square me-2" style="color:#FF6B35"></i>{{ __('products.edit_product') }}
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="editAlert" class="alert alert-danger d-none py-2 small mb-3"></div>

                {{-- Image upload --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('common.photo') }}</label>
                    <div class="img-upload-zone" id="prodImgZone" onclick="document.getElementById('prodImgInput').click()">
                        <img id="prodImgPreview" src="" class="img-preview d-none">
                        <div id="prodImgPlaceholder">
                            <i class="bi bi-cloud-upload" style="font-size:1.6rem;color:#d1d5db"></i>
                            <div class="mt-1" style="font-size:.78rem;color:#9ca3af">{{ __('common.click_or_drop') }}</div>
                            <div style="font-size:.72rem;color:#d1d5db">{{ __('common.max_file_size') }}</div>
                        </div>
                    </div>
                    <input type="file" id="prodImgInput" accept="image/*" class="d-none">
                    <button type="button" class="btn btn-link btn-sm text-danger p-0 mt-1 d-none" id="prodRemoveImgBtn" onclick="prodRemoveImg()">
                        <i class="bi bi-x-circle me-1"></i>{{ __('common.remove_photo') }}
                    </button>
                </div>

                {{-- Name --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('products.name') }}</label>
                    <input type="text" id="editProdName" class="form-control" placeholder="{{ __('products.name_placeholder') }}" maxlength="255">
                </div>

                {{-- Price --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('products.price_tl') }}</label>
                    <div class="input-group">
                        <span class="input-group-text">₺</span>
                        <input type="number" id="editProdPrice" class="form-control" step="0.01" min="0" placeholder="0.00">
                    </div>
                </div>

                {{-- Category --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('products.category') }}</label>
                    <select id="editProdCat" class="form-select">
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer" style="border:none;padding:.75rem 1.4rem 1.2rem">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-accent btn-sm" id="saveProdBtn" onclick="saveEdit()">
                    <i class="bi bi-check-lg me-1"></i>{{ __('common.save') }}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Toast --}}
<div class="inline-save-toast">
    <div id="saveToast" class="toast align-items-center text-bg-success border-0" role="alert" data-bs-autohide="true" data-bs-delay="2500">
        <div class="d-flex">
            <div class="toast-body"><i class="bi bi-check-circle me-2"></i>{{ __('common.saved') }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
<script>
const CSRF = '{{ csrf_token() }}';
let editingId  = null;
let removingImg = false;

// Drag-drop sort
Sortable.create(document.getElementById('sortableProds'), {
    handle: '.drag-handle',
    animation: 150,
    ghostClass: 'sortable-ghost',
    onEnd() {
        const order = [...document.querySelectorAll('#sortableProds tr[data-id]')]
            .map(r => r.dataset.id);
        fetch('{{ route("products.reorder") }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
            body: JSON.stringify({order})
        });
    }
});

// Open modal
function openEditModal(id, name, price, catId, imgUrl) {
    editingId   = id;
    removingImg = false;
    document.getElementById('editProdName').value  = name;
    document.getElementById('editProdPrice').value = price;
    document.getElementById('editProdCat').value   = catId;
    document.getElementById('prodImgInput').value  = '';
    document.getElementById('editAlert').classList.add('d-none');

    const preview     = document.getElementById('prodImgPreview');
    const placeholder = document.getElementById('prodImgPlaceholder');
    const removeBtn   = document.getElementById('prodRemoveImgBtn');

    if (imgUrl) {
        preview.src = imgUrl; preview.classList.remove('d-none');
        placeholder.classList.add('d-none');
        removeBtn.classList.remove('d-none');
    } else {
        preview.classList.add('d-none');
        placeholder.classList.remove('d-none');
        removeBtn.classList.add('d-none');
    }
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

// Image preview
document.getElementById('prodImgInput').addEventListener('change', function() {
    if (!this.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('prodImgPreview').src = e.target.result;
        document.getElementById('prodImgPreview').classList.remove('d-none');
        document.getElementById('prodImgPlaceholder').classList.add('d-none');
        document.getElementById('prodRemoveImgBtn').classList.remove('d-none');
    };
    reader.readAsDataURL(this.files[0]);
});

// Drag-drop zone
const zone = document.getElementById('prodImgZone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor='#FF6B35'; });
zone.addEventListener('dragleave', () => zone.style.borderColor='');
zone.addEventListener('drop', e => {
    e.preventDefault(); zone.style.borderColor='';
    const dt = new DataTransfer();
    dt.items.add(e.dataTransfer.files[0]);
    document.getElementById('prodImgInput').files = dt.files;
    document.getElementById('prodImgInput').dispatchEvent(new Event('change'));
});

function prodRemoveImg() {
    removingImg = true;
    document.getElementById('prodImgPreview').classList.add('d-none');
    document.getElementById('prodImgPlaceholder').classList.remove('d-none');
    document.getElementById('prodRemoveImgBtn').classList.add('d-none');
    document.getElementById('prodImgInput').value = '';
}

async function saveEdit() {
    const name  = document.getElementById('editProdName').value.trim();
    const price = document.getElementById('editProdPrice').value;
    if (!name || price === '') {
        const al = document.getElementById('editAlert');
        al.textContent = '{{ __('products.name_price_required') }}';
        al.classList.remove('d-none');
        return;
    }

    const btn = document.getElementById('saveProdBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>{{ __('common.loading') }}';

    const fd = new FormData();
    fd.append('_token', CSRF);
    fd.append('name', name);
    fd.append('price', price);
    fd.append('category_id', document.getElementById('editProdCat').value);
    if (removingImg) fd.append('remove_image', '1');
    const file = document.getElementById('prodImgInput').files[0];
    if (file) fd.append('image', file);

    try {
        const res  = await fetch(`/products/${editingId}/inline-update`, {method:'POST', body:fd});
        const data = await res.json();

        if (data.success) {
            document.querySelector(`[data-name="${editingId}"]`).textContent = data.name;
            document.querySelector(`[data-price="${editingId}"]`).textContent = data.price + ' ₺';
            document.querySelector(`[data-cat="${editingId}"]`).textContent = data.category_name;

            const imgEl = document.querySelector(`[data-img="${editingId}"]`);
            if (data.image_url) {
                if (imgEl.tagName === 'IMG') {
                    imgEl.src = data.image_url + '?t=' + Date.now();
                } else {
                    const img = document.createElement('img');
                    img.src = data.image_url;
                    img.className = 'prod-thumb';
                    img.dataset.img = editingId;
                    imgEl.replaceWith(img);
                }
            } else if (removingImg) {
                const div = document.createElement('div');
                div.className = 'prod-thumb-empty';
                div.dataset.img = editingId;
                div.innerHTML = '<i class="bi bi-box-seam"></i>';
                imgEl.replaceWith(div);
            }

            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
            bootstrap.Toast.getOrCreateInstance(document.getElementById('saveToast')).show();
        }
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>{{ __('common.save') }}';
    }
}
</script>
@endpush

<style>
.img-upload-zone {
    border:2px dashed #e5e7eb; border-radius:12px; padding:1.5rem;
    text-align:center; cursor:pointer; transition:border-color .15s,background .15s;
    min-height:110px; display:flex; align-items:center; justify-content:center;
}
.img-upload-zone:hover { border-color:#FF6B35; background:#fff8f5; }
.img-preview { max-width:100%; max-height:160px; border-radius:8px; object-fit:contain; }
</style>
@endsection
