@extends('layouts.dev')

@section('title', 'Mesaj — ' . $msg->name)
@section('page-title', 'İletişim Mesajı')

@section('content')
<style>
.msg-card {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
    box-shadow: 0 1px 4px rgba(0,0,0,.04); overflow: hidden;
}
.msg-header {
    display: flex; align-items: center; gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
}
.msg-avatar {
    width: 48px; height: 48px; border-radius: 13px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; font-weight: 800; flex-shrink: 0;
    background: linear-gradient(135deg, #FF6B35, #FF8C42); color: #fff;
}
.msg-sender { flex: 1; }
.msg-sender-name { font-size: 1rem; font-weight: 700; color: #111827; }
.msg-sender-email { font-size: .8rem; color: #6b7280; }
.msg-sender-phone { font-size: .78rem; color: #6b7280; margin-top: .1rem; }
.msg-body {
    padding: 1.5rem;
    font-size: .9rem; line-height: 1.75; color: #374151;
    white-space: pre-wrap; word-wrap: break-word;
}
.msg-footer {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .75rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid #f3f4f6;
    background: #fafbfc;
}
.msg-date { font-size: .78rem; color: #9ca3af; }
.msg-actions { display: flex; gap: .5rem; }
</style>

<div class="mb-3">
    <a href="{{ route('developer.contact-messages') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Tüm Mesajlar
    </a>
</div>

<div class="msg-card">
    <div class="msg-header">
        <div class="msg-avatar">{{ mb_strtoupper(mb_substr($msg->name, 0, 2)) }}</div>
        <div class="msg-sender">
            <div class="msg-sender-name">{{ $msg->name }}</div>
            <div class="msg-sender-email">
                <i class="bi bi-envelope me-1"></i>
                <a href="mailto:{{ $msg->email }}" style="color:inherit;text-decoration:none">{{ $msg->email }}</a>
            </div>
            @if($msg->phone)
            <div class="msg-sender-phone">
                <i class="bi bi-telephone me-1"></i>
                <a href="tel:{{ $msg->phone }}" style="color:inherit;text-decoration:none">{{ $msg->phone }}</a>
            </div>
            @endif
        </div>
        <div style="text-align:right">
            @if($msg->is_read)
                <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .55rem;border-radius:6px;background:rgba(16,185,129,.1);color:#16a34a;font-size:.7rem;font-weight:600">
                    <i class="bi bi-check-lg"></i> Okundu
                </span>
            @else
                <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .55rem;border-radius:6px;background:rgba(255,107,53,.1);color:#FF6B35;font-size:.7rem;font-weight:600">
                    <i class="bi bi-circle-fill" style="font-size:.4rem"></i> Yeni
                </span>
            @endif
        </div>
    </div>

    <div class="msg-body">{{ $msg->message }}</div>

    <div class="msg-footer">
        <div class="msg-date">
            <i class="bi bi-calendar3 me-1"></i>{{ $msg->created_at->format('d.m.Y H:i') }}
            <span style="margin-left:.5rem;color:#d1d5db">·</span>
            <span style="margin-left:.5rem">{{ $msg->created_at->diffForHumans() }}</span>
        </div>
        <div class="msg-actions">
            <a href="mailto:{{ $msg->email }}?subject=Re: Sipariş Masanda İletişim" class="btn btn-accent btn-sm">
                <i class="bi bi-reply me-1"></i> Yanıtla
            </a>
            @if($msg->phone)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $msg->phone) }}" target="_blank" class="btn btn-outline-secondary btn-sm" style="color:#25D366;border-color:#25D366">
                <i class="bi bi-whatsapp me-1"></i> WhatsApp
            </a>
            @endif
            <form method="POST" action="{{ route('developer.contact-messages.toggle-read', $msg->id) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-{{ $msg->is_read ? 'envelope' : 'envelope-open' }} me-1"></i>
                    {{ $msg->is_read ? 'Okunmadı Yap' : 'Okundu Yap' }}
                </button>
            </form>
            <form method="POST" action="{{ route('developer.contact-messages.destroy', $msg->id) }}" class="d-inline" onsubmit="return confirm('Bu mesajı silmek istediğinize emin misiniz?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash me-1"></i> Sil
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
