@extends('layouts.app')

@section('title', __('reservation.zones_title'))
@section('page-title', __('reservation.zones_title'))

@section('content')
<style>
.reservation-search { border:1.5px solid #e5e7eb; border-radius:9px; padding:.4rem .75rem .4rem 2rem; font-size:.82rem; width:220px; background:#fff; transition:border-color .15s, box-shadow .15s; }
.reservation-search:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(255,107,53,.12); outline:none; }
.reservation-search-wrap { position:relative; }
.reservation-search-wrap i { position:absolute; left:.7rem; top:50%; transform:translateY(-50%); color:#98a2b3; font-size:.8rem; pointer-events:none; }
</style>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="text-muted" style="font-size:.82rem">{{ __('reservation.zones_total', ['count' => $zones->count()]) }}</span>
        @if($zones->isNotEmpty())
        <div class="reservation-search-wrap">
            <i class="bi bi-search"></i>
            <input type="text" id="zonesSearch" class="reservation-search" placeholder="{{ __('reservation.search_zones') }}" autocomplete="off">
        </div>
        @endif
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reservation.tables.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-table me-1"></i>{{ __('reservation.tables_title') }}
        </a>
        <a href="{{ route('reservation.zones.create') }}" class="btn btn-accent btn-sm">
            <i class="bi bi-plus-lg me-1"></i>{{ __('reservation.zone_add') }}
        </a>
    </div>
</div>

@if($zones->isEmpty())
<div class="text-center py-5 text-muted">
    <i class="bi bi-pin-map" style="font-size:2.5rem;opacity:.3"></i>
    <div class="mt-2" style="font-size:.85rem">{{ __('reservation.no_zones') }}</div>
    <p class="small mt-1 mb-3">{{ __('reservation.zones_hint') }}</p>
    <a href="{{ route('reservation.zones.create') }}" class="btn btn-accent btn-sm">{{ __('reservation.zone_add_first') }}</a>
</div>
@else
<div class="sm-card">
    <div class="table-responsive">
        <table class="table sm-table align-middle mb-0" id="zonesTable">
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th>{{ __('reservation.zone_name') }}</th>
                    <th style="width:120px">{{ __('reservation.tables_count') }}</th>
                    <th class="text-end pe-4" style="width:120px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($zones as $zone)
                <tr>
                    <td class="text-muted" style="font-size:.8rem">{{ $zone->sort_order }}</td>
                    <td>
                        <span class="fw-semibold" style="color:var(--text-primary)">{{ $zone->name }}</span>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $zoneCounts[$zone->id] ?? 0 }} {{ __('reservation.tables') }}</span>
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('reservation.zones.edit', $zone->id) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('common.edit') }}">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('reservation.zones.destroy', $zone->id) }}" class="d-inline"
                              onsubmit="return confirm({{ json_encode(__('reservation.zone_delete_confirm', ['name' => $zone->name])) }})">
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

@if($zones->isNotEmpty())
@push('scripts')
<script>
$(function() {
    var dt = $('#zonesTable').DataTable({
        ordering: true,
        order: [[1, 'asc']],
        paging: true,
        pageLength: 25,
        dom: 't<"d-flex justify-content-between align-items-center mt-3"ip>',
        language: {
            info: '_START_–_END_ / _TOTAL_',
            infoEmpty: '0 / 0',
            infoFiltered: '',
            zeroRecords: '<div class="text-center py-4 text-muted"><i class="bi bi-search fs-3 d-block mb-2 opacity-25"></i>{{ __("reservation.no_match_zones") }}</div>',
            paginate: { previous: '‹', next: '›' }
        },
        columnDefs: [
            { orderable: false, searchable: false, targets: 3 }
        ]
    });
    $('#zonesSearch').on('keyup', function() { dt.search(this.value).draw(); });
});
</script>
@endpush
@endif
