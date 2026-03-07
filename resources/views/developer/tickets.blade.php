@extends('layouts.dev')

@section('title', 'Destek Mesajları')
@section('page-title', 'Destek Mesajları')

@section('content')
<style>
.ticket-status {
    display:inline-flex; align-items:center; gap:.3rem;
    font-size:.7rem; font-weight:700; padding:.2rem .55rem; border-radius:20px;
}
.st-open     { background:#dbeafe; color:#1d4ed8; }
.st-answered { background:#dcfce7; color:#15803d; }
.st-closed   { background:#f3f4f6; color:#6b7280; }
.search-box {
    border:1px solid #e5e7eb; border-radius:9px; padding:.5rem .85rem;
    font-size:.84rem; width:100%; max-width:300px; outline:none;
    transition:border-color .15s,box-shadow .15s;
}
.search-box:focus { border-color:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,.1); }
</style>

<div class="sm-card">
    <div class="sm-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-headset me-1" style="color:#6366f1"></i>Tüm Destek Mesajları</span>
        <div class="d-flex align-items-center gap-2">
            <input type="text" id="ticketSearch" class="search-box" placeholder="Ara...">
            <span class="badge" style="background:rgba(99,102,241,.1);color:#6366f1;font-size:.75rem;border-radius:6px;padding:.3rem .6rem">
                {{ $tickets->count() }} mesaj
            </span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table sm-table align-middle mb-0" id="ticketsTable">
            <thead>
                <tr>
                    <th class="ps-4" style="width:50px">#</th>
                    <th>Restoran</th>
                    <th>Kullanıcı</th>
                    <th>Konu</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $t)
                <tr class="ticket-row">
                    <td class="ps-4 text-muted">{{ $t->id }}</td>
                    <td style="font-weight:600;font-size:.83rem">{{ $t->restoran_adi }}</td>
                    <td style="font-size:.82rem">{{ $t->user_name }}</td>
                    <td style="font-size:.83rem">{{ Str::limit($t->subject, 50) }}</td>
                    <td>
                        <span class="ticket-status st-{{ $t->status }}">{{ $t->status === 'open' ? 'Açık' : ($t->status === 'answered' ? 'Yanıtlandı' : 'Kapatıldı') }}</span>
                    </td>
                    <td class="text-muted" style="font-size:.78rem">{{ \Carbon\Carbon::parse($t->created_at)->format('d.m.Y H:i') }}</td>
                    <td>
                        <a href="{{ route('developer.tickets.show', $t->id) }}" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem;padding:.2rem .6rem">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-chat-dots" style="font-size:2rem;opacity:.3"></i>
                        <div class="mt-2" style="font-size:.85rem">Henüz destek mesajı yok.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const s = document.getElementById('ticketSearch');
    if (s) {
        s.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.ticket-row').forEach(r => {
                r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }
});
</script>
@endsection
