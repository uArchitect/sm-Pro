@extends('layouts.app')

@section('title', __('reservation.table_add'))
@section('page-title', __('reservation.table_add'))

@push('styles')
<style>
    .bulk-gen-card {
        background: rgba(79,70,229,.04);
        border: 1.5px solid rgba(79,70,229,.15);
        border-radius: 12px;
        padding: 1rem 1.25rem;
    }
    .bulk-gen-card .form-label { font-size:.8rem; font-weight:600; margin-bottom:.25rem; }
    .bulk-gen-card .form-control, .bulk-gen-card .form-select { font-size:.84rem; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-table me-1" style="color:var(--accent)"></i>{{ __('reservation.table_add') }}
            </div>
            <div class="sm-card-body">

                {{-- Bulk generate section --}}
                <div class="bulk-gen-card mb-4">
                    <div class="fw-semibold mb-2" style="font-size:.88rem"><i class="bi bi-lightning-fill text-warning me-1"></i>{{ __('reservation.bulk_generate') }}</div>
                    <div class="row g-2 align-items-end">
                        <div class="col-sm-4">
                            <label class="form-label">{{ __('reservation.zone_name') }}</label>
                            <select id="bulkZone" class="form-select">
                                @foreach($zones as $z)
                                <option value="{{ $z->id }}" {{ (request('zone') == $z->id || old('zone_id') == $z->id) ? 'selected' : '' }}>{{ $z->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label">{{ __('reservation.bulk_count') }}</label>
                            <input type="number" id="bulkCount" class="form-control" value="5" min="1" max="50">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">{{ __('reservation.bulk_prefix') }}</label>
                            <input type="text" id="bulkPrefix" class="form-control" value="{{ __('reservation.table_default_prefix') }}" placeholder="{{ __('reservation.table_default_prefix') }}">
                        </div>
                        <div class="col-sm-3">
                            <button type="button" id="bulkGenBtn" class="btn btn-accent btn-sm w-100">
                                <i class="bi bi-plus-circle me-1"></i>{{ __('reservation.generate') }}
                            </button>
                        </div>
                    </div>
                    <div class="form-text mt-1">{{ __('reservation.bulk_hint') }}</div>
                </div>

                <form action="{{ route('reservation.tables.store') }}" method="POST" id="tableForm">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('reservation.zone_name') }} *</label>
                            <select name="zone_id" id="zoneSelect" class="form-select @error('zone_id') is-invalid @enderror" required>
                                <option value="">{{ __('reservation.select_zone') }}</option>
                                @foreach($zones as $z)
                                <option value="{{ $z->id }}" {{ (request('zone') == $z->id || old('zone_id') == $z->id) ? 'selected' : '' }}>{{ $z->name }}</option>
                                @endforeach
                            </select>
                            @error('zone_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('reservation.capacity') }}</label>
                            <input type="number" name="capacity" id="capacityInput" class="form-control @error('capacity') is-invalid @enderror"
                                   value="{{ old('capacity', 2) }}" min="1" max="99">
                            @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">{{ __('reservation.capacity_applies_all') }}</div>
                        </div>
                    </div>
                    <label class="form-label">{{ __('reservation.table_name') }} *</label>
                    <div id="tableRows">
                        <div class="table-row mb-2 d-flex gap-2 align-items-center">
                            <span class="text-muted me-1" style="font-size:.75rem;min-width:20px;text-align:center">1</span>
                            <input type="text" name="names[]" class="form-control @error('names.0') is-invalid @enderror"
                                   placeholder="{{ __('reservation.table_name_placeholder') }}" value="{{ old('names.0') }}">
                            <button type="button" class="btn btn-outline-secondary btn-sm table-remove" style="visibility:hidden" title="{{ __('reservation.remove_row') }}"><i class="bi bi-dash-lg"></i></button>
                        </div>
                        @if(is_array(old('names')))
                            @foreach(old('names') as $idx => $oldName)
                                @if($idx > 0)
                                <div class="table-row mb-2 d-flex gap-2 align-items-center">
                                    <span class="text-muted me-1" style="font-size:.75rem;min-width:20px;text-align:center">{{ $idx + 1 }}</span>
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
                        <button type="button" id="tableClearAll" class="btn btn-outline-danger btn-sm" style="display:none">
                            <i class="bi bi-x-lg me-1"></i>{{ __('reservation.clear_all') }}
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
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('tableRows');
    var addBtn = document.getElementById('tableAddMore');
    var clearBtn = document.getElementById('tableClearAll');
    var bulkGenBtn = document.getElementById('bulkGenBtn');
    var placeholder = @json(__('reservation.table_name_placeholder'));

    function reIndex() {
        var rows = container.querySelectorAll('.table-row');
        rows.forEach(function(r, i) {
            var num = r.querySelector('span');
            if (num) num.textContent = i + 1;
            var btn = r.querySelector('.table-remove');
            if (btn) btn.style.visibility = rows.length > 1 ? 'visible' : 'hidden';
        });
        clearBtn.style.display = rows.length > 3 ? '' : 'none';
    }

    function addRow(value) {
        var div = document.createElement('div');
        div.className = 'table-row mb-2 d-flex gap-2 align-items-center';
        div.innerHTML = '<span class="text-muted me-1" style="font-size:.75rem;min-width:20px;text-align:center"></span>' +
            '<input type="text" name="names[]" class="form-control" placeholder="' + placeholder + '">' +
            '<button type="button" class="btn btn-outline-danger btn-sm table-remove"><i class="bi bi-dash-lg"></i></button>';
        if (value) div.querySelector('input').value = value;
        container.appendChild(div);
        reIndex();
        return div.querySelector('input');
    }

    addBtn.addEventListener('click', function() {
        addRow().focus();
    });

    clearBtn.addEventListener('click', function() {
        var rows = container.querySelectorAll('.table-row');
        for (var i = rows.length - 1; i > 0; i--) rows[i].remove();
        container.querySelector('.table-row input').value = '';
        reIndex();
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.table-remove')) {
            e.target.closest('.table-row').remove();
            reIndex();
        }
    });

    bulkGenBtn.addEventListener('click', function() {
        var count = parseInt(document.getElementById('bulkCount').value) || 5;
        var prefix = document.getElementById('bulkPrefix').value.trim() || @json(__('reservation.table_default_prefix'));
        var bulkZoneId = document.getElementById('bulkZone').value;

        if (count < 1) count = 1;
        if (count > 50) count = 50;

        document.getElementById('zoneSelect').value = bulkZoneId;

        var rows = container.querySelectorAll('.table-row');
        var firstInput = rows[0].querySelector('input');
        var startEmpty = rows.length === 1 && firstInput.value.trim() === '';

        var existing = 0;
        if (!startEmpty) {
            existing = rows.length;
        }

        for (var i = 0; i < count; i++) {
            var name = prefix + ' ' + (existing + i + 1);
            if (startEmpty && i === 0) {
                firstInput.value = name;
                startEmpty = false;
                existing = 1;
                continue;
            }
            addRow(name);
        }
        reIndex();
    });

    document.getElementById('bulkZone').addEventListener('change', function() {
        document.getElementById('zoneSelect').value = this.value;
    });
    document.getElementById('zoneSelect').addEventListener('change', function() {
        document.getElementById('bulkZone').value = this.value;
    });

    reIndex();
});
</script>
@endpush
@endsection
