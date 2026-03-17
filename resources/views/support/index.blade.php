@extends('layouts.app')

@section('title', __('support.my_tickets'))
@section('page-title', __('support.title'))
@section('page-help', __('support.page_help'))

@section('content')
<style>
.ticket-status {
    display: inline-flex; align-items: center; gap: .3rem;
    font-size: .7rem; font-weight: 700; padding: .2rem .55rem;
    border-radius: 20px;
}
.st-open     { background: #dbeafe; color: #1d4ed8; }
.st-answered { background: #dcfce7; color: #15803d; }
.st-closed   { background: #f3f4f6; color: #6b7280; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <span class="text-muted" style="font-size:.82rem">{{ __('support.my_tickets') }}</span>
    <a href="{{ route('support.create') }}" class="btn btn-accent btn-sm">
        <i class="bi bi-plus-lg me-1"></i>{{ __('support.new_ticket') }}
    </a>
</div>

<div class="sm-card">
    <div class="table-responsive">
        <table class="table sm-table align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">#</th>
                    <th>{{ __('support.subject') }}</th>
                    <th>{{ __('support.status') }}</th>
                    <th>{{ __('support.date') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $t)
                <tr>
                    <td class="ps-4 text-muted">{{ $t->id }}</td>
                    <td style="font-weight:600;font-size:.85rem">{{ $t->subject }}</td>
                    <td>
                        <span class="ticket-status st-{{ $t->status }}">
                            {{ __('support.' . $t->status) }}
                        </span>
                    </td>
                    <td class="text-muted" style="font-size:.8rem">{{ \Carbon\Carbon::parse($t->created_at)->format('d.m.Y H:i') }}</td>
                    <td>
                        <a href="{{ route('support.show', $t->id) }}" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem;padding:.2rem .6rem;">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-chat-dots" style="font-size:2rem;opacity:.3"></i>
                        <div class="mt-2" style="font-size:.85rem">{{ __('support.no_tickets') }}</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
