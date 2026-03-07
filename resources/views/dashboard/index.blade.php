@extends('layouts.app')

@section('title', __('dashboard.title'))
@section('page-title', __('dashboard.title'))
@section('breadcrumb', __('dashboard.breadcrumb'))

@section('content')
@php
    $menuUrl = route('public.menu', ['tenantId' => session('tenant_id')]);
    $user    = auth()->user();
@endphp

{{-- ── QR Banner ── --}}
<div class="mb-4" style="
    background: linear-gradient(135deg, #0d1117 0%, #1a2332 60%, #0f1923 100%);
    border: 1px solid rgba(255,107,53,.2);
    border-radius: 18px;
    padding: 1.4rem 1.6rem;
    position: relative; overflow: hidden;
">
    {{-- Decorative glow --}}
    <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(255,107,53,.12) 0%,transparent 70%);pointer-events:none;"></div>

    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3">
        <div style="width:50px;height:50px;border-radius:13px;background:rgba(255,107,53,.15);border:1px solid rgba(255,107,53,.25);display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:#FF6B35;flex-shrink:0;">
            <i class="bi bi-qr-code"></i>
        </div>
        <div class="flex-grow-1">
            <div style="font-weight:700;font-size:.92rem;color:#fff;margin-bottom:.3rem;">
                {{ __('dashboard.menu_live') }}
                <span style="display:inline-flex;align-items:center;gap:.25rem;font-size:.65rem;font-weight:700;background:rgba(34,197,94,.15);color:#4ade80;border:1px solid rgba(34,197,94,.25);padding:.1rem .45rem;border-radius:999px;margin-left:.4rem;vertical-align:middle;">
                    <span style="width:5px;height:5px;border-radius:50%;background:#4ade80;"></span> {{ __('common.active') }}
                </span>
            </div>
            <code style="color:#FF8C42;font-size:.76rem;word-break:break-all;">{{ $menuUrl }}</code>
        </div>
        <div class="d-flex gap-2 flex-shrink-0">
            <a href="{{ route('menu.qr') }}" class="btn btn-accent btn-sm">
                <i class="bi bi-qr-code me-1"></i>{{ __('dashboard.qr_link') }}
            </a>
            <a href="{{ $menuUrl }}" target="_blank" class="btn btn-sm"
               style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.65);">
                <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </div>
    </div>
</div>

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(99,102,241,.1);">
                <i class="bi bi-people" style="color:#6366f1;"></i>
            </div>
            <div>
                <div class="stat-label">{{ __('dashboard.staff') }}</div>
                <div class="stat-value">{{ $stats['users'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,.1);">
                <i class="bi bi-grid-3x3-gap" style="color:#10b981;"></i>
            </div>
            <div>
                <div class="stat-label">{{ __('dashboard.category') }}</div>
                <div class="stat-value">{{ $stats['categories'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(255,107,53,.1);">
                <i class="bi bi-box-seam" style="color:#FF6B35;"></i>
            </div>
            <div>
                <div class="stat-label">{{ __('dashboard.product') }}</div>
                <div class="stat-value">{{ $stats['products'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Bottom Cards ── --}}
<div class="row g-3">

    {{-- Firma Bilgisi --}}
    <div class="col-md-6">
        <div class="sm-card h-100">
            <div class="sm-card-header">
                <i class="bi bi-building" style="color:#6366f1;"></i>
                {{ __('dashboard.company_info') }}
            </div>
            <div class="sm-card-body">
                <div class="d-flex flex-column gap-2 mb-3">
                    @foreach([['dashboard.company_name', $tenant->firma_adi], ['dashboard.restaurant_name', $tenant->restoran_adi], ['dashboard.menu_id', '#'.$tenant->id]] as [$lbl, $val])
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:.55rem .75rem;background:#f7f8fa;border-radius:9px;border:1px solid #eaecf0;">
                        <span style="font-size:.75rem;font-weight:600;color:#98a2b3;text-transform:uppercase;letter-spacing:.04em;">{{ __($lbl) }}</span>
                        <span style="font-size:.855rem;font-weight:600;color:#101828;">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>
                @if($user->role === 'owner')
                <a href="{{ route('company.edit') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-pencil me-1"></i>{{ __('dashboard.edit_info') }}
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Hızlı İşlemler --}}
    <div class="col-md-6">
        <div class="sm-card h-100">
            <div class="sm-card-header">
                <i class="bi bi-lightning-charge" style="color:#FF6B35;"></i>
                {{ __('dashboard.quick_actions') }}
            </div>
            <div class="sm-card-body d-flex flex-column gap-2">
                <a href="{{ route('categories.create') }}" class="btn btn-sm btn-outline-secondary text-start d-flex align-items-center gap-2">
                    <span style="width:26px;height:26px;border-radius:7px;background:rgba(16,185,129,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-plus-lg" style="color:#10b981;font-size:.75rem;"></i>
                    </span>
                    {{ __('dashboard.add_category') }}
                </a>
                <a href="{{ route('products.create') }}" class="btn btn-sm btn-outline-secondary text-start d-flex align-items-center gap-2">
                    <span style="width:26px;height:26px;border-radius:7px;background:rgba(255,107,53,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-plus-lg" style="color:#FF6B35;font-size:.75rem;"></i>
                    </span>
                    {{ __('dashboard.add_product') }}
                </a>
                @if($user->role === 'owner')
                <a href="{{ route('users.create') }}" class="btn btn-sm btn-outline-secondary text-start d-flex align-items-center gap-2">
                    <span style="width:26px;height:26px;border-radius:7px;background:rgba(99,102,241,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-person-plus" style="color:#6366f1;font-size:.75rem;"></i>
                    </span>
                    {{ __('dashboard.add_staff') }}
                </a>
                @endif
                <div style="height:1px;background:#eaecf0;margin:.25rem 0;"></div>
                <a href="{{ route('menu.qr') }}" class="btn btn-accent btn-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-qr-code"></i> {{ __('dashboard.view_menu_qr') }}
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
