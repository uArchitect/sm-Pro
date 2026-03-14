@extends('layouts.dev')

@section('title', 'Yazıyı Düzenle')
@section('page-title', 'Blog Yazısını Düzenle')

@section('content')
<a href="{{ route('developer.blog.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left me-1"></i>Geri
</a>

<div class="sm-card">
    <div class="sm-card-header">
        <i class="bi bi-pencil-square me-1" style="color:#6366f1"></i>{{ Str::limit($post->title, 50) }}
    </div>
    <div class="sm-card-body">
        <form method="POST" action="{{ route('developer.blog.update', $post->id) }}" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Başlık <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $post->title) }}" required maxlength="255">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Slug</label>
                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                       value="{{ old('slug', $post->slug) }}">
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">İçerik <span class="text-danger">*</span></label>
                <textarea name="body" id="blogBody" rows="14" class="form-control @error('body') is-invalid @enderror" required>{{ old('body', $post->body) }}</textarea>
                @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Öne Çıkan Görsel</label>
                    <div id="featuredImagePreviewEdit" class="mb-2 rounded overflow-hidden bg-light" style="max-height:140px">
                        @if($post->featured_image)
                            <img id="featuredImagePreviewImgEdit" src="{{ asset('uploads/'.$post->featured_image) }}?v={{ time() }}" alt="" class="img-fluid w-100" style="object-fit:contain;max-height:140px">
                        @else
                            <img id="featuredImagePreviewImgEdit" src="" alt="" class="img-fluid w-100" style="object-fit:contain;max-height:140px;display:none">
                        @endif
                    </div>
                    <input type="file" name="featured_image" id="featuredImageInputEdit" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,image/jpeg,image/png,image/gif,image/webp,image/svg+xml">
                    @error('featured_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Yayınla</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" {{ old('is_published', $post->is_published) ? 'checked' : '' }}>
                        <label class="form-check-label">Yayında göster</label>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            <div class="small text-muted mb-2">SEO</div>
            <div class="mb-3">
                <label class="form-label">Meta Başlık</label>
                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $post->meta_title) }}" maxlength="255">
            </div>
            <div class="mb-4">
                <label class="form-label">Meta Açıklama</label>
                <textarea name="meta_description" rows="2" class="form-control" maxlength="500">{{ old('meta_description', $post->meta_description) }}</textarea>
            </div>

            <button type="submit" class="btn btn-accent">
                <i class="bi bi-check-lg me-1"></i>Güncelle
            </button>
            <a href="{{ route('developer.blog.index') }}" class="btn btn-outline-secondary">İptal</a>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/4.22.1/standard-all/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof CKEDITOR === 'undefined') return;
    CKEDITOR.replace('blogBody', {
        height: 320,
        language: 'tr',
        removePlugins: 'elementspath',
        resize_enabled: false
    });
});
(function() {
    var input = document.getElementById('featuredImageInputEdit');
    var img = document.getElementById('featuredImagePreviewImgEdit');
    if (!input || !img) return;
    input.addEventListener('change', function() {
        var file = this.files && this.files[0];
        if (!file) return;
        var url = URL.createObjectURL(file);
        img.src = url;
        img.style.display = 'block';
        img.onload = function() { URL.revokeObjectURL(url); };
    });
})();
</script>
@endpush
