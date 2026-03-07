<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} — {{ $tenant->restoran_adi }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; min-height: 100vh; }
        .product-card { border: none; border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); max-width: 480px; margin: 0 auto; overflow: hidden; }
        .product-header { background: linear-gradient(135deg, #1e2a3a 0%, #2d4059 100%); padding: 1.5rem; }
        .price-badge { font-size: 1.5rem; font-weight: 700; color: #f59e0b; }
    </style>
</head>
<body class="d-flex flex-column align-items-center justify-content-start py-5 px-3">

    <div class="product-card w-100">
        <div class="product-header text-center">
            <div class="text-white-50 small mb-1">
                <i class="bi bi-building me-1"></i>{{ $tenant->restoran_adi }}
            </div>
            <h4 class="text-white fw-bold mb-0">{{ $product->name }}</h4>
        </div>

        <div class="card-body p-4 bg-white">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <span class="badge bg-light text-dark border fs-6 px-3 py-2">
                    <i class="bi bi-grid me-1"></i>{{ $product->category_name }}
                </span>
                <div class="price-badge">
                    {{ number_format($product->price, 2, ',', '.') }} ₺
                </div>
            </div>

            @if($product->description)
            <hr class="my-3">
            <h6 class="fw-semibold text-muted small text-uppercase letter-spacing">Açıklama</h6>
            <p class="text-dark mb-0">{{ $product->description }}</p>
            @endif
        </div>

        <div class="card-footer bg-light text-center py-3 border-0">
            <p class="text-muted small mb-0">
                <i class="bi bi-qr-code me-1"></i>
                {{ $tenant->restoran_adi }} — Sipariş Masanda
            </p>
        </div>
    </div>

</body>
</html>
