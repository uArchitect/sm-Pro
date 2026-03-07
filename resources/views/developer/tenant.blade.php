@extends('layouts.dev')

@section('title', $tenant->restoran_adi . ' — Detay')
@section('page-title', $tenant->restoran_adi)

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('developer.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Geri
    </a>
    <a href="{{ route('public.menu', $tenant->id) }}" target="_blank" class="btn btn-sm btn-outline-dark">
        <i class="bi bi-box-arrow-up-right me-1"></i>Public Menü
    </a>
    <form method="POST" action="{{ route('developer.tenant.destroy', $tenant->id) }}" class="ms-auto"
          onsubmit="return confirm('{{ $tenant->restoran_adi }} ve TÜM verileri kalıcı silinecek!')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Tenant'ı Sil</button>
    </form>
</div>

<div class="row g-3 mb-4">
    {{-- Tenant info --}}
    <div class="col-md-4">
        <div class="sm-card h-100">
            <div class="sm-card-header"><i class="bi bi-building me-1" style="color:#FF6B35"></i>Firma Bilgileri</div>
            <div class="sm-card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted fw-semibold">ID</dt>        <dd class="col-7"><code>#{{ $tenant->id }}</code></dd>
                    <dt class="col-5 text-muted fw-semibold">Restoran</dt>  <dd class="col-7 fw-600" style="font-weight:600">{{ $tenant->restoran_adi }}</dd>
                    <dt class="col-5 text-muted fw-semibold">Kayıt</dt>     <dd class="col-7">{{ \Carbon\Carbon::parse($tenant->created_at)->format('d.m.Y H:i') }}</dd>
                    <dt class="col-5 text-muted fw-semibold">Kullanıcı</dt> <dd class="col-7">{{ $users->count() }}</dd>
                    <dt class="col-5 text-muted fw-semibold">Kategori</dt>  <dd class="col-7">{{ $categories->count() }}</dd>
                    <dt class="col-5 text-muted fw-semibold">Ürün</dt>      <dd class="col-7">{{ $products->count() }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Users --}}
    <div class="col-md-8">
        <div class="sm-card h-100">
            <div class="sm-card-header"><i class="bi bi-people me-1" style="color:#6366f1"></i>Kullanıcılar</div>
            <div class="table-responsive">
                <table class="table sm-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Ad</th>
                            <th>E-posta</th>
                            <th>Rol</th>
                            <th>Kayıt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $u->id }}</td>
                            <td class="fw-semibold small">{{ $u->name }}</td>
                            <td class="text-muted small">{{ $u->email }}</td>
                            <td>
                                @php $roleColors = ['owner'=>'#c2410c','admin'=>'#0369a1','personel'=>'#374151']; @endphp
                                <span class="badge" style="background:rgba(0,0,0,.05);color:{{ $roleColors[$u->role] ?? '#374151' }};font-size:.72rem;border-radius:5px">
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

{{-- Products --}}
<div class="sm-card">
    <div class="sm-card-header"><i class="bi bi-box-seam me-1" style="color:#f59e0b"></i>Ürünler ({{ $products->count() }})</div>
    <div class="table-responsive">
        <table class="table sm-table align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width:50px">Foto</th>
                    <th>Ürün</th>
                    <th>Kategori</th>
                    <th style="width:100px">Fiyat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                <tr>
                    <td class="ps-4">
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
