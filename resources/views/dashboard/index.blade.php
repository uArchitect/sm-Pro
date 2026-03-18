@extends('layouts.app')

@section('title', __('dashboard.title'))
@section('page-title', __('dashboard.title'))
@section('breadcrumb', __('dashboard.breadcrumb'))
@section('page-help', __('dashboard.page_help'))

@section('content')
@php
    $menuUrl = route('public.menu', ['tenantId' => session('tenant_id')]);
    $user    = auth()->user();
@endphp

{{-- QR Banner --}}
<div class="mb-4" style="
    background: linear-gradient(135deg, #4F46E5 0%, #6366F1 60%, #4338CA 100%);
    border: none;
    border-radius: 18px;
    padding: 1.4rem 1.6rem;
    position: relative; overflow: hidden;
">
    <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(79,70,229,.12) 0%,transparent 70%);pointer-events:none;"></div>

    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3 flex-wrap">
        <div style="width:50px;height:50px;border-radius:13px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.25);display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:#fff;flex-shrink:0;">
            <i class="bi bi-qr-code"></i>
        </div>
        <div class="flex-grow-1" style="min-width:0">
            <div style="font-weight:700;font-size:.92rem;color:#fff;margin-bottom:.3rem;">
                {{ __('dashboard.menu_live') }}
                <span style="display:inline-flex;align-items:center;gap:.25rem;font-size:.65rem;font-weight:700;background:rgba(34,197,94,.15);color:#4ade80;border:1px solid rgba(34,197,94,.25);padding:.1rem .45rem;border-radius:999px;margin-left:.4rem;vertical-align:middle;">
                    <span style="width:5px;height:5px;border-radius:50%;background:#4ade80;"></span> {{ __('common.active') }}
                </span>
            </div>
            <code style="color:rgba(255,255,255,.8);font-size:.76rem;word-break:break-all;display:block;">{{ $menuUrl }}</code>
        </div>
        <div class="d-flex gap-2 flex-shrink-0 flex-wrap">
            <a href="{{ route('menu.qr') }}" class="btn btn-accent btn-sm flex-grow-1 flex-sm-grow-0">
                <i class="bi bi-qr-code me-1"></i>{{ __('dashboard.qr_link') }}  
            </a>
            <a href="{{ $menuUrl }}" target="_blank" class="btn btn-sm"
               style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.65);">
                <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(168,85,247,.1);">
                <i class="bi bi-eye" style="color:#A855F7;"></i>
            </div>
            <div>
                <div class="stat-label">{{ __('dashboard.qr_visits') }}</div>
                <div class="stat-value">{{ $stats['qr_total'] }}</div>
                <div style="font-size:.68rem;color:#98a2b3;margin-top:.15rem;">
                    {{ __('dashboard.today') }}: <strong style="color:#A855F7">{{ $stats['qr_today'] }}</strong>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(251,191,36,.1);">
                <i class="bi bi-star-fill" style="color:#FBBF24;"></i>
            </div>
            <div>
                <div class="stat-label">{{ __('dashboard.reviews') }}</div>
                <div class="stat-value">{{ $stats['reviews_avg'] }}<span style="font-size:.8rem;color:#FBBF24">/5</span></div>
                <div style="font-size:.68rem;color:#98a2b3;margin-top:.15rem;">
                    {{ __('dashboard.total_reviews', ['count' => $stats['reviews_count']]) }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
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
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(79,70,229,.1);">
                <i class="bi bi-box-seam" style="color:#4F46E5;"></i>
            </div>
            <div>
                <div class="stat-label">{{ __('dashboard.product') }}</div>
                <div class="stat-value">{{ $stats['products'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Setup Guide --}}
@if(session('just_registered') || !$setup['completed'])
<div class="mb-4" style="
    background:#fff;
    border:1px solid #eaecf0;
    border-radius:16px;
    padding:1rem 1.1rem;
    box-shadow:0 1px 4px rgba(16,24,40,.06);
">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <div class="d-flex align-items-center gap-2">
            <span style="width:32px;height:32px;border-radius:10px;background:rgba(99,102,241,.1);display:flex;align-items:center;justify-content:center;color:#6366f1;">
                <i class="bi bi-flag"></i>
            </span>
            <div>
                <div style="font-size:.9rem;font-weight:700;color:#111827;">{{ __('dashboard.setup_title') }}</div>
                <div style="font-size:.76rem;color:#6b7280;">{{ __('dashboard.setup_sub', ['done' => $setup['progress'], 'total' => 2]) }}</div>
            </div>
        </div>
        @if($setup['completed'])
            <span class="badge text-bg-success">{{ __('dashboard.setup_done') }}</span>
        @else
            <span class="badge text-bg-warning">{{ __('dashboard.setup_pending') }}</span>
        @endif
    </div>

    <div class="d-flex flex-column gap-2">
        <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background:#f8fafc;border:1px solid #eef2f7;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi {{ $setup['has_category'] ? 'bi-check-circle-fill text-success' : 'bi-circle text-secondary' }}"></i>
                <span style="font-size:.84rem;color:#1f2937;">{{ __('dashboard.setup_step_category') }}</span>
            </div>
            @if(!$setup['has_category'])
                <a href="{{ route('categories.create') }}" class="btn btn-sm btn-outline-secondary">{{ __('dashboard.setup_go_category') }}</a>
            @endif
        </div>

        <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background:#f8fafc;border:1px solid #eef2f7;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi {{ $setup['has_product'] ? 'bi-check-circle-fill text-success' : 'bi-circle text-secondary' }}"></i>
                <span style="font-size:.84rem;color:#1f2937;">{{ __('dashboard.setup_step_product') }}</span>
            </div>
            @if(!$setup['has_product'])
                <a href="{{ route('products.create') }}" class="btn btn-sm btn-outline-secondary">{{ __('dashboard.setup_go_product') }}</a>
            @endif
        </div>
    </div>
</div>
@endif

{{-- Bottom Cards --}}
<div class="row g-3">
    <div class="col-md-6">
        <div class="sm-card h-100">
            <div class="sm-card-header">
                <i class="bi bi-building" style="color:#6366f1;"></i>
                {{ __('dashboard.company_info') }}
            </div>
            <div class="sm-card-body">
                <div class="d-flex flex-column gap-2 mb-3">
                    @foreach([['dashboard.restaurant_name', $tenant->restoran_adi], ['dashboard.menu_id', '#'.$tenant->id]] as [$lbl, $val])
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

    <div class="col-md-6">
        <div class="sm-card h-100">
            <div class="sm-card-header">
                <i class="bi bi-lightning-charge" style="color:#4F46E5;"></i>
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
                    <span style="width:26px;height:26px;border-radius:7px;background:rgba(79,70,229,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-plus-lg" style="color:#4F46E5;font-size:.75rem;"></i>
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
