@extends('layouts.app')

@section('title', __('reservation.table_add'))
@section('page-title', __('reservation.table_add'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-table me-1" style="color:var(--accent)"></i>{{ __('reservation.table_add') }}
            </div>
            <div class="sm-card-body">
                <form action="{{ route('reservation.tables.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('reservation.zone_name') }} *</label>
                        <select name="zone_id" class="form-select @error('zone_id') is-invalid @enderror" required>
                            <option value="">{{ __('reservation.select_zone') }}</option>
                            @foreach($zones as $z)
                            <option value="{{ $z->id }}" {{ old('zone_id') == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                            @endforeach
                        </select>
                        @error('zone_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('reservation.table_name') }} *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="{{ __('reservation.table_name_placeholder') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label">{{ __('reservation.capacity') }}</label>
                        <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror"
                               value="{{ old('capacity', 2) }}" min="1" max="99" style="max-width:100px">
                        @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">{{ __('reservation.capacity_hint') }}</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('reservation.tables.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('support.back') }}</a>
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
