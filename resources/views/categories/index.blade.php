@extends('layouts.app')

@section('title', __('categories.title'))
@section('page-title', __('categories.title'))
@section('content')
<style>
.drag-handle { cursor: grab; color: #d1d5db; font-size: 1rem; padding: 0 6px; }
.drag-handle:hover { color: #FF6B35; }
.sortable-ghost { opacity: .4; background: #fff8f5; }
.cat-thumb { width:40px; height:40px; border-radius:8px; object-fit:cover; background:#f3f4f6; }
.cat-thumb-empty { width:40px; height:40px; border-radius:8px; background:#f3f4f6; display:flex; align-items:center; justify-content:center; color:#d1d5db; font-size:1rem; }
.sub-badge { font-size:.7rem; background:rgba(99,102,241,.1); color:#6366f1; border-radius:5px; padding:.2rem .5rem; font-weight:600; }
.parent-row td { background:#fff; }
.child-row td { background:#fafbff; border-left:3px solid #e0e7ff; padding-left:1.5rem !important; }
.child-row .drag-handle { visibility:hidden; } /* children not reorderable globally */
.expand-btn { background:none; border:none; padding:0 4px; cursor:pointer; color:#9ca3af; transition:transform .2s; }
.expand-btn.collapsed { transform:rotate(-90deg); }
.inline-save-toast { position:fixed; bottom:1.5rem; right:1.5rem; z-index:9999; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted small">
        <strong>{{ $parents->count() }}</strong> {{ __('categories.parent_count') }}
        @if($children->flatten()->count()) · <strong>{{ $children->flatten()->count() }}</strong> {{ __('categories.sub_count') }} @endif
    </span>
    <a href="{{ route('categories.create') }}" class="btn btn-accent btn-sm">
        <i class="bi bi-plus-circle me-1"></i>{{ __('categories.add') }}
    </a>
</div>

<div class="sm-card">
    <div class="table-responsive">
        <table class="table sm-table align-middle mb-0" id="catTable">
            <thead>
                <tr>
                    <th style="width:36px"></th>
                    <th style="width:56px"></th>
                    <th>{{ __('categories.name') }}</th>
                    <th style="width:120px">{{ __('categories.sub_category') }}</th>
                    <th class="text-end pe-4" style="width:140px">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody id="sortableCats">
                @forelse($parents as $parent)
                @php $subs = $children->get($parent->id, collect()); @endphp
                {{-- Parent row --}}
                <tr class="parent-row" data-id="{{ $parent->id }}">
                    <td class="ps-3"><i class="bi bi-grip-vertical drag-handle"></i></td>
                    <td>
                        @if($parent->image)
                            <img src="{{ asset('storage/'.$parent->image) }}" class="cat-thumb" data-img="{{ $parent->id }}">
                        @else
                            <div class="cat-thumb-empty" data-img="{{ $parent->id }}"><i class="bi bi-grid"></i></div>
                        @endif
                    </td>
                    <td>
                        <span class="fw-600 cat-name" data-name="{{ $parent->id }}" style="font-weight:600">{{ $parent->name }}</span>
                        @if($subs->count())
                        <button class="expand-btn ms-1" onclick="toggleSubs({{ $parent->id }}, this)">
                            <i class="bi bi-chevron-down" style="font-size:.75rem"></i>
                        </button>
                        @endif
                    </td>
                    <td>
                        @if($subs->count())
                            <span class="sub-badge">{{ $subs->count() }} {{ __('categories.sub') }}</span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex gap-1 justify-content-end">
                            <button class="btn btn-sm btn-outline-secondary" onclick="openEditModal({{ $parent->id }}, '{{ addslashes($parent->name) }}', {{ $parent->image ? "'".asset('storage/'.$parent->image)."'" : 'null' }})" title="{{ __('common.edit') }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('categories.destroy', $parent->id) }}"
                                  onsubmit="return confirm({{ json_encode(__('categories.delete_confirm_subs', ['name' => $parent->name])) }})">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('common.delete') }}"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                {{-- Sub-rows --}}
                @foreach($subs as $sub)
                <tr class="child-row sub-of-{{ $parent->id }}" data-id="{{ $sub->id }}">
                    <td class="ps-3"><i class="bi bi-grip-vertical drag-handle" style="visibility:hidden"></i></td>
                    <td>
                        @if($sub->image)
                            <img src="{{ asset('storage/'.$sub->image) }}" class="cat-thumb" data-img="{{ $sub->id }}">
                        @else
                            <div class="cat-thumb-empty" data-img="{{ $sub->id }}"><i class="bi bi-tag"></i></div>
                        @endif
                    </td>
                    <td>
                        <i class="bi bi-arrow-return-right text-muted me-1" style="font-size:.75rem"></i>
                        <span class="cat-name" data-name="{{ $sub->id }}">{{ $sub->name }}</span>
                    </td>
                    <td><span class="text-muted small">{{ __('categories.sub') }}</span></td>
                    <td class="text-end pe-4">
                        <div class="d-flex gap-1 justify-content-end">
                            <button class="btn btn-sm btn-outline-secondary" onclick="openEditModal({{ $sub->id }}, '{{ addslashes($sub->name) }}', {{ $sub->image ? "'".asset('storage/'.$sub->image)."'" : 'null' }})" title="{{ __('common.edit') }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('categories.destroy', $sub->id) }}"
                                  onsubmit="return confirm({{ json_encode(__('categories.delete_confirm', ['name' => $sub->name])) }})">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="bi bi-grid fs-1 d-block mb-2 opacity-25"></i>
                        {{ __('categories.no_categories') }}
                        <br><a href="{{ route('categories.create') }}" class="btn btn-accent btn-sm mt-2">{{ __('categories.add_first') }}</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Inline Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">
            <div class="modal-header" style="background:#0f1923;border:none;padding:1.1rem 1.4rem">
                <h6 class="modal-title text-white fw-700 mb-0" style="font-weight:700">
                    <i class="bi bi-pencil-square me-2" style="color:#FF6B35"></i>{{ __('categories.edit_category') }}
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="editModalAlert" class="alert alert-danger d-none py-2 small mb-3"></div>

                {{-- Image upload area --}}
                <div class="mb-4">
                    <label class="form-label">{{ __('common.photo') }}</label>
                    <div class="img-upload-zone" id="imgZone" onclick="document.getElementById('editImgInput').click()">
                        <img id="imgPreview" src="" class="img-preview d-none">
                        <div id="imgPlaceholder">
                            <i class="bi bi-cloud-upload" style="font-size:1.6rem;color:#d1d5db"></i>
                            <div class="mt-1" style="font-size:.78rem;color:#9ca3af">{{ __('common.click_or_drop') }}</div>
                            <div style="font-size:.72rem;color:#d1d5db">{{ __('common.max_file_size') }}</div>
                        </div>
                    </div>
                    <input type="file" id="editImgInput" accept="image/*" class="d-none">
                    <button type="button" class="btn btn-link btn-sm text-danger p-0 mt-1 d-none" id="removeImgBtn" onclick="removeImg()">
                        <i class="bi bi-x-circle me-1"></i>{{ __('common.remove_photo') }}
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('categories.name') }}</label>
                    <input type="text" id="editName" class="form-control" placeholder="{{ __('products.name_placeholder') }}" maxlength="255">
                </div>
            </div>
            <div class="modal-footer" style="border:none;padding:.75rem 1.4rem 1.2rem">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-accent btn-sm" id="saveEditBtn" onclick="saveEdit()">
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
let editingId = null;
let removingImg = false;

// ── Expand/collapse sub-rows ──
function toggleSubs(parentId, btn) {
    const rows = document.querySelectorAll('.sub-of-' + parentId);
    const hidden = rows[0]?.style.display === 'none';
    rows.forEach(r => r.style.display = hidden ? '' : 'none');
    btn.classList.toggle('collapsed', !hidden);
}

// ── Drag-drop sort ──
Sortable.create(document.getElementById('sortableCats'), {
    handle: '.drag-handle',
    animation: 150,
    ghostClass: 'sortable-ghost',
    filter: '.child-row',
    onEnd() {
        const order = [...document.querySelectorAll('#sortableCats .parent-row')]
            .map(r => r.dataset.id);
        fetch('{{ route("categories.reorder") }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
            body: JSON.stringify({order})
        });
    }
});

// ── Inline Edit Modal ──
function openEditModal(id, name, imgUrl) {
    editingId  = id;
    removingImg = false;
    document.getElementById('editName').value = name;
    document.getElementById('editImgInput').value = '';
    document.getElementById('editModalAlert').classList.add('d-none');

    const preview   = document.getElementById('imgPreview');
    const placeholder = document.getElementById('imgPlaceholder');
    const removeBtn = document.getElementById('removeImgBtn');

    if (imgUrl) {
        preview.src = imgUrl;
        preview.classList.remove('d-none');
        placeholder.classList.add('d-none');
        removeBtn.classList.remove('d-none');
    } else {
        preview.classList.add('d-none');
        placeholder.classList.remove('d-none');
        removeBtn.classList.add('d-none');
    }

    new bootstrap.Modal(document.getElementById('editModal')).show();
}

// Image preview in modal
document.getElementById('editImgInput').addEventListener('change', function() {
    if (!this.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('imgPreview').src = e.target.result;
        document.getElementById('imgPreview').classList.remove('d-none');
        document.getElementById('imgPlaceholder').classList.add('d-none');
        document.getElementById('removeImgBtn').classList.remove('d-none');
    };
    reader.readAsDataURL(this.files[0]);
});

// Drag-drop into upload zone
const zone = document.getElementById('imgZone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor='#FF6B35'; });
zone.addEventListener('dragleave', () => { zone.style.borderColor=''; });
zone.addEventListener('drop', e => {
    e.preventDefault(); zone.style.borderColor='';
    const dt = new DataTransfer();
    dt.items.add(e.dataTransfer.files[0]);
    document.getElementById('editImgInput').files = dt.files;
    document.getElementById('editImgInput').dispatchEvent(new Event('change'));
});

function removeImg() {
    removingImg = true;
    document.getElementById('imgPreview').classList.add('d-none');
    document.getElementById('imgPlaceholder').classList.remove('d-none');
    document.getElementById('removeImgBtn').classList.add('d-none');
    document.getElementById('editImgInput').value = '';
}

async function saveEdit() {
    const name = document.getElementById('editName').value.trim();
    if (!name) {
        const al = document.getElementById('editModalAlert');
        al.textContent = '{{ __('categories.name_required') }}';
        al.classList.remove('d-none');
        return;
    }

    const btn = document.getElementById('saveEditBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>{{ __('common.loading') }}';

    const fd = new FormData();
    fd.append('_token', CSRF);
    fd.append('name', name);
    if (removingImg) fd.append('remove_image', '1');
    const file = document.getElementById('editImgInput').files[0];
    if (file) fd.append('image', file);

    try {
        const res  = await fetch(`/categories/${editingId}/inline-update`, {method:'POST', body:fd});
        const data = await res.json();

        if (data.success) {
            // Update table row
            document.querySelector(`[data-name="${editingId}"]`).textContent = data.name;
            const imgEl = document.querySelector(`[data-img="${editingId}"]`);
            if (data.image_url) {
                if (imgEl.tagName === 'IMG') {
                    imgEl.src = data.image_url + '?t=' + Date.now();
                } else {
                    const img = document.createElement('img');
                    img.src = data.image_url;
                    img.className = 'cat-thumb';
                    img.dataset.img = editingId;
                    imgEl.replaceWith(img);
                }
            } else if (removingImg) {
                const div = document.createElement('div');
                div.className = 'cat-thumb-empty';
                div.dataset.img = editingId;
                div.innerHTML = '<i class="bi bi-grid"></i>';
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
    border: 2px dashed #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: border-color .15s, background .15s;
    position: relative;
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.img-upload-zone:hover { border-color: #FF6B35; background: #fff8f5; }
.img-preview { max-width: 100%; max-height: 180px; border-radius: 8px; object-fit: contain; }
</style>
@endsection
