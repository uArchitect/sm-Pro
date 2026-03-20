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
    padding: 1.15rem 1.25rem;
    position: relative; overflow: hidden;
    box-shadow: 0 10px 30px rgba(79,70,229,.18);
">
    <div style="position:absolute;top:-55px;right:-55px;width:210px;height:210px;border-radius:50%;background:radial-gradient(circle,rgba(79,70,229,.18) 0%,transparent 70%);pointer-events:none;"></div>

    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3 flex-wrap">
        <div style="width:50px;height:50px;border-radius:13px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.25);display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:#fff;flex-shrink:0;">
            <i class="bi bi-qr-code"></i>
        </div>
        <div class="flex-grow-1" style="min-width:0">
            <div style="font-weight:700;font-size:.92rem;color:#fff;margin-bottom:.3rem;">
                {{ __('dashboard.menu_live') }}
                <span style="display:inline-flex;align-items:center;gap:.25rem;font-size:.65rem;font-weight:800;background:rgba(34,197,94,.15);color:#4ade80;border:1px solid rgba(34,197,94,.25);padding:.12rem .5rem;border-radius:999px;margin-left:.4rem;vertical-align:middle;line-height:1;white-space:nowrap;">
                    <span style="width:5px;height:5px;border-radius:50%;background:#4ade80;"></span> {{ __('common.active') }}
                </span>
            </div>
            <code style="color:rgba(255,255,255,.88);font-size:.76rem;word-break:break-all;display:block;
                background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);
                padding:.45rem .65rem;border-radius:12px;">{{ $menuUrl }}</code>
            @if(!empty($tenant->short_link))
            <div style="margin-top:.35rem;display:flex;align-items:center;gap:.5rem;">
                <span style="font-size:.68rem;color:rgba(255,255,255,.5);white-space:nowrap;flex-shrink:0;">Kısa:</span>
                <code style="color:#4ade80;font-size:.76rem;background:rgba(255,255,255,.06);border:1px solid rgba(74,222,128,.2);padding:.3rem .6rem;border-radius:10px;flex-grow:1;word-break:break-all;">{{ $tenant->short_link }}</code>
                <button onclick="copyDashboardShortLink('{{ $tenant->short_link }}')" id="dashCopyShortBtn"
                    style="background:rgba(74,222,128,.12);border:1px solid rgba(74,222,128,.25);color:#4ade80;border-radius:8px;padding:.3rem .55rem;font-size:.72rem;cursor:pointer;flex-shrink:0;line-height:1;">
                    <i class="bi bi-clipboard" id="dashCopyShortIcon"></i>
                </button>
            </div>
            @endif
        </div>
        <div class="d-flex gap-2 flex-shrink-0 flex-wrap">
            <a href="{{ route('menu.qr') }}" class="btn btn-accent btn-sm flex-grow-1 flex-sm-grow-0" style="white-space:nowrap">
                <i class="bi bi-qr-code me-1"></i>{{ __('dashboard.qr_link') }}
            </a>
            <button type="button" onclick="openMenuPreview()" class="btn btn-sm"
               style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;
                      display:inline-flex;align-items:center;gap:.35rem;
                      height:38px;border-radius:12px;padding:0 .75rem;font-size:.8rem;white-space:nowrap;">
                <i class="bi bi-eye"></i><span class="d-none d-sm-inline">{{ __('dashboard.preview_menu') }}</span>
            </button>
            <a href="{{ $menuUrl }}" target="_blank" class="btn btn-sm"
               style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.65);
                      display:inline-flex;align-items:center;justify-content:center;
                      width:42px;height:38px;border-radius:12px;padding:0;">
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
<div class="mb-4" style="background:#fff;border:1px solid #eaecf0;border-radius:16px;padding:1rem 1.1rem;box-shadow:0 1px 4px rgba(16,24,40,.06);">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <span style="width:32px;height:32px;border-radius:10px;background:rgba(99,102,241,.1);display:flex;align-items:center;justify-content:center;color:#6366f1;">
                <i class="bi bi-flag"></i>
            </span>
            <div>
                <div style="font-size:.9rem;font-weight:700;color:#111827;">{{ __('dashboard.setup_title') }}</div>
                <div style="font-size:.76rem;color:#6b7280;">{{ __('dashboard.setup_sub', ['done' => $setup['progress'], 'total' => $setup['total']]) }}</div>
            </div>
        </div>
        @if($setup['completed'])
            <span class="badge text-bg-success"><i class="bi bi-check-lg me-1"></i>{{ __('dashboard.setup_done') }}</span>
        @else
            <span class="badge text-bg-warning">{{ __('dashboard.setup_pending') }}</span>
        @endif
    </div>

    {{-- Progress bar --}}
    <div style="height:5px;background:#eaecf0;border-radius:99px;margin-bottom:.85rem;overflow:hidden;">
        <div style="height:100%;width:{{ ($setup['progress'] / $setup['total']) * 100 }}%;background:linear-gradient(90deg,#6366f1,#4f46e5);border-radius:99px;transition:width .4s;"></div>
    </div>

    <div class="d-flex flex-column gap-2">
        @php
        $steps = [
            ['done' => $setup['has_category'], 'label' => __('dashboard.setup_step_category'), 'btn' => __('dashboard.setup_go_category'), 'href' => route('categories.create')],
            ['done' => $setup['has_product'],  'label' => __('dashboard.setup_step_product'),  'btn' => __('dashboard.setup_go_product'),  'href' => route('products.create')],
            ['done' => $setup['has_logo'],     'label' => __('dashboard.setup_step_logo'),     'btn' => __('dashboard.setup_go_logo'),     'href' => route('company.edit')],
            ['done' => $setup['has_social'],   'label' => __('dashboard.setup_step_social'),   'btn' => __('dashboard.setup_go_social'),   'href' => route('company.edit')],
        ];
        @endphp
        @foreach($steps as $i => $step)
        <div class="d-flex align-items-center justify-content-between p-2 rounded"
             style="background:{{ $step['done'] ? 'rgba(16,185,129,.04)' : '#f8fafc' }};border:1px solid {{ $step['done'] ? 'rgba(16,185,129,.15)' : '#eef2f7' }};">
            <div class="d-flex align-items-center gap-2">
                <span style="width:20px;height:20px;border-radius:50%;background:{{ $step['done'] ? 'rgba(16,185,129,.15)' : 'rgba(99,102,241,.08)' }};display:flex;align-items:center;justify-content:center;font-size:.7rem;color:{{ $step['done'] ? '#10b981' : '#94a3b8' }};flex-shrink:0;">
                    @if($step['done'])
                        <i class="bi bi-check-lg"></i>
                    @else
                        <span style="font-weight:700;">{{ $i + 1 }}</span>
                    @endif
                </span>
                <span style="font-size:.84rem;color:{{ $step['done'] ? '#6b7280' : '#1f2937' }};{{ $step['done'] ? 'text-decoration:line-through;' : '' }}">{{ $step['label'] }}</span>
            </div>
            @if(!$step['done'])
                <a href="{{ $step['href'] }}" class="btn btn-sm btn-outline-secondary" style="font-size:.76rem;padding:.2rem .65rem;">{{ $step['btn'] }}</a>
            @else
                <i class="bi bi-check-circle-fill text-success" style="font-size:.9rem;"></i>
            @endif
        </div>
        @endforeach
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
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:.55rem .75rem;background:#f7f8fa;border-radius:9px;border:1px solid #eaecf0;">
                        <span style="font-size:.75rem;font-weight:600;color:#98a2b3;text-transform:uppercase;letter-spacing:.04em;">{{ __('dashboard.restaurant_name') }}</span>
                        <span style="font-size:.855rem;font-weight:600;color:#101828;">{{ $tenant->restoran_adi }}</span>
                    </div>
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
{{-- En Çok Görüntülenen Ürünler --}}
@if($topProducts->isNotEmpty())
<div class="row g-3 mt-0">
    <div class="col-12">
        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-bar-chart-line" style="color:#A855F7;"></i>
                En Çok Görüntülenen Ürünler
            </div>
            <div class="sm-card-body p-0">
                @php $maxViews = $topProducts->first()->view_count; @endphp
                @foreach($topProducts as $i => $prod)
                <div style="display:flex;align-items:center;gap:.75rem;padding:.6rem 1rem;{{ !$loop->last ? 'border-bottom:1px solid #f1f5f9;' : '' }}">
                    <span style="width:20px;height:20px;border-radius:50%;background:{{ $i === 0 ? 'linear-gradient(135deg,#A855F7,#7C3AED)' : 'rgba(99,102,241,.08)' }};display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:800;color:{{ $i === 0 ? '#fff' : '#6366f1' }};flex-shrink:0;">{{ $i + 1 }}</span>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:.82rem;font-weight:600;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $prod->name }}</div>
                        <div style="height:4px;background:#f1f5f9;border-radius:2px;margin-top:.3rem;overflow:hidden;">
                            <div style="height:100%;width:{{ round(($prod->view_count / $maxViews) * 100) }}%;background:{{ $i === 0 ? 'linear-gradient(90deg,#A855F7,#7C3AED)' : 'linear-gradient(90deg,#6366f1,#4f46e5)' }};border-radius:2px;transition:width .4s;"></div>
                        </div>
                    </div>
                    <span style="font-size:.78rem;font-weight:700;color:{{ $i === 0 ? '#A855F7' : '#6366f1' }};flex-shrink:0;min-width:32px;text-align:right;">{{ $prod->view_count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

{{-- Menü Önizleme Modalı --}}
<div id="menuPreviewOverlay" onclick="closeMenuPreview()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1055;backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:1rem;">
    <div onclick="event.stopPropagation()" style="background:#fff;border-radius:20px;box-shadow:0 24px 80px rgba(0,0,0,.3);width:100%;max-width:420px;height:85vh;display:flex;flex-direction:column;overflow:hidden;">
        <div style="padding:.75rem 1rem;border-bottom:1px solid #eaecf0;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:.6rem;">
                <div style="width:10px;height:10px;border-radius:50%;background:#ef4444;"></div>
                <div style="width:10px;height:10px;border-radius:50%;background:#f59e0b;"></div>
                <div style="width:10px;height:10px;border-radius:50%;background:#10b981;"></div>
                <span style="font-size:.78rem;color:#64748b;margin-left:.5rem;font-weight:500;">{{ __('dashboard.preview_menu') }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:.5rem;">
                <a href="{{ $menuUrl }}" target="_blank" style="font-size:.78rem;color:#6366f1;text-decoration:none;display:flex;align-items:center;gap:.25rem;">
                    <i class="bi bi-box-arrow-up-right" style="font-size:.7rem;"></i> {{ __('common.open') ?? 'Aç' }}
                </a>
                <button type="button" onclick="closeMenuPreview()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:1.1rem;padding:.2rem;line-height:1;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
        <iframe id="menuPreviewFrame" src="" style="flex:1;border:none;width:100%;" loading="lazy"></iframe>
    </div>
</div>

@push('scripts')
<script>
function copyDashboardShortLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        const icon = document.getElementById('dashCopyShortIcon');
        const btn  = document.getElementById('dashCopyShortBtn');
        icon.className = 'bi bi-check2';
        btn.style.color = '#86efac';
        setTimeout(() => {
            icon.className = 'bi bi-clipboard';
            btn.style.color = '#4ade80';
        }, 2000);
    });
}
function openMenuPreview() {
    var overlay = document.getElementById('menuPreviewOverlay');
    var frame   = document.getElementById('menuPreviewFrame');
    if (!frame.src || frame.src === window.location.href) {
        frame.src = '{{ $menuUrl }}';
    }
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeMenuPreview() {
    document.getElementById('menuPreviewOverlay').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeMenuPreview();
});
</script>
@endpush
@endsection
