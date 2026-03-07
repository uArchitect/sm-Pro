@extends('layouts.app')

@section('title', __('users.add'))
@section('page-title', __('users.add'))
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-person-plus" style="color:#6366f1"></i>{{ __('users.add_new') }}
            </div>
            <div class="sm-card-body">
                <form method="POST" action="{{ route('users.store') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('users.full_name') }}</label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('users.email') }}</label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('users.password') }}</label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="{{ __('auth.password_min') }}" required minlength="8">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('users.password_confirm') }}</label>
                        <input type="password" name="password_confirmation"
                               class="form-control" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">{{ __('users.role') }}</label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">{{ __('users.role_select') }}</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>{{ __('nav.roles.admin') }}</option>
                            <option value="personel" {{ old('role') === 'personel' ? 'selected' : '' }}>{{ __('nav.roles.personel') }}</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2 form-actions-wrap">
                        <button type="submit" class="btn btn-accent">
                            <i class="bi bi-check-lg me-1"></i>{{ __('users.add') }}
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
