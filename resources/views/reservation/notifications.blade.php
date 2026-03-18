@extends('layouts.app')

@section('title', __('reservation.reservations_title'))
@section('page-title', __('reservation.reservations_title'))
@section('page-help', __('reservation.reservations_help'))

@push('styles')
<style>
    .rv-card {
        background: #fff; border: 1px solid var(--border); border-radius: 12px;
        padding: 1rem 1.25rem; margin-bottom: .75rem;
        transition: box-shadow .15s, border-color .15s;
    }
    .rv-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.06); }
    .rv-card.rv-pending { border-left: 3px solid #f59e0b; }
    .rv-card.rv-confirmed { border-left: 3px solid #22c55e; }
    .rv-card.rv-cancelled { border-left: 3px solid #ef4444; opacity: .65; }
    .rv-top { display: flex; align-items: center; justify-content: space-between; gap: .75rem; flex-wrap: wrap; }
    .rv-customer { font-weight: 700; font-size: .95rem; color: var(--text-primary); }
    .rv-status {
        font-size: .7rem; font-weight: 700; padding: .2rem .6rem;
        border-radius: 999px; text-transform: uppercase; letter-spacing: .03em;
    }
    .rv-status-pending { background: rgba(245,158,11,.12); color: #b45309; }
    .rv-status-confirmed { background: rgba(34,197,94,.12); color: #15803d; }
    .rv-status-cancelled { background: rgba(239,68,68,.12); color: #dc2626; }
    .rv-details { display: flex; flex-wrap: wrap; gap: .5rem 1.25rem; margin-top: .6rem; }
    .rv-detail { font-size: .8rem; color: var(--text-secondary); display: flex; align-items: center; gap: .35rem; }
    .rv-detail i { color: var(--text-muted); font-size: .85rem; }
    .rv-notes { font-size: .78rem; color: var(--text-muted); margin-top: .5rem; padding: .5rem .75rem; background: #f8fafc; border-radius: 8px; }
    .rv-actions { display: flex; gap: .4rem; margin-top: .75rem; }
    .rv-actions .btn { font-size: .72rem; padding: .25rem .6rem; }
    .rv-empty { text-align: center; padding: 3rem; color: var(--text-muted); }
    .rv-empty i { font-size: 2.5rem; opacity: .3; display: block; margin-bottom: .75rem; }
</style>
@endpush

@section('content')
@if($reservations->isEmpty())
    <div class="rv-empty">
        <i class="bi bi-calendar-check"></i>
        <div style="font-size:.88rem;font-weight:600">{{ __('reservation.no_reservations') }}</div>
        <p class="small mt-1">{{ __('reservation.no_reservations_hint') }}</p>
    </div>
@else
    @foreach($reservations as $r)
    <div class="rv-card rv-{{ $r->status }}">
        <div class="rv-top">
            <div class="rv-customer"><i class="bi bi-person-fill me-1" style="color:var(--accent)"></i>{{ $r->customer_name }}</div>
            <span class="rv-status rv-status-{{ $r->status }}">
                @if($r->status === 'pending') <i class="bi bi-clock me-1"></i>{{ __('reservation.status_pending') }}
                @elseif($r->status === 'confirmed') <i class="bi bi-check-circle me-1"></i>{{ __('reservation.status_confirmed') }}
                @else <i class="bi bi-x-circle me-1"></i>{{ __('reservation.status_cancelled') }}
                @endif
            </span>
        </div>
        <div class="rv-details">
            <div class="rv-detail"><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($r->reservation_date)->format('d.m.Y') }}</div>
            <div class="rv-detail"><i class="bi bi-clock"></i> {{ substr($r->start_time, 0, 5) }} – {{ substr($r->end_time, 0, 5) }}</div>
            <div class="rv-detail"><i class="bi bi-pin-map"></i> {{ $r->zone_name }}</div>
            <div class="rv-detail"><i class="bi bi-table"></i> {{ $r->table_name }} <span class="opacity-50">({{ $r->capacity }} {{ __('reservation.persons') }})</span></div>
            <div class="rv-detail"><i class="bi bi-telephone"></i> {{ $r->customer_phone }}</div>
            @if($r->customer_email)
            <div class="rv-detail"><i class="bi bi-envelope"></i> {{ $r->customer_email }}</div>
            @endif
        </div>
        @if($r->notes)
        <div class="rv-notes"><i class="bi bi-chat-text me-1"></i> {{ $r->notes }}</div>
        @endif
        <div class="rv-actions">
            @if($r->status === 'pending')
            <form method="POST" action="{{ route('reservations.updateStatus', $r->id) }}" class="d-inline">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="confirmed">
                <button class="btn btn-sm btn-outline-success"><i class="bi bi-check-lg me-1"></i>{{ __('reservation.confirm') }}</button>
            </form>
            <form method="POST" action="{{ route('reservations.updateStatus', $r->id) }}" class="d-inline">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="cancelled">
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg me-1"></i>{{ __('reservation.cancel') }}</button>
            </form>
            @elseif($r->status === 'confirmed')
            <form method="POST" action="{{ route('reservations.updateStatus', $r->id) }}" class="d-inline">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="cancelled">
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg me-1"></i>{{ __('reservation.cancel') }}</button>
            </form>
            @endif
            <form method="POST" action="{{ route('reservations.destroy', $r->id) }}" class="d-inline"
                  onsubmit="return confirm(@json(__('reservation.delete_reservation_confirm')))">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-trash me-1"></i>{{ __('common.delete') }}</button>
            </form>
            <span class="text-muted ms-auto" style="font-size:.7rem">{{ \Carbon\Carbon::parse($r->created_at)->diffForHumans() }}</span>
        </div>
    </div>
    @endforeach

    <div class="d-flex justify-content-center mt-3">
        {{ $reservations->links() }}
    </div>
@endif
@endsection
