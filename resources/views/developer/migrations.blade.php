@extends('layouts.dev')

@section('title', 'Migration Yönetimi')
@section('page-title', 'Migration Yönetimi')

@section('content')
<style>
.mig-stat {
    display: flex; align-items: center; gap: 1rem;
    padding: 1rem 1.25rem;
    background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
}
.mig-stat-icon {
    width: 44px; height: 44px; border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; flex-shrink: 0;
}
.mig-stat-value { font-size: 1.5rem; font-weight: 800; line-height: 1; }
.mig-stat-label { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; margin-top: .15rem; }

.mig-row {
    display: flex; align-items: center; gap: .75rem;
    padding: .75rem 1.1rem;
    border-bottom: 1px solid #f3f4f6;
    transition: background .12s;
}
.mig-row:last-child { border-bottom: none; }
.mig-row:hover { background: #fafbfc; }

.mig-badge {
    display: inline-flex; align-items: center; gap: .3rem;
    font-size: .7rem; font-weight: 700; padding: .2rem .6rem;
    border-radius: 999px;
}
.mig-badge-ran { background: #dcfce7; color: #15803d; }
.mig-badge-pending { background: #fef3c7; color: #92400e; }

.mig-name {
    font-size: .84rem; font-weight: 600; color: #111827;
    word-break: break-all; flex: 1; min-width: 0;
}
.mig-meta {
    font-size: .72rem; color: #9ca3af;
    white-space: nowrap;
}

.mig-warn {
    background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px;
    padding: .65rem 1rem; font-size: .82rem; color: #92400e;
    display: flex; align-items: center; gap: .5rem;
}

.mig-confirm-overlay {
    display: none; position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,.5); backdrop-filter: blur(3px);
    align-items: center; justify-content: center;
}
.mig-confirm-overlay.active { display: flex; }
.mig-confirm-box {
    background: #fff; border-radius: 16px; padding: 1.5rem;
    max-width: 420px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,.2);
    text-align: center;
}
.mig-confirm-icon {
    width: 52px; height: 52px; border-radius: 50%;
    background: #fef3c7; color: #d97706;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.4rem; margin-bottom: .75rem;
}
.mig-confirm-title { font-size: 1.05rem; font-weight: 800; color: #111827; margin-bottom: .5rem; }
.mig-confirm-desc  { font-size: .85rem; color: #6b7280; line-height: 1.6; margin-bottom: 1.25rem; }
.mig-confirm-file  { font-size: .78rem; font-weight: 600; color: #374151; background: #f3f4f6; padding: .4rem .75rem; border-radius: 7px; display: inline-block; word-break: break-all; margin-bottom: 1rem; }
</style>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="mig-stat">
            <div class="mig-stat-icon" style="background:rgba(34,197,94,.1); color:#16a34a;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <div class="mig-stat-value">{{ $migrations->where('ran', true)->count() }}</div>
                <div class="mig-stat-label">Yüklü</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mig-stat">
            <div class="mig-stat-icon" style="background:rgba(245,158,11,.1); color:#d97706;">
                <i class="bi bi-clock-fill"></i>
            </div>
            <div>
                <div class="mig-stat-value">{{ $pendingCount }}</div>
                <div class="mig-stat-label">Bekleyen</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mig-stat">
            <div class="mig-stat-icon" style="background:rgba(99,102,241,.1); color:#6366f1;">
                <i class="bi bi-layers-fill"></i>
            </div>
            <div>
                <div class="mig-stat-value">{{ $migrations->count() }}</div>
                <div class="mig-stat-label">Toplam Dosya</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mig-stat">
            <div class="mig-stat-icon" style="background:rgba(239,68,68,.1); color:#ef4444;">
                <i class="bi bi-hash"></i>
            </div>
            <div>
                <div class="mig-stat-value">{{ $lastBatch }}</div>
                <div class="mig-stat-label">Son Batch</div>
            </div>
        </div>
    </div>
</div>

{{-- Run All Button --}}
@if($pendingCount > 0)
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div class="mig-warn">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span><strong>{{ $pendingCount }}</strong> migration yüklenmek için bekliyor. Mevcut veriler silinmez, sadece yeni tablolar/sütunlar oluşturulur.</span>
    </div>
    <button type="button" class="btn btn-accent btn-sm" onclick="confirmRunAll()">
        <i class="bi bi-play-fill me-1"></i> Hepsini Yükle
    </button>
</div>
@else
<div class="d-flex align-items-center gap-2 mb-3" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:.65rem 1rem;">
    <i class="bi bi-check-circle-fill" style="color:#16a34a"></i>
    <span style="font-size:.84rem;color:#166534;font-weight:600">Tüm migration'lar güncel — bekleyen yok.</span>
</div>
@endif

{{-- Migration List --}}
<div class="sm-card">
    <div class="sm-card-header">
        <i class="bi bi-database" style="color:var(--dev-accent)"></i>
        Migration Dosyaları
        <span style="margin-left:auto;font-size:.75rem;color:#9ca3af;font-weight:400">database/migrations/</span>
    </div>
    <div>
        @foreach($migrations as $m)
        <div class="mig-row">
            @if($m->ran)
                <span class="mig-badge mig-badge-ran"><i class="bi bi-check-lg"></i> Yüklü</span>
            @else
                <span class="mig-badge mig-badge-pending"><i class="bi bi-clock"></i> Bekliyor</span>
            @endif
            <div class="mig-name">{{ $m->name }}</div>
            @if($m->ran)
                <span class="mig-meta">batch {{ $m->batch }}</span>
            @else
                <button type="button" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem;padding:.2rem .6rem;" onclick="confirmRunSingle('{{ $m->name }}')">
                    <i class="bi bi-play-fill me-1"></i>Yükle
                </button>
            @endif
        </div>
        @endforeach
    </div>
</div>

{{-- Confirm dialog --}}
<div class="mig-confirm-overlay" id="migConfirm">
    <div class="mig-confirm-box">
        <div class="mig-confirm-icon"><i class="bi bi-database-fill-gear"></i></div>
        <div class="mig-confirm-title" id="migConfirmTitle">Migration'ları Yükle</div>
        <div id="migConfirmFile" class="mig-confirm-file" style="display:none"></div>
        <div class="mig-confirm-desc" id="migConfirmDesc">
            Bekleyen tüm migration'lar veritabanına uygulanacak.
            <strong>Mevcut veriler silinmez</strong>, sadece yeni tablolar ve sütunlar oluşturulur.
        </div>
        <div class="d-flex gap-2 justify-content-center">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="closeConfirm()">İptal</button>
            <form method="POST" action="{{ route('developer.migrations.run') }}" id="migRunForm">
                @csrf
                <input type="hidden" name="file" id="migRunFile" value="">
                <button type="submit" class="btn btn-accent btn-sm" id="migRunBtn">
                    <i class="bi bi-play-fill me-1"></i><span id="migRunBtnText">Yükle</span>
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmRunAll() {
    document.getElementById('migConfirmTitle').textContent = 'Tüm Bekleyen Migration\'ları Yükle';
    document.getElementById('migConfirmDesc').innerHTML = '<strong>{{ $pendingCount }}</strong> bekleyen migration veritabanına uygulanacak.<br>Mevcut veriler <strong>silinmez</strong>, sadece yeni tablolar/sütunlar oluşturulur.';
    document.getElementById('migConfirmFile').style.display = 'none';
    document.getElementById('migRunFile').value = '';
    document.getElementById('migRunBtnText').textContent = 'Hepsini Yükle';
    document.getElementById('migConfirm').classList.add('active');
}

function confirmRunSingle(name) {
    document.getElementById('migConfirmTitle').textContent = 'Migration Yükle';
    document.getElementById('migConfirmDesc').innerHTML = 'Bu migration veritabanına uygulanacak.<br>Mevcut veriler <strong>silinmez</strong>.';
    var fileEl = document.getElementById('migConfirmFile');
    fileEl.textContent = name;
    fileEl.style.display = 'inline-block';
    document.getElementById('migRunFile').value = name;
    document.getElementById('migRunBtnText').textContent = 'Yükle';
    document.getElementById('migConfirm').classList.add('active');
}

function closeConfirm() {
    document.getElementById('migConfirm').classList.remove('active');
}

document.getElementById('migConfirm').addEventListener('click', function(e) {
    if (e.target === this) closeConfirm();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeConfirm();
});

document.getElementById('migRunForm').addEventListener('submit', function() {
    var btn = document.getElementById('migRunBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Yükleniyor...';
});
</script>
@endpush
@endsection
