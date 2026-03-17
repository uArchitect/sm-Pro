@extends('layouts.app')

@section('title', __('reservation.table_add'))
@section('page-title', __('reservation.table_add'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-table me-1" style="color:var(--accent)"></i>{{ __('reservation.table_add') }}
            </div>
            <div class="sm-card-body">
                <p class="text-muted small mb-3">{{ __('reservation.tables_batch_hint') }}</p>
                <form action="{{ route('reservation.tables.store') }}" method="POST" id="tableForm">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('reservation.zone_name') }} *</label>
                            <select name="zone_id" class="form-select @error('zone_id') is-invalid @enderror" required>
                                <option value="">{{ __('reservation.select_zone') }}</option>
                                @foreach($zones as $z)
                                <option value="{{ $z->id }}" {{ old('zone_id') == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                                @endforeach
                            </select>
                            @error('zone_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('reservation.capacity') }}</label>
                            <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror"
                                   value="{{ old('capacity', 2) }}" min="1" max="99">
                            @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">{{ __('reservation.capacity_applies_all') }}</div>
                        </div>
                    </div>
                    <label class="form-label">{{ __('reservation.table_name') }} *</label>
                    <div id="tableRows">
                        <div class="table-row mb-2 d-flex gap-2 align-items-center">
                            <input type="text" name="names[]" class="form-control @error('names.0') is-invalid @enderror"
                                   placeholder="{{ __('reservation.table_name_placeholder') }}" value="{{ old('names.0') }}">
                            <button type="button" class="btn btn-outline-secondary btn-sm table-remove" style="visibility:hidden" title="{{ __('reservation.remove_row') }}"><i class="bi bi-dash-lg"></i></button>
                        </div>
                        @if(is_array(old('names')))
                            @foreach(old('names') as $idx => $oldName)
                                @if($idx > 0)
                                <div class="table-row mb-2 d-flex gap-2 align-items-center">
                                    <input type="text" name="names[]" class="form-control" placeholder="{{ __('reservation.table_name_placeholder') }}" value="{{ $oldName }}">
                                    <button type="button" class="btn btn-outline-danger btn-sm table-remove"><i class="bi bi-dash-lg"></i></button>
                                </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                    @error('names')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" id="tableAddMore" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>{{ __('reservation.add_another_table') }}
                        </button>
                    </div>
                    <hr class="my-4">
                    <div class="d-flex gap-2">
                        <a href="{{ route('reservation.tables.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('common.back') }}</a>
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
(function() {
    var container = document.getElementById('tableRows');
    var addBtn = document.getElementById('tableAddMore');
    var placeholder = {{ json_encode(__('reservation.table_name_placeholder')) }};

    function updateRemoveVisibility() {
        var rows = container.querySelectorAll('.table-row');
        rows.forEach(function(r) {
            var btn = r.querySelector('.table-remove');
            if (btn) btn.style.visibility = rows.length > 1 ? 'visible' : 'hidden';
        });
    }

    addBtn.addEventListener('click', function() {
        var div = document.createElement('div');
        div.className = 'table-row mb-2 d-flex gap-2 align-items-center';
        div.innerHTML = '<input type="text" name="names[]" class="form-control" placeholder="' + placeholder + '">' +
            '<button type="button" class="btn btn-outline-danger btn-sm table-remove"><i class="bi bi-dash-lg"></i></button>';
        container.appendChild(div);
        div.querySelector('input').focus();
        updateRemoveVisibility();
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.table-remove')) {
            e.target.closest('.table-row').remove();
            updateRemoveVisibility();
        }
    });

    updateRemoveVisibility();
})();
</script>
@endpush
@endsection
