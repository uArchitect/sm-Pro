@extends('layouts.app')

@section('title', __('sliders.title'))
@section('page-title', __('sliders.title'))
@section('page-help', __('sliders.page_help'))

@section('content')
<style>
.slider-card {
    position: relative; border-radius: 12px; overflow: hidden;
    border: 1.5px solid #e5e7eb; transition: box-shadow .15s, transform .15s;
    background: #fff; cursor: grab;
}
.slider-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,.08); transform: translateY(-2px); }
.slider-card img {
    width: 100%; height: 160px; object-fit: cover; display: block;
}
.slider-card-body { padding: .75rem .85rem; }
.slider-card-title { font-size: .82rem; font-weight: 700; color: #1f2937; }
.slider-card-desc { font-size: .75rem; color: #6b7280; margin-top: .2rem; }
.slider-del {
    position: absolute; top: .5rem; right: .5rem;
    background: rgba(255,255,255,.9); border: none; border-radius: 50%;
    width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;
    color: #dc2626; font-size: .8rem; cursor: pointer; transition: all .15s;
    box-shadow: 0 1px 4px rgba(0,0,0,.1);
}
.slider-del:hover { background: #fee2e2; }
.slider-order { font-size: .65rem; color: #9ca3af; font-weight: 600; }
.upload-zone {
    border: 2px dashed #e5e7eb; border-radius: 12px; padding: 2rem;
    text-align: center; transition: border-color .15s; cursor: pointer;
}
.upload-zone:hover { border-color: var(--accent); }
.upload-zone i { font-size: 2rem; color: #d1d5db; }
.dragging { opacity: .5; }
</style>

@if(session('success'))
<div class="alert alert-success py-2 px-3" style="font-size:.84rem;border-radius:9px">{{ session('success') }}</div>
@endif

<div class="row g-4">
    {{-- Upload Form --}}
    <div class="col-lg-4">
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-cloud-arrow-up me-1" style="color:var(--accent)"></i>{{ __('sliders.add') }}
            </div>
            <div class="sm-card-body">
                <form action="{{ route('sliders.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('sliders.image') }} *</label>
                        <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,image/jpeg,image/png,image/gif,image/webp,image/svg+xml" class="form-control @error('image') is-invalid @enderror" required>
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('sliders.title_field') }}</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('sliders.description') }}</label>
                        <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-accent btn-sm w-100">
                        <i class="bi bi-plus-lg me-1"></i>{{ __('sliders.add') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Slider List --}}
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted" style="font-size:.82rem">
                {{ __('sliders.total', ['count' => $sliders->count()]) }}
            </span>
            @if($sliders->count() > 1)
            <small class="text-muted" style="font-size:.72rem"><i class="bi bi-arrows-move me-1"></i>{{ __('sliders.reorder_hint') }}</small>
            @endif
        </div>

        @if($sliders->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-images" style="font-size:2.5rem;opacity:.3"></i>
            <div class="mt-2" style="font-size:.85rem">{{ __('sliders.no_sliders') }}</div>
        </div>
        @else
        <div class="row g-3" id="sliderGrid">
            @foreach($sliders as $s)
            <div class="col-sm-6" data-id="{{ $s->id }}" draggable="true">
                <div class="slider-card">
                    <img src="{{ asset('uploads/' . $s->image) }}" alt="{{ $s->title }}">
                    <form method="POST" action="{{ route('sliders.destroy', $s->id) }}"
                          onsubmit="return confirm('{{ __('sliders.delete_confirm') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="slider-del"><i class="bi bi-trash"></i></button>
                    </form>
                    <div class="slider-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="slider-card-title">{{ $s->title ?: '-' }}</div>
                            <span class="slider-order">#{{ $s->sort_order }}</span>
                        </div>
                        @if($s->description)
                        <div class="slider-card-desc">{{ Str::limit($s->description, 80) }}</div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const grid = document.getElementById('sliderGrid');
    if (!grid) return;

    let dragEl = null;

    grid.querySelectorAll('[draggable]').forEach(el => {
        el.addEventListener('dragstart', function(e) {
            dragEl = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });
        el.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            dragEl = null;
            saveOrder();
        });
        el.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            if (dragEl && dragEl !== this) {
                const rect = this.getBoundingClientRect();
                const mid = rect.top + rect.height / 2;
                if (e.clientY < mid) {
                    grid.insertBefore(dragEl, this);
                } else {
                    grid.insertBefore(dragEl, this.nextSibling);
                }
            }
        });
    });

    function saveOrder() {
        const ids = [...grid.querySelectorAll('[data-id]')].map(el => el.dataset.id);
        fetch('{{ route("sliders.reorder") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order: ids })
        });
    }
});
</script>
@endsection
