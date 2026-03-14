@extends('layouts.dev')

@section('title', 'Yeni Yazı')
@section('page-title', 'Yeni Blog Yazısı')

@section('content')
<a href="{{ route('developer.blog.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left me-1"></i>Geri
</a>

<div class="sm-card">
    <div class="sm-card-header">
        <i class="bi bi-pencil-square me-1" style="color:#6366f1"></i>Yazı Bilgileri
    </div>
    <div class="sm-card-body">
        <form method="POST" action="{{ route('developer.blog.store') }}" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="mb-3">
                <label class="form-label">Başlık <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title') }}" required maxlength="255">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Slug <span class="text-muted small">(boş bırakırsanız başlıktan üretilir)</span></label>
                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                       value="{{ old('slug') }}" placeholder="ornek-yazi-basligi">
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">İçerik <span class="text-danger">*</span></label>
                <textarea name="body" rows="14" class="form-control @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
                <div class="form-text">HTML kullanabilirsiniz.</div>
                @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Öne Çıkan Görsel</label>
                    <input type="file" name="featured_image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                    @error('featured_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Yayınla</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                        <label class="form-check-label">Yayında göster</label>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            <div class="small text-muted mb-2">SEO (isteğe bağlı)</div>
            <div class="mb-3">
                <label class="form-label">Meta Başlık</label>
                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}" maxlength="255">
            </div>
            <div class="mb-4">
                <label class="form-label">Meta Açıklama</label>
                <textarea name="meta_description" rows="2" class="form-control" maxlength="500">{{ old('meta_description') }}</textarea>
            </div>

            <button type="submit" class="btn btn-accent">
                <i class="bi bi-check-lg me-1"></i>Kaydet
            </button>
            <a href="{{ route('developer.blog.index') }}" class="btn btn-outline-secondary">İptal</a>
        </form>
    </div>
</div>
@endsection
