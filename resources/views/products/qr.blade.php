@extends('layouts.app')

@section('title', __('product_qr.title') . ' — ' . $product->name)
@section('page-title', __('product_qr.title'))
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="sm-card text-center">
            <div class="sm-card-header justify-content-center">
                <i class="bi bi-qr-code" style="color:#4F46E5"></i>{{ $product->name }} — {{ __('product_qr.title') }}
            </div>
            <div class="sm-card-body">
                <div class="d-inline-block border rounded-3 p-3 mb-3 bg-white">
                    {!! $qrCode !!}
                </div>

                <p class="text-muted small mb-1">{{ __('product_qr.qr_points_to') }}</p>
                <code class="d-block small text-break bg-light rounded p-2 mb-4">{{ $url }}</code>

                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="{{ $url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('product_qr.open_product') }}
                    </a>
                    <button onclick="printQR()" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-printer me-1"></i>{{ __('product_qr.print') }}
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-dark btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>{{ __('common.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function printQR() {
    const svg = document.querySelector('.sm-card-body svg');
    const win = window.open('', '_blank');
    win.document.write(`<!DOCTYPE html><html><head><title>QR - {{ $product->name }}</title>
    <style>body{display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;font-family:sans-serif;}
    h3{margin-bottom:16px;}svg{width:280px;height:280px;}</style></head>
    <body><h3>{{ $product->name }}</h3>${svg.outerHTML}<p style="margin-top:12px;font-size:12px;color:#666;">{{ $url }}</p></body></html>`);
    win.document.close();
    win.print();
}
</script>
@endpush
@endsection
