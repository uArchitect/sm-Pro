@extends('layouts.app')

@section('title', __('events.edit'))
@section('page-title', __('events.edit'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-pencil-square me-1" style="color:var(--accent)"></i>{{ __('events.edit') }}
            </div>
            <div class="sm-card-body">
                <form action="{{ route('events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">{{ __('events.event_title') }} *</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $event->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('events.description') }}</label>
                        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $event->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('events.image') }}</label>
                        @if($event->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $event->image) }}" alt="" style="max-height:120px;border-radius:8px">
                        </div>
                        @endif
                        <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label">{{ __('events.start_date') }} *</label>
                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                   value="{{ old('start_date', $event->start_date) }}" required>
                            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{ __('events.end_date') }}</label>
                            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                                   value="{{ old('end_date', $event->end_date) }}">
                            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                   {{ old('is_active', $event->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive" style="font-size:.85rem;font-weight:600">{{ __('events.active') }}</label>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('support.back') }}</a>
                        <button type="submit" class="btn btn-accent btn-sm">
                            <i class="bi bi-check-lg me-1"></i>{{ __('events.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
