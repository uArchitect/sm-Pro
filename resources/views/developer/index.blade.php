@extends('layouts.dev')

@section('title', 'Developer Panel')
@section('page-title', 'Platform Genel Bakış')

@section('content')
<style>
.stat-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:1.25rem 1.5rem; }
.stat-num  { font-size:2rem; font-weight:800; line-height:1.1; }
.stat-lbl  { font-size:.78rem; color:#9ca3af; font-weight:500; margin-top:.2rem; }
.tenant-row:hover { background:#fafafa; }
.role-dev  { background:rgba(239,68,68,.1);  color:#dc2626; }
.role-owner{ background:rgba(255,107,53,.12); color:#c2410c; }
.stat-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; }
</style>

{{-- Stat cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:rgba(99,102,241,.1); color:#6366f1"><i class="bi bi-building"></i></div>
            <div>
                <div class="stat-num" style="color:#6366f1">{{ $stats['total_tenants'] }}</div>
                <div class="stat-lbl">Kayıtlı Restoran</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:rgba(255,107,53,.1); color:#FF6B35"><i class="bi bi-people"></i></div>
            <div>
                <div class="stat-num" style="color:#FF6B35">{{ $stats['total_users'] }}</div>
                <div class="stat-lbl">Toplam Kullanıcı</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:rgba(16,185,129,.1); color:#10b981"><i class="bi bi-grid-3x3-gap"></i></div>
            <div>
                <div class="stat-num" style="color:#10b981">{{ $stats['total_categories'] }}</div>
                <div class="stat-lbl">Toplam Kategori</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:rgba(245,158,11,.1); color:#f59e0b"><i class="bi bi-box-seam"></i></div>
            <div>
                <div class="stat-num" style="color:#f59e0b">{{ $stats['total_products'] }}</div>
                <div class="stat-lbl">Toplam Ürün</div>
            </div>
        </div>
    </div>
</div>

{{-- Tenants table --}}
<div class="sm-card">
    <div class="sm-card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-building me-1" style="color:#FF6B35"></i>Tüm Restoranlar / Tenantlar</span>
        <span class="badge" style="background:rgba(99,102,241,.1);color:#6366f1;font-size:.75rem;border-radius:6px;padding:.3rem .6rem">
            {{ $stats['total_tenants'] }} kayıt
        </span>
    </div>
    <div class="table-responsive">
        <table class="table sm-table align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width:50px">#</th>
                    <th>Restoran / Firma</th>
                    <th style="width:80px" class="text-center">Kullanıcı</th>
                    <th style="width:90px" class="text-center">Kategori</th>
                    <th style="width:80px" class="text-center">Ürün</th>
                    <th style="width:110px">Owner</th>
                    <th style="width:110px">Kayıt Tarihi</th>
                    <th class="text-end pe-4" style="width:120px">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                <tr class="tenant-row">
                    <td class="ps-4 text-muted small">{{ $tenant->id }}</td>
                    <td>
                        <div class="fw-600" style="font-weight:600">{{ $tenant->restoran_adi }}</div>
                        <div class="text-muted" style="font-size:.78rem">{{ $tenant->firma_adi }}</div>
                    </td>
                    <td class="text-center">
                        <span class="badge" style="background:rgba(99,102,241,.08);color:#6366f1;border-radius:6px;padding:.25rem .5rem;font-size:.75rem">
                            {{ $tenant->user_count }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge" style="background:rgba(16,185,129,.08);color:#059669;border-radius:6px;padding:.25rem .5rem;font-size:.75rem">
                            {{ $tenant->category_count }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge" style="background:rgba(245,158,11,.08);color:#d97706;border-radius:6px;padding:.25rem .5rem;font-size:.75rem">
                            {{ $tenant->product_count }}
                        </span>
                    </td>
                    <td>
                        @if($tenant->owner)
                        <div style="font-size:.8rem;font-weight:500">{{ $tenant->owner->name }}</div>
                        <div class="text-muted" style="font-size:.72rem">{{ $tenant->owner->email }}</div>
                        @else
                        <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ \Carbon\Carbon::parse($tenant->created_at)->format('d.m.Y') }}</td>
                    <td class="text-end pe-4">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('developer.tenant', $tenant->id) }}" class="btn btn-sm btn-outline-secondary" title="Detay">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('public.menu', $tenant->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Menüyü Görüntüle">
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
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-building fs-1 d-block mb-2 opacity-25"></i>
                        Henüz kayıtlı restoran yok.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
