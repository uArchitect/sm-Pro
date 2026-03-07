@extends('layouts.app')

@section('title', __('support.new_ticket'))
@section('page-title', __('support.new_ticket'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-pencil-square me-1" style="color:var(--accent)"></i>{{ __('support.new_ticket') }}
            </div>
            <div class="sm-card-body">
                <form action="{{ route('support.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('support.subject') }}</label>
                        <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                               value="{{ old('subject') }}" placeholder="{{ __('support.subject_placeholder') }}" required>
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label">{{ __('support.message') }}</label>
                        <textarea name="message" rows="6" class="form-control @error('message') is-invalid @enderror"
                                  placeholder="{{ __('support.message_placeholder') }}" required>{{ old('message') }}</textarea>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('support.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('support.back') }}</a>
                        <button type="submit" class="btn btn-accent btn-sm">
                            <i class="bi bi-send me-1"></i>{{ __('support.send') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
