@extends('layouts.app')

@section('title', __('reservation.tables_title'))
@section('page-title', __('reservation.tables_title'))
@section('page-help', __('reservation.page_help_tables'))

@section('content')
<style>
.reservation-search { border:1.5px solid #e5e7eb; border-radius:9px; padding:.4rem .75rem .4rem 2rem; font-size:.82rem; width:220px; background:#fff; transition:border-color .15s, box-shadow .15s; }
.reservation-search:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(79,70,229,.12); outline:none; }
.reservation-search-wrap { position:relative; }
.reservation-search-wrap i { position:absolute; left:.7rem; top:50%; transform:translateY(-50%); color:#98a2b3; font-size:.8rem; pointer-events:none; }
.reservation-zone-filter { font-size:.82rem; padding:.35rem .65rem; border-radius:8px; border:1.5px solid #e5e7eb; min-width:160px; background:#fff; }
.reservation-zone-filter:focus { border-color:var(--accent); outline:none; }
</style>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="text-muted" style="font-size:.82rem">{{ __('reservation.tables_total', ['count' => $tables->count()]) }}</span>
        @if($tables->isNotEmpty())
        <div class="reservation-search-wrap">
            <i class="bi bi-search"></i>
            <input type="text" id="tablesSearch" class="reservation-search" placeholder="{{ __('reservation.search_tables') }}" autocomplete="off">
        </div>
        <select id="tablesZoneFilter" class="reservation-zone-filter" title="{{ __('reservation.filter_by_zone') }}">
            <option value="">{{ __('reservation.all_zones') }}</option>
            @foreach($zones as $z)
            <option value="{{ $z->name }}">{{ $z->name }}</option>
            @endforeach
        </select>
        @endif
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reservation.zones.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pin-map me-1"></i>{{ __('reservation.zones_title') }}
        </a>
        <a href="{{ route('reservation.tables.create') }}" class="btn btn-accent btn-sm">
            <i class="bi bi-plus-lg me-1"></i>{{ __('reservation.table_add') }}
        </a>
    </div>
</div>

@if($tables->isEmpty())
<div class="text-center py-5 text-muted">
    <i class="bi bi-table" style="font-size:2.5rem;opacity:.3"></i>
    <div class="mt-2" style="font-size:.85rem">{{ __('reservation.no_tables') }}</div>
    <p class="small mt-1 mb-3">{{ __('reservation.tables_hint') }}</p>
    <a href="{{ route('reservation.zones.index') }}" class="btn btn-outline-secondary btn-sm me-2">{{ __('reservation.zones_title') }}</a>
    <a href="{{ route('reservation.tables.create') }}" class="btn btn-accent btn-sm">{{ __('reservation.table_add_first') }}</a>
</div>
@else
<div class="sm-card">
    <div class="table-responsive">
        <table class="table sm-table align-middle mb-0" id="reservationTablesTable">
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th>{{ __('reservation.table_name') }}</th>
                    <th>{{ __('reservation.zone_name') }}</th>
                    <th style="width:100px">{{ __('reservation.capacity') }}</th>
                    <th class="text-end pe-4" style="width:120px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables as $t)
                <tr>
                    <td class="text-muted" style="font-size:.8rem">{{ $t->sort_order }}</td>
                    <td>
                        <span class="fw-semibold" style="color:var(--text-primary)">{{ $t->name }}</span>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $t->zone_name }}</span>
                    </td>
                    <td>
                        <span class="text-muted small"><i class="bi bi-people me-1"></i>{{ $t->capacity }} {{ __('reservation.persons') }}</span>
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('reservation.tables.edit', $t->id) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('common.edit') }}">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('reservation.tables.destroy', $t->id) }}" class="d-inline"
                              onsubmit="return confirm({{ json_encode(__('reservation.table_delete_confirm', ['name' => $t->name])) }})">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('common.delete') }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

@if($tables->isNotEmpty())
@push('scripts')
<script>
$(function() {
    var dt = $('#reservationTablesTable').DataTable({
        ordering: true,
        order: [[2, 'asc'], [1, 'asc']],
        paging: true,
        pageLength: 25,
        dom: 't<"d-flex justify-content-between align-items-center mt-3"ip>',
        language: {
            info: '_START_–_END_ / _TOTAL_',
            infoEmpty: '0 / 0',
            infoFiltered: '',
            zeroRecords: '<div class="text-center py-4 text-muted"><i class="bi bi-search fs-3 d-block mb-2 opacity-25"></i>{{ __("reservation.no_match_tables") }}</div>',
            paginate: { previous: '‹', next: '›' }
        },
        columnDefs: [
            { orderable: false, searchable: false, targets: 4 }
        ]
    });
    $('#tablesSearch').on('keyup', function() { dt.search(this.value).draw(); });
    $('#tablesZoneFilter').on('change', function() {
        var val = $(this).val();
        dt.column(2).search(val || '').draw();
    });
});
</script>
@endpush
@endif
