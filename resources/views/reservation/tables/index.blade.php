@extends('layouts.app')

@section('title', __('reservation.tables_title'))
@section('page-title', __('reservation.tables_title'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <span class="text-muted" style="font-size:.82rem">{{ __('reservation.tables_total', ['count' => $tables->count()]) }}</span>
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
        <table class="table sm-table align-middle mb-0">
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
