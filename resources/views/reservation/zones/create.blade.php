@extends('layouts.app')

@section('title', __('reservation.zone_add'))
@section('page-title', __('reservation.zone_add'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-pin-map me-1" style="color:var(--accent)"></i>{{ __('reservation.zone_add') }}
            </div>
            <div class="sm-card-body">
                <p class="text-muted small mb-3">{{ __('reservation.zones_batch_hint') }}</p>
                <form action="{{ route('reservation.zones.store') }}" method="POST" id="zoneForm">
                    @csrf
                    <div id="zoneRows">
                        <div class="zone-row mb-2 d-flex gap-2 align-items-center">
                            <input type="text" name="names[]" class="form-control @error('names.0') is-invalid @enderror"
                                   placeholder="{{ __('reservation.zone_name_placeholder') }}" value="{{ old('names.0') }}">
                            <button type="button" class="btn btn-outline-secondary btn-sm zone-remove" style="visibility:hidden" title="{{ __('reservation.remove_row') }}"><i class="bi bi-dash-lg"></i></button>
                        </div>
                        @if(is_array(old('names')))
                            @foreach(old('names') as $idx => $oldName)
                                @if($idx > 0)
                                <div class="zone-row mb-2 d-flex gap-2 align-items-center">
                                    <input type="text" name="names[]" class="form-control" placeholder="{{ __('reservation.zone_name_placeholder') }}" value="{{ $oldName }}">
                                    <button type="button" class="btn btn-outline-danger btn-sm zone-remove"><i class="bi bi-dash-lg"></i></button>
                                </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                    @error('names')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" id="zoneAddMore" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>{{ __('reservation.add_another_zone') }}
                        </button>
                    </div>
                    <div class="form-text mt-1">{{ __('reservation.zone_name_hint') }}</div>
                    <hr class="my-4">
                    <div class="d-flex gap-2">
                        <a href="{{ route('reservation.zones.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('support.back') }}</a>
                        <button type="submit" class="btn btn-accent btn-sm">
                            <i class="bi bi-check-lg me-1"></i>{{ __('support.send') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
(function() {
    var container = document.getElementById('zoneRows');
    var addBtn = document.getElementById('zoneAddMore');
    var placeholder = {{ json_encode(__('reservation.zone_name_placeholder')) }};

    function updateRemoveVisibility() {
        var rows = container.querySelectorAll('.zone-row');
        rows.forEach(function(r, i) {
            var btn = r.querySelector('.zone-remove');
            if (btn) btn.style.visibility = rows.length > 1 ? 'visible' : 'hidden';
        });
    }

    addBtn.addEventListener('click', function() {
        var div = document.createElement('div');
        div.className = 'zone-row mb-2 d-flex gap-2 align-items-center';
        div.innerHTML = '<input type="text" name="names[]" class="form-control" placeholder="' + placeholder + '">' +
            '<button type="button" class="btn btn-outline-danger btn-sm zone-remove"><i class="bi bi-dash-lg"></i></button>';
        container.appendChild(div);
        div.querySelector('input').focus();
        updateRemoveVisibility();
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.zone-remove')) {
            e.target.closest('.zone-row').remove();
            updateRemoveVisibility();
        }
    });

    updateRemoveVisibility();
})();
</script>
@endpush
@endsection
