@extends('layouts.dev')

@section('title', 'İletişim Mesajları')
@section('page-title', 'İletişim Mesajları')

@section('content')
<style>
.cm-stats { display: flex; gap: 1rem; margin-bottom: 1.25rem; flex-wrap: wrap; }
.cm-stat {
    display: flex; align-items: center; gap: .85rem;
    padding: .85rem 1.1rem;
    background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,.04); flex: 1; min-width: 160px;
}
.cm-stat-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
}
.cm-stat-value { font-size: 1.35rem; font-weight: 800; line-height: 1; }
.cm-stat-label { font-size: .68rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; margin-top: .1rem; }

.cm-row {
    display: flex; align-items: center; gap: .75rem;
    padding: .8rem 1.1rem;
    border-bottom: 1px solid #f3f4f6;
    transition: background .12s;
    text-decoration: none; color: inherit;
}
.cm-row:last-child { border-bottom: none; }
.cm-row:hover { background: #fafbfc; }
.cm-row.unread { background: #fffbf5; }
.cm-row.unread:hover { background: #fff6eb; }

.cm-avatar {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; font-weight: 700; flex-shrink: 0;
    background: linear-gradient(135deg, #4F46E5, #6366F1); color: #fff;
}
.cm-info { flex: 1; min-width: 0; }
.cm-name { font-size: .84rem; font-weight: 700; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cm-preview { font-size: .76rem; color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 400px; }
.cm-meta { display: flex; flex-direction: column; align-items: flex-end; gap: .15rem; flex-shrink: 0; }
.cm-date { font-size: .7rem; color: #9ca3af; font-weight: 500; }
.cm-dot { width: 8px; height: 8px; border-radius: 50%; background: #4F46E5; }
.cm-empty { text-align: center; padding: 3rem 1rem; color: #9ca3af; }
.cm-empty i { font-size: 2.5rem; display: block; margin-bottom: .75rem; opacity: .4; }
</style>

<div class="cm-stats">
    <div class="cm-stat">
        <div class="cm-stat-icon" style="background:rgba(79,70,229,.1);color:#4F46E5"><i class="bi bi-envelope-fill"></i></div>
        <div>
            <div class="cm-stat-value">{{ $messages ? $messages->total() : 0 }}</div>
            <div class="cm-stat-label">Toplam Mesaj</div>
        </div>
    </div>
    <div class="cm-stat">
        <div class="cm-stat-icon" style="background:rgba(239,68,68,.1);color:#ef4444"><i class="bi bi-envelope-exclamation-fill"></i></div>
        <div>
            <div class="cm-stat-value">{{ $unreadCount ?? 0 }}</div>
            <div class="cm-stat-label">Okunmamış</div>
        </div>
    </div>
</div>

<div class="sm-card">
    <div class="sm-card-header">
        <i class="bi bi-chat-left-text" style="color:var(--dev-accent)"></i>
        Gelen Mesajlar
    </div>
    <div>
        @if(!$messages || $messages->isEmpty())
            <div class="cm-empty">
                <i class="bi bi-inbox"></i>
                <div style="font-size:.88rem;font-weight:600">Henüz mesaj yok</div>
                <div style="font-size:.78rem;margin-top:.25rem">İletişim sayfasından gönderilen mesajlar burada görünecek.</div>
            </div>
        @else
            @foreach($messages as $m)
            <a href="{{ route('developer.contact-messages.show', $m->id) }}" class="cm-row {{ !$m->is_read ? 'unread' : '' }}">
                <div class="cm-avatar">{{ mb_strtoupper(mb_substr($m->name, 0, 2)) }}</div>
                <div class="cm-info">
                    <div class="cm-name">
                        {{ $m->name }}
                        <span style="font-weight:400;color:#9ca3af;font-size:.75rem;margin-left:.35rem">{{ $m->email }}</span>
                    </div>
                    <div class="cm-preview">{{ Str::limit($m->message, 80) }}</div>
                </div>
                <div class="cm-meta">
                    <div class="cm-date">{{ \Carbon\Carbon::parse($m->created_at)->diffForHumans() }}</div>
                    @if(!$m->is_read)
                        <div class="cm-dot"></div>
                    @endif
                </div>
            </a>
            @endforeach
        @endif
    </div>
</div>

@if($messages && $messages->hasPages())
<div class="d-flex justify-content-center mt-3">
    {{ $messages->links() }}
</div>
@endif
@endsection
