@extends('layouts.app')

@section('title', __('qr.title'))
@section('page-title', __('qr.title'))
@section('breadcrumb', __('qr.breadcrumb'))
@section('content')
@php $menuUrl = route('public.menu', ['tenantId' => session('tenant_id')]); @endphp

<div class="row g-4">

    {{-- QR Kod Paneli --}}
    <div class="col-lg-5">
        <div class="sm-card h-100">
            <div class="sm-card-header">
                <i class="bi bi-qr-code text-warning"></i> {{ __('qr.print_ready') }}
            </div>
            <div class="sm-card-body text-center py-4">
                <div class="d-inline-block bg-white rounded-3 p-3 border mb-3" style="line-height:0">
                    {!! $qrCode !!}
                </div>
                <p class="text-muted small mb-4">
                    {!! __('qr.description') !!}
                </p>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <button onclick="printQR()" class="btn btn-accent btn-sm">
                        <i class="bi bi-printer me-1"></i>{{ __('qr.print') }}
                    </button>
                    <button onclick="downloadQR()" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-download me-1"></i>{{ __('qr.download_svg') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Kalıcı Link & Bilgi --}}
    <div class="col-lg-7 d-flex flex-column gap-4">

        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-link-45deg text-primary"></i> {{ __('qr.permanent_link') }}
            </div>
            <div class="sm-card-body">
                <p class="text-muted small mb-2">
                    {!! __('qr.permanent_hint') !!}
                </p>
                <div class="d-flex gap-2 align-items-center">
                    <code id="menuUrl" class="flex-grow-1 bg-light rounded px-3 py-2 small text-break border" style="font-size:.8rem">{{ $menuUrl }}</code>
                    <button onclick="copyUrl()" class="btn btn-outline-primary btn-sm flex-shrink-0" id="copyBtn">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>
                <div class="mt-3">
                    <a href="{{ $menuUrl }}" target="_blank" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('qr.preview_menu') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="sm-card">
            <div class="sm-card-header">
                <i class="bi bi-building text-muted"></i> {{ __('qr.linked_restaurant') }}
            </div>
            <div class="sm-card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted fw-semibold">{{ __('qr.restaurant') }}</dt>
                    <dd class="col-7 fw-bold">{{ $tenant->restoran_adi }}</dd>
                    <dt class="col-5 text-muted fw-semibold">{{ __('qr.company') }}</dt>
                    <dd class="col-7">{{ $tenant->firma_adi }}</dd>
                    <dt class="col-5 text-muted fw-semibold">{{ __('qr.menu_id') }}</dt>
                    <dd class="col-7"><code>#{{ $tenant->id }}</code></dd>
                </dl>
            </div>
        </div>

        <div class="sm-card" style="border-left: 3px solid #FF6B35">
            <div class="sm-card-body">
                <h6 class="fw-bold mb-2" style="font-size:.85rem"><i class="bi bi-lightbulb-fill text-warning me-1"></i>{{ __('qr.usage_guide') }}</h6>
                <ul class="mb-0 ps-3 small text-muted" style="line-height:1.9">
                    @foreach(__('qr.usage_tips') as $tip)
                    <li>{{ $tip }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyUrl() {
    navigator.clipboard.writeText('{{ $menuUrl }}').then(() => {
        const btn = document.getElementById('copyBtn');
        btn.innerHTML = '<i class="bi bi-check2"></i>';
        btn.classList.replace('btn-outline-primary', 'btn-success');
        setTimeout(() => {
            btn.innerHTML = '<i class="bi bi-clipboard"></i>';
            btn.classList.replace('btn-success', 'btn-outline-primary');
        }, 2000);
    });
}

function downloadQR() {
    const svg    = document.querySelector('.sm-card-body svg');
    const blob   = new Blob([svg.outerHTML], {type: 'image/svg+xml'});
    const a      = Object.assign(document.createElement('a'), {href: URL.createObjectURL(blob), download: 'menu-qr-{{ $tenant->id }}.svg'});
    a.click(); URL.revokeObjectURL(a.href);
}

function printQR() {
    const svg = document.querySelector('.sm-card-body svg');
    const win = window.open('', '_blank');
    win.document.write(`<!DOCTYPE html><html><head><title>Menu QR — {{ $tenant->restoran_adi }}</title>
    <style>
        body{display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;font-family:sans-serif;background:#fff;}
        h2{margin-bottom:8px;font-size:20px;}
        p{color:#666;font-size:13px;margin-bottom:20px;}
        svg{width:300px;height:300px;}
        .url{margin-top:14px;font-size:11px;color:#999;word-break:break-all;max-width:320px;text-align:center;}
    </style></head>
    <body>
        <h2>{{ $tenant->restoran_adi }}</h2>
        <p>{{ __('qr.digital_menu_brand') }}</p>
        ${svg.outerHTML}
        <div class="url">{{ $menuUrl }}</div>
    </body></html>`);
    win.document.close();
    setTimeout(() => win.print(), 400);
}
</script>
@endpush
@endsection
