@extends('layouts.app')

@section('title', __('reservation.zones_title'))
@section('page-title', __('reservation.zones_title'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <span class="text-muted" style="font-size:.82rem">{{ __('reservation.zones_total', ['count' => $zones->count()]) }}</span>
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
        <table class="table sm-table align-middle mb-0">
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
