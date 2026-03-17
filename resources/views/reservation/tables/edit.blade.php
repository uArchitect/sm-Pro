@extends('layouts.app')

@section('title', __('reservation.table_edit'))
@section('page-title', __('reservation.table_edit'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-pencil-square me-1" style="color:var(--accent)"></i>{{ __('reservation.table_edit') }}
            </div>
            <div class="sm-card-body">
                <form action="{{ route('reservation.tables.update', $table->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">{{ __('reservation.zone_name') }} *</label>
                        <select name="zone_id" class="form-select @error('zone_id') is-invalid @enderror" required>
                            @foreach($zones as $z)
                            <option value="{{ $z->id }}" {{ old('zone_id', $table->zone_id) == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                            @endforeach
                        </select>
                        @error('zone_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('reservation.table_name') }} *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $table->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label">{{ __('reservation.capacity') }}</label>
                        <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror"
                               value="{{ old('capacity', $table->capacity) }}" min="1" max="99" style="max-width:100px">
                        @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('reservation.tables.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('common.back') }}</a>
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
