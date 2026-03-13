@extends('layouts.dev')

@section('title', 'Destek #' . $ticket->id)
@section('page-title', 'Destek Mesajı')

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
.chat-meta { font-size:.75rem; color:#98a2b3; }
</style>

<a href="{{ route('developer.tickets') }}" class="btn btn-sm btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left me-1"></i>Geri
</a>

@if(session('success'))
<div class="alert alert-success py-2 px-3" style="font-size:.84rem;border-radius:9px">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-lg-8">
        {{-- Ticket Info --}}
        <div class="sm-card mb-3">
            <div class="sm-card-header d-flex justify-content-between align-items-center">
                <span style="font-weight:700">{{ $ticket->subject }}</span>
                <span class="ticket-status st-{{ $ticket->status }}">{{ $ticket->status === 'open' ? 'Açık' : ($ticket->status === 'answered' ? 'Yanıtlandı' : 'Kapatıldı') }}</span>
            </div>
            <div class="sm-card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <span style="font-weight:600;font-size:.85rem">{{ $ticket->user_name }}</span>
                        <span class="text-muted" style="font-size:.78rem">({{ $ticket->user_email }})</span>
                    </div>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d.m.Y H:i') }}</small>
                </div>
                <div class="mb-2">
                    <span class="badge" style="background:rgba(255,107,53,.1);color:#c2410c;font-size:.72rem;border-radius:6px;padding:.2rem .5rem">
                        <i class="bi bi-building me-1"></i>{{ $ticket->restoran_adi }}
                    </span>
                </div>
                <div class="msg-bubble">{!! nl2br(e($ticket->message)) !!}</div>
            </div>
        </div>

        <div class="sm-card mb-3">
            <div class="sm-card-header">
                <i class="bi bi-chat-left-text me-1" style="color:#2563eb"></i>Mesaj Geçmişi
            </div>
            <div class="sm-card-body">
                @foreach($messages as $m)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="chat-meta">{{ $m['name'] }}</small>
                            <small class="chat-meta">{{ \Carbon\Carbon::parse($m['datetime'])->format('d.m.Y H:i') }}</small>
                        </div>
                        <div class="{{ $m['sender'] === 'developer' ? 'reply-bubble' : 'msg-bubble' }}">{!! nl2br(e($m['message'])) !!}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Reply Form --}}
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-pencil-square me-1" style="color:var(--accent)"></i>Yanıtla
            </div>
            <div class="sm-card-body">
                <form action="{{ route('developer.tickets.reply', $ticket->id) }}" method="POST">
                    @csrf
                    <textarea name="message" rows="5" class="form-control @error('message') is-invalid @enderror"
                              placeholder="Yanıtınızı yazın..." required>{{ old('message') }}</textarea>
                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="mt-3">
                        <button type="submit" class="btn btn-accent btn-sm">
                            <i class="bi bi-send me-1"></i>Yanıtla
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="sm-card sticky-top" style="top: 1rem;">
            <div class="sm-card-header">
                <i class="bi bi-info-circle me-1" style="color:#6366f1"></i>Bilet Özeti
            </div>
            <div class="sm-card-body">
                <div class="mb-3">
                    <div class="text-muted small mb-1">Bilet No</div>
                    <div class="fw-600">#{{ $ticket->id }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small mb-1">Restoran</div>
                    <div class="fw-600">{{ $ticket->restoran_adi }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small mb-1">Gönderen</div>
                    <div class="fw-600">{{ $ticket->user_name }}</div>
                    <div class="small text-muted">{{ $ticket->user_email }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small mb-1">Durum</div>
                    <span class="ticket-status st-{{ $ticket->status }}">{{ $ticket->status === 'open' ? 'Açık' : ($ticket->status === 'answered' ? 'Yanıtlandı' : 'Kapatıldı') }}</span>
                </div>
                <div>
                    <div class="text-muted small mb-1">Oluşturulma</div>
                    <div class="small">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d.m.Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
