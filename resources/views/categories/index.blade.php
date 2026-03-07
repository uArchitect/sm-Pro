@extends('layouts.app')

@section('title', __('categories.title'))
@section('page-title', __('categories.title'))

@section('content')
<style>
.drag-handle { cursor:grab; color:#d1d5db; font-size:1rem; padding:0 6px; }
.drag-handle:hover { color:#FF6B35; }
.sortable-ghost { opacity:.4; background:#fff8f5; }
.cat-thumb { width:40px; height:40px; border-radius:8px; object-fit:cover; background:#f3f4f6; cursor:pointer; transition:all .15s; }
.cat-thumb:hover { opacity:.7; box-shadow:0 0 0 2px rgba(255,107,53,.4); }
.cat-thumb-empty { width:40px; height:40px; border-radius:8px; background:#f3f4f6; display:flex; align-items:center; justify-content:center; color:#d1d5db; font-size:1rem; cursor:pointer; transition:all .15s; }
.cat-thumb-empty:hover { background:#fff0eb; color:#FF6B35; border:1px dashed rgba(255,107,53,.4); }
.sub-badge { font-size:.7rem; background:rgba(99,102,241,.1); color:#6366f1; border-radius:5px; padding:.2rem .5rem; font-weight:600; }
.parent-row td { background:#fff; }
.child-row td { background:#fafbff; border-left:3px solid #e0e7ff; padding-left:1.5rem !important; }
.child-row .drag-handle { visibility:hidden; }
.expand-btn { background:none; border:none; padding:0 4px; cursor:pointer; color:#9ca3af; transition:transform .2s; }
.expand-btn.collapsed { transform:rotate(-90deg); }
.inline-save-toast { position:fixed; bottom:1.5rem; right:1.5rem; z-index:9999; }
.ie-name { cursor:pointer; border-radius:4px; padding:2px 6px; margin:-2px -6px; transition:background .15s; display:inline; }
.ie-name:hover { background:rgba(255,107,53,.06); }
.ie-input { border:1.5px solid var(--accent); border-radius:6px; padding:4px 8px; font-size:inherit; font-family:inherit; font-weight:inherit; width:100%; outline:none; background:#fff; box-shadow:0 0 0 3px rgba(255,107,53,.12); }
.img-loading { opacity:.3; pointer-events:none; }
.sm-search { border:1.5px solid #e5e7eb; border-radius:9px; padding:.4rem .75rem .4rem 2rem; font-size:.82rem; font-family:'Inter',sans-serif; transition:border-color .15s,box-shadow .15s; width:200px; background:#fff; }
.sm-search:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(255,107,53,.12); outline:none; }
.search-wrap { position:relative; }
.search-wrap i { position:absolute; left:.7rem; top:50%; transform:translateY(-50%); color:#98a2b3; font-size:.8rem; pointer-events:none; }
.no-match-row td { text-align:center; padding:2rem; color:#98a2b3; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted small">
        <strong>{{ $parents->count() }}</strong> {{ __('categories.parent_count') }}
        @if($children->flatten()->count()) · <strong>{{ $children->flatten()->count() }}</strong> {{ __('categories.sub_count') }} @endif
        <span class="ms-2 opacity-75">· {{ __('categories.reorder_hint') }}</span>
    </span>
    <div class="d-flex gap-2 align-items-center">
        <div class="search-wrap">
            <i class="bi bi-search"></i>
            <input type="text" id="catSearch" class="sm-search" placeholder="{{ __('categories.title') }}...">
        </div>
        <a href="{{ route('categories.create') }}" class="btn btn-accent btn-sm">
            <i class="bi bi-plus-circle me-1"></i>{{ __('categories.add') }}
        </a>
    </div>
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
                    <th class="text-end pe-4" style="width:80px"></th>
                </tr>
            </thead>
            <tbody id="sortableCats">
                @forelse($parents as $parent)
                @php $subs = $children->get($parent->id, collect()); @endphp
                <tr class="parent-row" data-id="{{ $parent->id }}">
                    <td class="ps-3"><i class="bi bi-grip-vertical drag-handle"></i></td>
                    <td>
                        @if($parent->image)
                            <img src="{{ asset('storage/'.$parent->image) }}" class="cat-thumb"
                                 data-img="{{ $parent->id }}" onclick="triggerImgUpload({{ $parent->id }})"
                                 title="{{ __('common.photo') }}">
                        @else
                            <div class="cat-thumb-empty" data-img="{{ $parent->id }}"
                                 onclick="triggerImgUpload({{ $parent->id }})" title="{{ __('common.photo') }}">
                                <i class="bi bi-camera"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <span class="ie-name" style="font-weight:600" onclick="startEdit(this)"
                              data-field="name" data-id="{{ $parent->id }}" data-value="{{ e($parent->name) }}">{{ $parent->name }}</span>
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
                        <form method="POST" action="{{ route('categories.destroy', $parent->id) }}"
                              onsubmit="return confirm({{ json_encode(__('categories.delete_confirm_subs', ['name' => $parent->name])) }})">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @foreach($subs as $sub)
                <tr class="child-row sub-of-{{ $parent->id }}" data-id="{{ $sub->id }}">
                    <td class="ps-3"><i class="bi bi-grip-vertical drag-handle" style="visibility:hidden"></i></td>
                    <td>
                        @if($sub->image)
                            <img src="{{ asset('storage/'.$sub->image) }}" class="cat-thumb"
                                 data-img="{{ $sub->id }}" onclick="triggerImgUpload({{ $sub->id }})"
                                 title="{{ __('common.photo') }}">
                        @else
                            <div class="cat-thumb-empty" data-img="{{ $sub->id }}"
                                 onclick="triggerImgUpload({{ $sub->id }})" title="{{ __('common.photo') }}">
                                <i class="bi bi-camera"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <i class="bi bi-arrow-return-right text-muted me-1" style="font-size:.75rem"></i>
                        <span class="ie-name" onclick="startEdit(this)"
                              data-field="name" data-id="{{ $sub->id }}" data-value="{{ e($sub->name) }}">{{ $sub->name }}</span>
                    </td>
                    <td><span class="text-muted small">{{ __('categories.sub') }}</span></td>
                    <td class="text-end pe-4">
                        <form method="POST" action="{{ route('categories.destroy', $sub->id) }}"
                              onsubmit="return confirm({{ json_encode(__('categories.delete_confirm', ['name' => $sub->name])) }})">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
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

<input type="file" id="catImgUploadInput" accept="image/*" class="d-none">

<div class="inline-save-toast">
    <div id="saveToast" class="toast align-items-center text-bg-success border-0" role="alert" data-bs-autohide="true" data-bs-delay="2000">
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
let uploadingForId = null;

$('#catSearch').on('keyup', function() {
    const q = this.value.toLowerCase().trim();
    const $noMatch = $('#catTable .no-match-row');
    $noMatch.remove();

    if (!q) {
        $('#sortableCats tr.parent-row, #sortableCats tr.child-row').show();
        return;
    }

    $('#sortableCats tr.parent-row, #sortableCats tr.child-row').hide();
    let found = 0;
    $('#sortableCats tr.parent-row, #sortableCats tr.child-row').each(function() {
        const name = $(this).find('.ie-name').text().toLowerCase();
        if (name.includes(q)) {
            $(this).show();
            found++;
            if ($(this).hasClass('child-row')) {
                const m = this.className.match(/sub-of-(\d+)/);
                if (m) $(`tr.parent-row[data-id="${m[1]}"]`).show();
            }
            if ($(this).hasClass('parent-row')) {
                $(`.sub-of-${$(this).data('id')}`).show();
            }
        }
    });
    if (!found) {
        $('#sortableCats').append('<tr class="no-match-row"><td colspan="5" class="text-center text-muted py-4"><i class="bi bi-search fs-3 d-block mb-2 opacity-25"></i>{{ __("categories.no_categories") }}</td></tr>');
    }
});

function toggleSubs(parentId, btn) {
    const rows = document.querySelectorAll('.sub-of-' + parentId);
    const hidden = rows[0]?.style.display === 'none';
    rows.forEach(r => r.style.display = hidden ? '' : 'none');
    btn.classList.toggle('collapsed', !hidden);
}

Sortable.create(document.getElementById('sortableCats'), {
    handle: '.drag-handle',
    animation: 150,
    ghostClass: 'sortable-ghost',
    filter: '.child-row',
    onEnd() {
        const order = [...document.querySelectorAll('#sortableCats .parent-row')].map(r => r.dataset.id);
        fetch('{{ route("categories.reorder") }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
            body: JSON.stringify({order})
        }).then(r => r.json()).then(data => { if (data.success) showToast(); });
    }
});

function startEdit(el) {
    if (el.querySelector('input')) return;
    const id     = el.dataset.id;
    const rawVal = el.dataset.value;
    const origHTML = el.innerHTML;

    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'ie-input';
    input.value = rawVal;
    input.style.maxWidth = '220px';

    el.innerHTML = '';
    el.appendChild(input);
    input.focus();
    input.select();

    let done = false;
    const finish = async (save) => {
        if (done) return;
        done = true;
        const nv = input.value.trim();
        if (!save || !nv || nv === rawVal) { el.innerHTML = origHTML; return; }
        el.innerHTML = '<span class="spinner-border spinner-border-sm text-muted"></span>';
        const fd = new FormData();
        fd.append('_token', CSRF);
        fd.append('name', nv);
        try {
            const res = await fetch(`/categories/${id}/inline-update`, {method:'POST', body:fd});
            const data = await res.json();
            if (data.success) {
                el.dataset.value = data.name;
                el.textContent = data.name;
                showToast();
            } else { el.innerHTML = origHTML; }
        } catch(e) { el.innerHTML = origHTML; }
    };

    input.addEventListener('blur', () => finish(true));
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); input.blur(); }
        if (e.key === 'Escape') { e.preventDefault(); finish(false); }
    });
}

function triggerImgUpload(catId) {
    uploadingForId = catId;
    const inp = document.getElementById('catImgUploadInput');
    inp.value = '';
    inp.click();
}

document.getElementById('catImgUploadInput').addEventListener('change', async function() {
    if (!this.files[0] || !uploadingForId) return;
    const id = uploadingForId;
    const imgEl = document.querySelector(`[data-img="${id}"]`);
    imgEl.classList.add('img-loading');

    const fd = new FormData();
    fd.append('_token', CSRF);
    fd.append('image', this.files[0]);
    try {
        const res = await fetch(`/categories/${id}/inline-update`, {method:'POST', body:fd});
        const data = await res.json();
        if (data.success && data.image_url) {
            if (imgEl.tagName === 'IMG') {
                imgEl.src = data.image_url + '?t=' + Date.now();
            } else {
                const img = document.createElement('img');
                img.src = data.image_url;
                img.className = 'cat-thumb';
                img.dataset.img = id;
                img.onclick = () => triggerImgUpload(id);
                img.title = '{{ __("common.photo") }}';
                imgEl.replaceWith(img);
            }
            showToast();
        }
    } finally { imgEl.classList.remove('img-loading'); }
});

function showToast() {
    bootstrap.Toast.getOrCreateInstance(document.getElementById('saveToast')).show();
}
</script>
@endpush
@endsection
