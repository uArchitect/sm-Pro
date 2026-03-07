<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Masanda</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #0a0f1e; min-height: 100vh; overflow-x: hidden; }

        /* Background grid */
        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(255,107,53,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,107,53,0.04) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        /* Glow blobs */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            pointer-events: none;
            opacity: .35;
        }
        .blob-1 { width: 600px; height: 600px; background: #FF6B35; top: -200px; left: -200px; }
        .blob-2 { width: 500px; height: 500px; background: #6C5CE7; bottom: -150px; right: -150px; }

        .hero { min-height: 100vh; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1; }

        .logo-mark {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, #FF6B35, #FF8C42);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: white;
            box-shadow: 0 20px 60px rgba(255,107,53,.4);
            margin: 0 auto 1.5rem;
        }

        .brand-name {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            background: linear-gradient(135deg, #fff 40%, #FF6B35);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.1;
            letter-spacing: -.02em;
        }

        .tagline {
            font-size: 1.1rem;
            color: rgba(255,255,255,.5);
            font-weight: 400;
            max-width: 480px;
            margin: 1.25rem auto 2.5rem;
            line-height: 1.7;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #FF6B35, #FF8C42);
            border: none;
            color: white;
            font-weight: 600;
            font-size: .95rem;
            padding: .8rem 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(255,107,53,.35);
            transition: all .2s;
            text-decoration: none;
        }
        .btn-primary-custom:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(255,107,53,.5); color: white; }

        .btn-outline-custom {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.15);
            color: rgba(255,255,255,.85);
            font-weight: 500;
            font-size: .95rem;
            padding: .8rem 2rem;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            transition: all .2s;
            text-decoration: none;
        }
        .btn-outline-custom:hover { background: rgba(255,255,255,.12); border-color: rgba(255,255,255,.3); color: white; transform: translateY(-2px); }

        .feature-chips {
            display: flex;
            gap: .6rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 3rem;
        }
        .chip {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .4rem .9rem;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 100px;
            color: rgba(255,255,255,.55);
            font-size: .78rem;
            font-weight: 500;
        }
        .chip i { color: #FF6B35; font-size: .85rem; }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <section class="hero">
        <div class="text-center px-3">
            <div class="logo-mark">
                <i class="bi bi-qr-code-scan"></i>
            </div>

            <h1 class="brand-name">Sipariş Masanda</h1>

            <p class="tagline">
                Restoranınızın dijital menüsünü oluşturun,<br>
                QR kod ile masaya taşıyın.
            </p>

            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('register') }}" class="btn-primary-custom">
                    <i class="bi bi-rocket-takeoff me-2"></i>Hemen Başla
                </a>
                <a href="{{ route('login') }}" class="btn-outline-custom">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Giriş Yap
                </a>
            </div>

            <div class="feature-chips">
                <span class="chip"><i class="bi bi-qr-code"></i> QR Menü</span>
                <span class="chip"><i class="bi bi-people"></i> Çok Kullanıcı</span>
                <span class="chip"><i class="bi bi-grid"></i> Kategori & Ürün</span>
                <span class="chip"><i class="bi bi-building"></i> Çoklu Restoran</span>
                <span class="chip"><i class="bi bi-printer"></i> Baskıya Hazır QR</span>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
