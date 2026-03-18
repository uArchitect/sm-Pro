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
                            <span class="text-muted me-1" style="font-size:.75rem;min-width:20px;text-align:center">1</span>
                            <input type="text" name="names[]" class="form-control @error('names.0') is-invalid @enderror"
                                   placeholder="{{ __('reservation.zone_name_placeholder') }}" value="{{ old('names.0') }}">
                            <button type="button" class="btn btn-outline-secondary btn-sm zone-remove" style="visibility:hidden" title="{{ __('reservation.remove_row') }}"><i class="bi bi-dash-lg"></i></button>
                        </div>
                        @if(is_array(old('names')))
                            @foreach(old('names') as $idx => $oldName)
                                @if($idx > 0)
                                <div class="zone-row mb-2 d-flex gap-2 align-items-center">
                                    <span class="text-muted me-1" style="font-size:.75rem;min-width:20px;text-align:center">{{ $idx + 1 }}</span>
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
                        <button type="button" id="zoneAddBulk" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>{{ __('reservation.add_5_zones') }}
                        </button>
                    </div>
                    <div class="form-text mt-1">{{ __('reservation.zone_name_hint') }}</div>

                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="redirectToTables" name="redirect_to_tables" value="1" checked>
                        <label class="form-check-label small" for="redirectToTables">{{ __('reservation.redirect_to_tables_after') }}</label>
                    </div>

                    <hr class="my-4">
                    <div class="d-flex gap-2">
                        <a href="{{ route('reservation.zones.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('common.back') }}</a>
                        <button type="submit" class="btn btn-accent btn-sm">
                            <i class="bi bi-check-lg me-1"></i>{{ __('common.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('zoneRows');
    var addBtn = document.getElementById('zoneAddMore');
    var bulkBtn = document.getElementById('zoneAddBulk');
    var placeholder = @json(__('reservation.zone_name_placeholder'));

    function reIndex() {
        var rows = container.querySelectorAll('.zone-row');
        rows.forEach(function(r, i) {
            var num = r.querySelector('span');
            if (num) num.textContent = i + 1;
            var btn = r.querySelector('.zone-remove');
            if (btn) btn.style.visibility = rows.length > 1 ? 'visible' : 'hidden';
        });
    }

    function addRow() {
        var div = document.createElement('div');
        div.className = 'zone-row mb-2 d-flex gap-2 align-items-center';
        div.innerHTML = '<span class="text-muted me-1" style="font-size:.75rem;min-width:20px;text-align:center"></span>' +
            '<input type="text" name="names[]" class="form-control" placeholder="' + placeholder + '">' +
            '<button type="button" class="btn btn-outline-danger btn-sm zone-remove"><i class="bi bi-dash-lg"></i></button>';
        container.appendChild(div);
        reIndex();
        return div.querySelector('input');
    }

    addBtn.addEventListener('click', function() {
        addRow().focus();
    });

    bulkBtn.addEventListener('click', function() {
        var lastInput;
        for (var i = 0; i < 5; i++) {
            lastInput = addRow();
        }
        if (lastInput) container.querySelectorAll('.zone-row input')[container.querySelectorAll('.zone-row').length - 5].focus();
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.zone-remove')) {
            e.target.closest('.zone-row').remove();
            reIndex();
        }
    });

    reIndex();
});
</script>
@endpush
@endsection
