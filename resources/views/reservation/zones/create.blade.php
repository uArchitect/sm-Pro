@extends('layouts.app')

@section('title', __('reservation.zone_add'))
@section('page-title', __('reservation.zone_add'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-pin-map me-1" style="color:var(--accent)"></i>{{ __('reservation.zone_add') }}
            </div>
            <div class="sm-card-body">
                <form action="{{ route('reservation.zones.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label">{{ __('reservation.zone_name') }} *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="{{ __('reservation.zone_name_placeholder') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">{{ __('reservation.zone_name_hint') }}</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('reservation.zones.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('support.back') }}</a>
                        <button type="submit" class="btn btn-accent btn-sm">
                            <i class="bi bi-check-lg me-1"></i>{{ __('support.send') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
