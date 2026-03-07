@extends('layouts.app')

@section('title', __('tenant.edit_title'))
@section('page-title', __('tenant.edit_title'))
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <form method="POST" action="{{ route('company.update') }}" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            {{-- Logo --}}
            <div class="sm-card mb-4">
                <div class="sm-card-header">
                    <i class="bi bi-image" style="color:#FF6B35"></i>{{ __('tenant.logo_section') }}
                </div>
                <div class="sm-card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div id="logoPreviewWrap" style="width:72px;height:72px;border-radius:14px;border:2px dashed #e5e7eb;display:flex;align-items:center;justify-content:center;overflow:hidden;cursor:pointer;flex-shrink:0;background:#fafafa" onclick="document.getElementById('logoInput').click()">
                            @if($tenant->logo)
                                <img id="logoPreview" src="{{ asset('storage/'.$tenant->logo) }}" style="width:100%;height:100%;object-fit:cover;border-radius:12px">
                            @else
                                <img id="logoPreview" src="" style="width:100%;height:100%;object-fit:cover;border-radius:12px;display:none">
                                <i class="bi bi-cloud-upload" id="logoPlaceholder" style="font-size:1.5rem;color:#d1d5db"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small">{{ __('tenant.logo_label') }}</div>
                            <div class="text-muted" style="font-size:.75rem">{{ __('tenant.logo_hint') }}</div>
                            <input type="file" id="logoInput" name="logo" accept="image/*" class="d-none">
                            @if($tenant->logo)
                            <label class="form-check mt-2 mb-0">
                                <input type="checkbox" name="remove_logo" value="1" class="form-check-input" style="width:1rem;height:1rem">
                                <span class="form-check-label small text-danger">{{ __('tenant.logo_remove') }}</span>
                            </label>
                            @endif
                        </div>
                    </div>
                    @error('logo')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Siparis Toggle --}}
            <div class="sm-card mb-4">
                <div class="sm-card-header">
                    <i class="bi bi-bag-check" style="color:#10b981"></i>{{ __('tenant.ordering_section') }}
                </div>
                <div class="sm-card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="fw-semibold small">{{ __('tenant.ordering_label') }}</div>
                            <div class="text-muted" style="font-size:.78rem">{{ __('tenant.ordering_hint') }}</div>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="ordering_enabled" value="1"
                                   id="orderingSwitch" style="width:2.8rem;height:1.4rem;cursor:pointer"
                                   {{ old('ordering_enabled', $tenant->ordering_enabled ?? false) ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Firma Bilgileri --}}
            <div class="sm-card mb-4">
                <div class="sm-card-header">
                    <i class="bi bi-building" style="color:#6366f1"></i>{{ __('tenant.edit_header') }}
                </div>
                <div class="sm-card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('tenant.restaurant_name') }}</label>
                        <input type="text" name="restoran_adi"
                               class="form-control @error('restoran_adi') is-invalid @enderror"
                               value="{{ old('restoran_adi', $tenant->restoran_adi) }}" required>
                        @error('restoran_adi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('tenant.address') }}</label>
                        <input type="text" name="restoran_adresi"
                               class="form-control @error('restoran_adresi') is-invalid @enderror"
                               value="{{ old('restoran_adresi', $tenant->restoran_adresi) }}" required>
                        @error('restoran_adresi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small">{{ __('tenant.phone') }}</label>
                        <input type="text" name="restoran_telefonu"
                               class="form-control @error('restoran_telefonu') is-invalid @enderror"
                               value="{{ old('restoran_telefonu', $tenant->restoran_telefonu) }}" required>
                        @error('restoran_telefonu')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Sosyal Medya --}}
            <div class="sm-card mb-4">
                <div class="sm-card-header">
                    <i class="bi bi-share" style="color:#FF6B35"></i>{{ __('tenant.social_section') }}
                </div>
                <div class="sm-card-body">
                    <p class="text-muted small mb-3">{{ __('tenant.social_hint') }}</p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            <i class="bi bi-instagram me-1" style="color:#E4405F"></i>Instagram
                        </label>
                        <div class="input-group">
                            <span class="input-group-text" style="font-size:.8rem">instagram.com/</span>
                            <input type="text" name="instagram" class="form-control"
                                   value="{{ old('instagram', $tenant->instagram ?? '') }}"
                                   placeholder="{{ __('tenant.social_placeholder') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            <i class="bi bi-facebook me-1" style="color:#1877F2"></i>Facebook
                        </label>
                        <div class="input-group">
                            <span class="input-group-text" style="font-size:.8rem">facebook.com/</span>
                            <input type="text" name="facebook" class="form-control"
                                   value="{{ old('facebook', $tenant->facebook ?? '') }}"
                                   placeholder="{{ __('tenant.social_placeholder') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            <i class="bi bi-twitter-x me-1"></i>X (Twitter)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text" style="font-size:.8rem">x.com/</span>
                            <input type="text" name="twitter" class="form-control"
                                   value="{{ old('twitter', $tenant->twitter ?? '') }}"
                                   placeholder="{{ __('tenant.social_placeholder') }}">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small">
                            <i class="bi bi-whatsapp me-1" style="color:#25D366"></i>WhatsApp
                        </label>
                        <div class="input-group">
                            <span class="input-group-text" style="font-size:.8rem">+90</span>
                            <input type="text" name="whatsapp" class="form-control"
                                   value="{{ old('whatsapp', $tenant->whatsapp ?? '') }}"
                                   placeholder="5XX XXX XX XX">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-accent">
                    <i class="bi bi-check-lg me-1"></i>{{ __('common.save') }}
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('logoInput').addEventListener('change', function() {
    if (!this.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById('logoPreview');
        img.src = e.target.result;
        img.style.display = 'block';
        const ph = document.getElementById('logoPlaceholder');
        if (ph) ph.style.display = 'none';
    };
    reader.readAsDataURL(this.files[0]);
});
</script>
@endpush
@endsection
