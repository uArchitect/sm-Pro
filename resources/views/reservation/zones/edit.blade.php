@extends('layouts.app')

@section('title', __('reservation.zone_edit'))
@section('page-title', __('reservation.zone_edit'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-pencil-square me-1" style="color:var(--accent)"></i>{{ __('reservation.zone_edit') }}
            </div>
            <div class="sm-card-body">
                <form action="{{ route('reservation.zones.update', $zone->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="form-label">{{ __('reservation.zone_name') }} *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $zone->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('reservation.zones.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('support.back') }}</a>
                        <button type="submit" class="btn btn-accent btn-sm">
                            <i class="bi bi-check-lg me-1"></i>{{ __('reservation.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
