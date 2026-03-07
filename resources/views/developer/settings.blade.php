@extends('layouts.dev')

@section('title', 'Developer Ayarları')
@section('page-title', 'Hesap Ayarları')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-gear me-1" style="color:#ef4444"></i>Developer Hesap Ayarları
            </div>
            <div class="sm-card-body">
                <form method="POST" action="{{ route('developer.settings.update') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Ad Soyad</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $dev->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">E-posta</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $dev->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <hr class="my-4">
                    <div class="text-muted small mb-3">Şifreyi değiştirmek istemiyorsanız boş bırakın.</div>

                    <div class="mb-3">
                        <label class="form-label">Yeni Şifre</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                               placeholder="En az 8 karakter">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Şifre Tekrar</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Şifreyi tekrar girin">
                    </div>

                    <button type="submit" class="btn btn-accent w-100">
                        <i class="bi bi-check-lg me-1"></i>Ayarları Güncelle
                    </button>
                </form>
            </div>
        </div>

        <div class="sm-card mt-3">
            <div class="sm-card-body">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:42px;height:42px;border-radius:10px;background:rgba(239,68,68,.1);color:#ef4444;display:flex;align-items:center;justify-content:center;font-size:1.1rem">
                        <i class="bi bi-shield-fill-check"></i>
                    </div>
                    <div>
                        <div class="fw-600" style="font-weight:600;font-size:.88rem">Rol: Developer</div>
                        <div class="text-muted" style="font-size:.78rem">Tüm tenant verilerine okuma/yazma erişimi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
