@extends('layouts.dev')

@section('title', $tenant->restoran_adi . ' — Detay')
@section('page-title', $tenant->restoran_adi)

@section('content')
<style>
.info-label { font-size:.72rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.15rem; }
.info-val   { font-size:.88rem; font-weight:600; color:#1f2937; }
.toggle-btn { border:none; padding:.4rem .85rem; border-radius:20px; font-size:.78rem; font-weight:700; cursor:pointer; transition:all .15s; }
.toggle-active { background:#dcfce7; color:#15803d; }
.toggle-active:hover { background:#bbf7d0; }
.toggle-passive { background:#fee2e2; color:#dc2626; }
.toggle-passive:hover { background:#fecaca; }
.imp-btn-lg { background:linear-gradient(135deg,#6366f1,#818cf8); color:#fff; border:none; padding:.5rem 1.1rem; border-radius:9px; font-size:.82rem; font-weight:700; cursor:pointer; transition:all .15s; display:inline-flex; align-items:center; gap:.4rem; box-shadow:0 2px 10px rgba(99,102,241,.3); }
.imp-btn-lg:hover { box-shadow:0 6px 20px rgba(99,102,241,.4); transform:translateY(-1px); color:#fff; }
.mini-stat { text-align:center; padding:.75rem .5rem; }
.mini-stat .ms-num { font-size:1.5rem; font-weight:800; line-height:1; }
.mini-stat .ms-lbl { font-size:.65rem; color:#9ca3af; text-transform:uppercase; letter-spacing:.06em; font-weight:600; margin-top:.25rem; }
.pkg-card {
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 1rem;
    background: linear-gradient(180deg, #fff 0%, #fafafa 100%);
}
.pkg-badge {
    display:inline-flex; align-items:center; gap:.4rem;
    padding:.32rem .75rem; border-radius:999px; font-size:.74rem; font-weight:800;
}
.pkg-basic { background:#eef2f7; color:#475569; }
.pkg-premium { background:#fef3c7; color:#92400e; }
</style>

{{-- Top actions bar --}}
<div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
    <a href="{{ route('developer.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Geri
    </a>

    <form method="POST" action="{{ route('developer.tenant.toggle', $tenant->id) }}" class="d-inline">
        @csrf
        <button type="submit" class="toggle-btn {{ $tenant->is_active ? 'toggle-active' : 'toggle-passive' }}">
            <i class="bi {{ $tenant->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
            {{ $tenant->is_active ? 'Aktif' : 'Pasif' }}
        </button>
    </form>

    @php $pkg = $tenant->package ?? 'basic'; @endphp
    <form method="POST" action="{{ route('developer.tenant.package', $tenant->id) }}" class="d-inline">
        @csrf
        <button type="submit" class="toggle-btn {{ $pkg === 'premium' ? 'toggle-active' : 'toggle-passive' }}" style="{{ $pkg === 'premium' ? 'background:#fef3c7;color:#92400e;' : '' }}">
            <i class="bi {{ $pkg === 'premium' ? 'bi-gem' : 'bi-box' }} me-1"></i>
            {{ $pkg === 'premium' ? 'Premium' : 'Basic' }}
        </button>
    </form>

    @php $owner = $users->where('role', 'owner')->first(); @endphp
    @if($owner)
    <form method="POST" action="{{ route('developer.tenant.impersonate', $tenant->id) }}" class="d-inline">
        @csrf
        <button type="submit" class="imp-btn-lg">
            <i class="bi bi-box-arrow-in-right"></i> Hesaba Giriş Yap
        </button>
    </form>
    @endif

    <a href="{{ route('public.menu', $tenant->id) }}" target="_blank" class="btn btn-sm btn-outline-dark">
        <i class="bi bi-box-arrow-up-right me-1"></i>Menüyü Görüntüle
    </a>

    <form method="POST" action="{{ route('developer.tenant.destroy', $tenant->id) }}" class="ms-auto"
          onsubmit="return confirm('{{ $tenant->restoran_adi }} ve TÜM verileri kalıcı silinecek!')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Sil</button>
    </form>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="sm-card">
            <div class="mini-stat">
                <div class="ms-num" style="color:#6366f1">{{ $users->count() }}</div>
                <div class="ms-lbl">Kullanıcı</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="sm-card">
            <div class="mini-stat">
                <div class="ms-num" style="color:#f59e0b">{{ $products->count() }}</div>
                <div class="ms-lbl">Ürün</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="sm-card">
            <div class="mini-stat">
                <div class="ms-num" style="color:#ec4899">{{ $reviewStats->total }}</div>
                <div class="ms-lbl">Değerlendirme ({{ number_format($reviewStats->avg_rating, 1) }} <i class="bi bi-star-fill" style="font-size:.6rem"></i>)</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="sm-card">
            <div class="mini-stat">
                <div class="ms-num" style="color:#0ea5e9">{{ $qrStats['total'] }}</div>
                <div class="ms-lbl">QR Ziyaret (Bugün: {{ $qrStats['today'] }})</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Tenant info + edit --}}
    <div class="col-md-5">
        <div class="sm-card h-100">
            <div class="sm-card-header"><i class="bi bi-building me-1" style="color:#FF6B35"></i>Restoran Bilgileri</div>
            <div class="sm-card-body">
                <div class="pkg-card mb-3">
                    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <div>
                            <div class="info-label">Paket Durumu</div>
                            <div class="info-val">Bu restoran şu anda {{ $pkg === 'premium' ? 'Premium' : 'Basic' }} pakette.</div>
                        </div>
                        <span class="pkg-badge {{ $pkg === 'premium' ? 'pkg-premium' : 'pkg-basic' }}">
                            <i class="bi {{ $pkg === 'premium' ? 'bi-gem' : 'bi-box-seam' }}"></i>
                            {{ strtoupper($pkg) }}
                        </span>
                    </div>
                    <div class="mt-3 d-flex gap-2 flex-wrap">
                        <form method="POST" action="{{ route('developer.tenant.package', $tenant->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $pkg === 'premium' ? 'btn-outline-secondary' : 'btn-warning' }}">
                                <i class="bi {{ $pkg === 'premium' ? 'bi-arrow-down-circle' : 'bi-gem' }} me-1"></i>
                                {{ $pkg === 'premium' ? 'Basic Pakete Düşür' : 'Premium Pakete Yükselt' }}
                            </button>
                        </form>
                        <small class="text-muted align-self-center">
                            Premium: Slider, Etkinlikler ve Sipariş Yönetimi
                        </small>
                    </div>
                </div>

                <form method="POST" action="{{ route('developer.tenant.update', $tenant->id) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Restoran Adı</label>
                        <input type="text" name="restoran_adi" class="form-control @error('restoran_adi') is-invalid @enderror"
                               value="{{ old('restoran_adi', $tenant->restoran_adi) }}" required>
                        @error('restoran_adi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adres</label>
                        <input type="text" name="restoran_adresi" class="form-control"
                               value="{{ old('restoran_adresi', $tenant->restoran_adresi) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="text" name="restoran_telefonu" class="form-control"
                               value="{{ old('restoran_telefonu', $tenant->restoran_telefonu) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Paket</label>
                        <select name="package" class="form-select">
                            <option value="basic" {{ old('package', $tenant->package ?? 'basic') === 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="premium" {{ old('package', $tenant->package ?? 'basic') === 'premium' ? 'selected' : '' }}>Premium</option>
                        </select>
                        <div class="form-text">Premium pakette Slider, Etkinlikler ve Sipariş Yönetimi açılır.</div>
                    </div>

                    <div class="row g-2 text-muted small mb-3" style="font-size:.75rem">
                        <div class="col-6"><strong>ID:</strong> #{{ $tenant->id }}</div>
                        <div class="col-6"><strong>Kayıt:</strong> {{ \Carbon\Carbon::parse($tenant->created_at)->format('d.m.Y H:i') }}</div>
                        <div class="col-6"><strong>Sipariş:</strong> {{ $tenant->ordering_enabled ? 'Açık' : 'Kapalı' }}</div>
                        <div class="col-6"><strong>Logo:</strong> {{ $tenant->logo ? 'Var' : 'Yok' }}</div>
                    </div>

                    @if($tenant->instagram || $tenant->facebook || $tenant->twitter || $tenant->whatsapp)
                    <div class="d-flex gap-2 mb-3">
                        @if($tenant->instagram)<span class="badge" style="background:rgba(225,48,108,.08);color:#e1306c;font-size:.7rem"><i class="bi bi-instagram me-1"></i>{{ $tenant->instagram }}</span>@endif
                        @if($tenant->facebook)<span class="badge" style="background:rgba(24,119,242,.08);color:#1877f2;font-size:.7rem"><i class="bi bi-facebook me-1"></i>{{ $tenant->facebook }}</span>@endif
                        @if($tenant->twitter)<span class="badge" style="background:rgba(29,155,240,.08);color:#1d9bf0;font-size:.7rem"><i class="bi bi-twitter-x me-1"></i>{{ $tenant->twitter }}</span>@endif
                        @if($tenant->whatsapp)<span class="badge" style="background:rgba(37,211,102,.08);color:#25d366;font-size:.7rem"><i class="bi bi-whatsapp me-1"></i>{{ $tenant->whatsapp }}</span>@endif
                    </div>
                    @endif

                    <button type="submit" class="btn btn-accent btn-sm w-100">
                        <i class="bi bi-check-lg me-1"></i>Bilgileri Güncelle
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Users --}}
    <div class="col-md-7">
        <div class="sm-card h-100">
            <div class="sm-card-header"><i class="bi bi-people me-1" style="color:#6366f1"></i>Kullanıcılar ({{ $users->count() }})</div>
            <div class="table-responsive">
                <table class="table sm-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Ad</th>
                            <th>E-posta</th>
                            <th>Rol</th>
                            <th>Kayıt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                        <tr>
                            <td class="ps-3 text-muted small">{{ $u->id }}</td>
                            <td class="fw-semibold small">{{ $u->name }}</td>
                            <td class="text-muted small">{{ $u->email }}</td>
                            <td>
                                @php
                                    $roleColors = ['owner'=>'#c2410c','admin'=>'#0369a1','personel'=>'#374151'];
                                    $roleBgs    = ['owner'=>'rgba(255,107,53,.1)','admin'=>'rgba(3,105,161,.08)','personel'=>'rgba(0,0,0,.04)'];
                                @endphp
                                <span class="badge" style="background:{{ $roleBgs[$u->role] ?? 'rgba(0,0,0,.04)' }};color:{{ $roleColors[$u->role] ?? '#374151' }};font-size:.72rem;border-radius:5px">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ \Carbon\Carbon::parse($u->created_at)->format('d.m.Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Reviews --}}
@if($recentReviews->count() > 0)
<div class="sm-card mb-4">
    <div class="sm-card-header"><i class="bi bi-chat-heart me-1" style="color:#ec4899"></i>Son Değerlendirmeler</div>
    <div class="sm-card-body" style="padding:.75rem 1.25rem">
        @foreach($recentReviews as $r)
        <div style="padding:.6rem 0;{{ !$loop->last ? 'border-bottom:1px solid #f3f4f6' : '' }}">
            <div class="d-flex align-items-center justify-content-between">
                <span style="font-size:.82rem;font-weight:600;color:#1f2937">{{ $r->customer_name ?: 'Anonim' }}</span>
                <div>
                    <span style="font-size:.72rem;color:#f59e0b;font-weight:700">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= $r->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </span>
                    <span class="text-muted ms-2" style="font-size:.68rem">{{ \Carbon\Carbon::parse($r->created_at)->diffForHumans() }}</span>
                </div>
            </div>
            @if($r->comment)
            <div style="font-size:.78rem;color:#6b7280;margin-top:.25rem">{{ $r->comment }}</div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Products --}}
<div class="sm-card">
    <div class="sm-card-header"><i class="bi bi-box-seam me-1" style="color:#f59e0b"></i>Ürünler ({{ $products->count() }})</div>
    <div class="table-responsive">
        <table class="table sm-table align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-3" style="width:50px">Foto</th>
                    <th>Ürün</th>
                    <th>Kategori</th>
                    <th style="width:100px">Fiyat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                <tr>
                    <td class="ps-3">
                        @if($p->image)
                            <img src="{{ asset('storage/'.$p->image) }}" style="width:36px;height:36px;border-radius:8px;object-fit:cover">
                        @else
                            <div style="width:36px;height:36px;border-radius:8px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#d1d5db">
                                <i class="bi bi-box-seam" style="font-size:.8rem"></i>
                            </div>
                        @endif
                    </td>
                    <td class="fw-semibold small">{{ $p->name }}</td>
                    <td><span class="badge" style="background:rgba(99,102,241,.08);color:#6366f1;border-radius:5px;font-size:.72rem">{{ $p->category_name }}</span></td>
                    <td style="color:#FF6B35;font-weight:700;font-size:.85rem">{{ number_format($p->price, 2, ',', '.') }} ₺</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Ürün yok</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
