@extends('layouts.app')

@section('title', __('tenant.edit_title'))
@section('page-title', __('tenant.edit_title'))
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-building" style="color:#6366f1"></i>{{ __('tenant.edit_header') }}
            </div>
            <div class="sm-card-body">
                <form method="POST" action="{{ route('company.update') }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('tenant.company_name') }}</label>
                        <input type="text" name="firma_adi"
                               class="form-control @error('firma_adi') is-invalid @enderror"
                               value="{{ old('firma_adi', $tenant->firma_adi) }}" required>
                        @error('firma_adi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">{{ __('tenant.restaurant_name') }}</label>
                        <input type="text" name="restoran_adi"
                               class="form-control @error('restoran_adi') is-invalid @enderror"
                               value="{{ old('restoran_adi', $tenant->restoran_adi) }}" required>
                        @error('restoran_adi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">{{ __('tenant.address') }}</label>
                        <input type="text" name="restoran_adresi"
                               class="form-control @error('restoran_adresi') is-invalid @enderror"
                               value="{{ old('restoran_adresi', $tenant->restoran_adresi) }}" required>
                        @error('restoran_adresi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">{{ __('tenant.phone') }}</label>
                        <input type="text" name="restoran_telefonu"
                               class="form-control @error('restoran_telefonu') is-invalid @enderror"
                               value="{{ old('restoran_telefonu', $tenant->restoran_telefonu) }}" required>
                        @error('restoran_telefonu')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
    </div>
</div>
@endsection
