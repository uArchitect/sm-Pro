@extends('layouts.app')

@section('title', $ticket->subject)
@section('page-title', __('support.title'))

@section('content')
<style>
.ticket-status {
    display:inline-flex; align-items:center; gap:.3rem;
    font-size:.7rem; font-weight:700; padding:.2rem .55rem; border-radius:20px;
}
.st-open     { background:#dbeafe; color:#1d4ed8; }
.st-answered { background:#dcfce7; color:#15803d; }
.st-closed   { background:#f3f4f6; color:#6b7280; }
.msg-bubble {
    background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px;
    padding:1rem 1.2rem; font-size:.88rem; line-height:1.7; color:#374151;
}
.reply-bubble {
    background:linear-gradient(135deg,#eff6ff,#dbeafe); border:1px solid #bfdbfe;
    border-radius:12px; padding:1rem 1.2rem; font-size:.88rem; line-height:1.7; color:#1e3a5f;
}
</style>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <a href="{{ route('support.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left me-1"></i>{{ __('support.back') }}
        </a>

        <div class="sm-card mb-3">
            <div class="sm-card-header d-flex justify-content-between align-items-center">
                <span style="font-weight:700">{{ $ticket->subject }}</span>
                <span class="ticket-status st-{{ $ticket->status }}">{{ __('support.' . $ticket->status) }}</span>
            </div>
            <div class="sm-card-body">
                <div class="d-flex justify-content-between mb-2">
                    <small class="text-muted">{{ $user->name ?? '-' }}</small>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d.m.Y H:i') }}</small>
                </div>
                <div class="msg-bubble">{!! nl2br(e($ticket->message)) !!}</div>
            </div>
        </div>

        @if($ticket->admin_reply)
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-reply me-1" style="color:#2563eb"></i>{{ __('support.admin_reply') }}
            </div>
            <div class="sm-card-body">
                <div class="d-flex justify-content-between mb-2">
                    <small class="text-muted">Developer</small>
                    @if($ticket->replied_at)
                    <small class="text-muted">{{ \Carbon\Carbon::parse($ticket->replied_at)->format('d.m.Y H:i') }}</small>
                    @endif
                </div>
                <div class="reply-bubble">{!! nl2br(e($ticket->admin_reply)) !!}</div>
            </div>
        </div>
        @else
        <div class="text-center py-4 text-muted" style="font-size:.85rem">
            <i class="bi bi-hourglass-split me-1"></i>{{ __('support.no_reply_yet') }}
        </div>
        @endif
    </div>
</div>
@endsection
