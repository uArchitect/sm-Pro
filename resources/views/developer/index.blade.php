@extends('layouts.dev')

@section('title', 'Developer Panel')
@section('page-title', 'Platform Genel Bakış')

@section('content')
<style>
.stat-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:1.25rem 1.5rem; transition:box-shadow .18s,transform .18s; }
.stat-card:hover { box-shadow:0 4px 20px rgba(0,0,0,.08); transform:translateY(-2px); }
.stat-num  { font-size:2rem; font-weight:800; line-height:1.1; }
.stat-lbl  { font-size:.78rem; color:#9ca3af; font-weight:500; margin-top:.2rem; }
.stat-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; }
.tenant-row:hover { background:#fafafa; }
.toggle-btn { border:none; padding:.35rem .7rem; border-radius:20px; font-size:.72rem; font-weight:700; cursor:pointer; transition:all .15s; }
.toggle-active { background:#dcfce7; color:#15803d; }
.toggle-active:hover { background:#bbf7d0; }
.toggle-passive { background:#fee2e2; color:#dc2626; }
.toggle-passive:hover { background:#fecaca; }
.imp-btn { background:rgba(99,102,241,.08); color:#6366f1; border:1px solid rgba(99,102,241,.15); padding:.35rem .6rem; border-radius:7px; font-size:.75rem; font-weight:600; cursor:pointer; transition:all .15s; text-decoration:none; display:inline-flex; align-items:center; gap:.3rem; }
.imp-btn:hover { background:rgba(99,102,241,.15); color:#4f46e5; }
.pkg-chip { display:inline-flex; align-items:center; gap:.3rem; padding:.18rem .5rem; border-radius:999px; font-size:.68rem; font-weight:800; }
.pkg-basic { background:#eef2f7; color:#475569; }
.pkg-premium { background:#fef3c7; color:#92400e; }
.mini-stat { text-align:center; }
.mini-stat .ms-num { font-size:1.2rem; font-weight:800; color:#111; }
.mini-stat .ms-lbl { font-size:.65rem; color:#9ca3af; text-transform:uppercase; letter-spacing:.06em; font-weight:600; }
</style>

{{-- Stat cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(99,102,241,.1); color:#6366f1"><i class="bi bi-building"></i></div>
                <div>
                    <div class="stat-num" style="color:#6366f1">{{ $stats['total_tenants'] }}</div>
                    <div class="stat-lbl">Restoran</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(16,185,129,.1); color:#10b981"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="stat-num" style="color:#10b981">{{ $stats['active_tenants'] }}</div>
                    <div class="stat-lbl">Aktif</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(255,107,53,.1); color:#FF6B35"><i class="bi bi-people"></i></div>
                <div>
                    <div class="stat-num" style="color:#FF6B35">{{ $stats['total_users'] }}</div>
                    <div class="stat-lbl">Kullanıcı</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(245,158,11,.1); color:#f59e0b"><i class="bi bi-box-seam"></i></div>
                <div>
                    <div class="stat-num" style="color:#f59e0b">{{ $stats['total_products'] }}</div>
                    <div class="stat-lbl">Ürün</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(236,72,153,.1); color:#ec4899"><i class="bi bi-star-fill"></i></div>
                <div>
                    <div class="stat-num" style="color:#ec4899">{{ $stats['total_reviews'] }}</div>
                    <div class="stat-lbl">Değerlendirme</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(14,165,233,.1); color:#0ea5e9"><i class="bi bi-qr-code"></i></div>
                <div>
                    <div class="stat-num" style="color:#0ea5e9">{{ $stats['total_qr_visits'] }}</div>
                    <div class="stat-lbl">QR Ziyaret</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick stats row --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="sm-card">
            <div class="sm-card-body d-flex align-items-center gap-3">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(245,158,11,.12);color:#d97706;display:flex;align-items:center;justify-content:center;font-size:1.1rem">
                    <i class="bi bi-gem"></i>
                </div>
                <div>
                    <div style="font-size:1.3rem;font-weight:800;color:#d97706">{{ $stats['premium_tenants'] }}</div>
                    <div style="font-size:.72rem;color:#9ca3af;font-weight:600">Premium Restoran</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sm-card">
            <div class="sm-card-body d-flex align-items-center gap-3">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(16,185,129,.08);color:#10b981;display:flex;align-items:center;justify-content:center;font-size:1.1rem">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div>
                    <div style="font-size:1.3rem;font-weight:800;color:#10b981">{{ $stats['today_qr_visits'] }}</div>
                    <div style="font-size:.72rem;color:#9ca3af;font-weight:600">Bugünkü QR Ziyaret</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sm-card">
            <div class="sm-card-body d-flex align-items-center gap-3">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(236,72,153,.08);color:#ec4899;display:flex;align-items:center;justify-content:center;font-size:1.1rem">
                    <i class="bi bi-chat-heart"></i>
                </div>
                <div>
                    <div style="font-size:1.3rem;font-weight:800;color:#ec4899">{{ $stats['today_reviews'] }}</div>
                    <div style="font-size:.72rem;color:#9ca3af;font-weight:600">Bugünkü Değerlendirme</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sm-card">
            <div class="sm-card-body d-flex align-items-center gap-3">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(99,102,241,.08);color:#6366f1;display:flex;align-items:center;justify-content:center;font-size:1.1rem">
                    <i class="bi bi-building-add"></i>
                </div>
                <div>
                    <div style="font-size:1.3rem;font-weight:800;color:#6366f1">{{ $stats['new_tenants_week'] }}</div>
                    <div style="font-size:.72rem;color:#9ca3af;font-weight:600">Bu Hafta Yeni Restoran</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Tenants table --}}
    <div class="col-lg-8">
        <div class="sm-card">
            <div class="sm-card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-building me-1" style="color:#FF6B35"></i>Tüm Restoranlar</span>
                <span class="badge" style="background:rgba(99,102,241,.1);color:#6366f1;font-size:.75rem;border-radius:6px;padding:.3rem .6rem">
                    {{ $stats['total_tenants'] }} kayıt
                </span>
            </div>
            <div class="table-responsive">
                <table class="table sm-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width:40px">#</th>
                            <th>Restoran</th>
                            <th style="width:65px" class="text-center">Durum</th>
                            <th style="width:90px" class="text-center">Paket</th>
                            <th style="width:55px" class="text-center"><i class="bi bi-people" title="Kullanıcı"></i></th>
                            <th style="width:55px" class="text-center"><i class="bi bi-box-seam" title="Ürün"></i></th>
                            <th style="width:55px" class="text-center"><i class="bi bi-star" title="Değerlendirme"></i></th>
                            <th style="width:55px" class="text-center"><i class="bi bi-qr-code" title="QR Ziyaret"></i></th>
                            <th style="width:90px">Owner</th>
                            <th class="text-end pe-3" style="width:180px">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenants as $tenant)
                        <tr class="tenant-row">
                            <td class="ps-3 text-muted small">{{ $tenant->id }}</td>
                            <td>
                                <div class="fw-600" style="font-weight:600">{{ $tenant->restoran_adi }}</div>
                                <div class="text-muted" style="font-size:.7rem">{{ \Carbon\Carbon::parse($tenant->created_at)->format('d.m.Y') }}</div>
                            </td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('developer.tenant.toggle', $tenant->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="toggle-btn {{ $tenant->is_active ? 'toggle-active' : 'toggle-passive' }}">
                                        <i class="bi {{ $tenant->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                                        {{ $tenant->is_active ? 'Aktif' : 'Pasif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-center">
                                @php $pkg = $tenant->package ?? 'basic'; @endphp
                                <form method="POST" action="{{ route('developer.tenant.package', $tenant->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="pkg-chip {{ $pkg === 'premium' ? 'pkg-premium' : 'pkg-basic' }}" style="border:none">
                                        <i class="bi {{ $pkg === 'premium' ? 'bi-gem' : 'bi-box-seam' }}"></i>
                                        {{ strtoupper($pkg) }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-center small fw-semibold">{{ $tenant->user_count }}</td>
                            <td class="text-center small fw-semibold">{{ $tenant->product_count }}</td>
                            <td class="text-center small fw-semibold">{{ $tenant->review_count }}</td>
                            <td class="text-center small fw-semibold">{{ $tenant->qr_visit_count }}</td>
                            <td>
                                @if($tenant->owner)
                                <div style="font-size:.78rem;font-weight:500">{{ Str::limit($tenant->owner->name, 15) }}</div>
                                @else
                                <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-flex gap-1 justify-content-end flex-wrap">
                                    @if($tenant->owner)
                                    <form method="POST" action="{{ route('developer.tenant.impersonate', $tenant->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="imp-btn" title="Hesaba Giriş Yap" style="border:none">
                                            <i class="bi bi-box-arrow-in-right"></i> Giriş
                                        </button>
                                    </form>
                                    @endif
                                    <a href="{{ route('developer.tenant', $tenant->id) }}" class="btn btn-sm btn-outline-secondary" title="Detay">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('public.menu', $tenant->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Menü">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                    <form method="POST" action="{{ route('developer.tenant.destroy', $tenant->id) }}"
                                          onsubmit="return confirm('{{ $tenant->restoran_adi }} ve TÜM verileri kalıcı olarak silinecek. Onaylıyor musunuz?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Sil"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                <i class="bi bi-building fs-1 d-block mb-2 opacity-25"></i>
                                Henüz kayıtlı restoran yok.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent Reviews --}}
    <div class="col-lg-4">
        <div class="sm-card h-100">
            <div class="sm-card-header">
                <i class="bi bi-chat-heart me-1" style="color:#ec4899"></i>Son Değerlendirmeler
            </div>
            <div class="sm-card-body" style="padding:.75rem 1rem">
                @forelse($recentReviews as $r)
                <div style="padding:.65rem 0;{{ !$loop->last ? 'border-bottom:1px solid #f3f4f6' : '' }}">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span style="font-size:.78rem;font-weight:600;color:#374151">{{ $r->customer_name ?: 'Anonim' }}</span>
                        <span style="font-size:.68rem;color:#f59e0b;font-weight:700">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $r->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                        </span>
                    </div>
                    @if($r->comment)
                    <div style="font-size:.75rem;color:#6b7280;line-height:1.4">{{ Str::limit($r->comment, 80) }}</div>
                    @endif
                    <div class="d-flex justify-content-between mt-1">
                        <span style="font-size:.65rem;color:#6366f1;font-weight:600">{{ $r->restoran_adi }}</span>
                        <span style="font-size:.65rem;color:#9ca3af">{{ \Carbon\Carbon::parse($r->created_at)->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-chat-heart fs-3 d-block mb-2 opacity-25"></i>
                    <div style="font-size:.82rem">Henüz değerlendirme yok</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
