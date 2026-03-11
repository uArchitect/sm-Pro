@extends('layouts.app')

@section('title', __('categories.add'))
@section('page-title', __('categories.add'))
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-plus-circle" style="color:#FF6B35"></i>{{ __('categories.new_category') }}
            </div>
            <div class="sm-card-body">
                <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data" novalidate>
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">{{ __('categories.photo_optional') }} <span class="text-muted">{{ __('common.optional') }}</span></label>
                        <div class="img-upload-zone" id="imgZone" onclick="document.getElementById('imgInput').click()">
                            <img id="imgPreview" src="" class="img-preview d-none">
                            <div id="imgPlaceholder" class="text-center">
                                <i class="bi bi-cloud-upload fs-2" style="color:#d1d5db"></i>
                                <div class="mt-1 small" style="color:#9ca3af">{{ __('common.click_or_drop') }}</div>
                                <div style="font-size:.72rem;color:#d1d5db">{{ __('common.max_file_size') }}</div>
                            </div>
                        </div>
                        <input type="file" id="imgInput" name="image" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,image/jpeg,image/png,image/gif,image/webp,image/svg+xml" class="d-none @error('image') is-invalid @enderror">
                        @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <button type="button" class="btn btn-link btn-sm text-danger p-0 mt-1 d-none" id="removeBtn" onclick="removeImg()">
                            <i class="bi bi-x-circle me-1"></i>{{ __('common.remove_photo') }}
                        </button>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">{{ __('categories.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="{{ __('categories.name_placeholder') }}" required autofocus>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">{{ __('categories.parent_category') }} <span class="text-muted">{{ __('common.optional') }}</span></label>
                        <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">{{ __('categories.parent_none') }}</option>
                            @foreach($parents as $p)
                            <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">{{ __('categories.parent_hint') }}</div>
                        @error('parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2 form-actions-wrap">
                        <button type="submit" class="btn btn-accent">
                            <i class="bi bi-check-lg me-1"></i>{{ __('categories.add_btn') }}
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('imgInput').addEventListener('change', function() {
    if (!this.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('imgPreview').src = e.target.result;
        document.getElementById('imgPreview').classList.remove('d-none');
        document.getElementById('imgPlaceholder').classList.add('d-none');
        document.getElementById('removeBtn').classList.remove('d-none');
    };
    reader.readAsDataURL(this.files[0]);
});

const zone = document.getElementById('imgZone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor='#FF6B35'; });
zone.addEventListener('dragleave', () => zone.style.borderColor='');
zone.addEventListener('drop', e => {
    e.preventDefault(); zone.style.borderColor='';
    const dt = new DataTransfer();
    dt.items.add(e.dataTransfer.files[0]);
    document.getElementById('imgInput').files = dt.files;
    document.getElementById('imgInput').dispatchEvent(new Event('change'));
});

function removeImg() {
    document.getElementById('imgInput').value = '';
    document.getElementById('imgPreview').classList.add('d-none');
    document.getElementById('imgPlaceholder').classList.remove('d-none');
    document.getElementById('removeBtn').classList.add('d-none');
}
</script>
@endpush

<style>
.img-upload-zone {
    border:2px dashed #e5e7eb; border-radius:12px; padding:1.5rem;
    text-align:center; cursor:pointer; transition:border-color .15s,background .15s;
    min-height:120px; display:flex; align-items:center; justify-content:center;
}
.img-upload-zone:hover { border-color:#FF6B35; background:#fff8f5; }
.img-preview { max-width:100%; max-height:180px; border-radius:8px; object-fit:contain; }
</style>
@endsection
