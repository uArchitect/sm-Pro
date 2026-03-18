<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon-indigo.svg') }}">
    <title>{{ $tenant->restoran_adi }} — {{ $locale === 'tr' ? 'Rezervasyon' : 'Reservation' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { background: #f8fafc; margin: 0; padding: 0; color: #1e293b; min-height: 100vh; }
        .rv-wrap { max-width: 520px; margin: 0 auto; padding: 1.5rem 1rem 3rem; }
        .rv-header { text-align: center; padding: 2rem 1rem 1.5rem; }
        .rv-logo { width: 64px; height: 64px; border-radius: 16px; object-fit: cover; border: 2px solid #e2e8f0; margin-bottom: .75rem; }
        .rv-logo-fallback { width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, #4F46E5, #6366F1); display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #fff; margin-bottom: .75rem; }
        .rv-title { font-size: 1.15rem; font-weight: 800; color: #0f172a; }
        .rv-subtitle { font-size: .78rem; color: #64748b; margin-top: .2rem; }
        .rv-form-card { background: #fff; border-radius: 16px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,.06); border: 1px solid #e2e8f0; }
        .rv-form-card .form-label { font-size: .8rem; font-weight: 600; color: #334155; margin-bottom: .3rem; }
        .rv-form-card .form-control, .rv-form-card .form-select { font-size: .84rem; border-radius: 10px; border: 1.5px solid #e2e8f0; padding: .55rem .85rem; }
        .rv-form-card .form-control:focus, .rv-form-card .form-select:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
        .rv-submit-btn { width: 100%; padding: .7rem; border: none; border-radius: 12px; background: linear-gradient(135deg, #4F46E5, #6366F1); color: #fff; font-weight: 700; font-size: .88rem; cursor: pointer; transition: all .2s; }
        .rv-submit-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(79,70,229,.3); }
        .rv-success { text-align: center; padding: 2.5rem 1rem; }
        .rv-success-icon { width: 64px; height: 64px; border-radius: 50%; background: rgba(34,197,94,.12); display: inline-flex; align-items: center; justify-content: center; font-size: 1.6rem; color: #22c55e; margin-bottom: 1rem; }
        .rv-back-link { display: inline-flex; align-items: center; gap: .3rem; font-size: .8rem; color: #4F46E5; text-decoration: none; margin-top: 1rem; font-weight: 600; }
        .rv-back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="rv-wrap">
    <div class="rv-header">
        @if($tenant->logo)
            <img src="{{ asset('uploads/'.$tenant->logo) }}" alt="{{ $tenant->restoran_adi }}" class="rv-logo">
        @else
            <div class="rv-logo-fallback"><i class="bi bi-shop"></i></div>
        @endif
        <div class="rv-title">{{ $tenant->restoran_adi }}</div>
        <div class="rv-subtitle">{{ $locale === 'tr' ? 'Online Rezervasyon' : 'Online Reservation' }}</div>
    </div>

    @if(session('reservation_success'))
    <div class="rv-form-card">
        <div class="rv-success">
            <div class="rv-success-icon"><i class="bi bi-check-lg"></i></div>
            <div style="font-weight:700;font-size:1.05rem;color:#0f172a">{{ $locale === 'tr' ? 'Rezervasyonunuz Alındı!' : 'Reservation Received!' }}</div>
            <p class="text-muted small mt-2">{{ $locale === 'tr' ? 'Restoran en kısa sürede rezervasyonunuzu onaylayacaktır. Teşekkür ederiz.' : 'The restaurant will confirm your reservation shortly. Thank you.' }}</p>
            <a href="{{ route('public.reservation', $tenant->id) }}" class="rv-back-link"><i class="bi bi-arrow-left"></i> {{ $locale === 'tr' ? 'Yeni rezervasyon' : 'New reservation' }}</a>
            <br>
            <a href="{{ route('public.menu', $tenant->id) }}" class="rv-back-link"><i class="bi bi-journal-text"></i> {{ $locale === 'tr' ? 'Menüye dön' : 'Back to menu' }}</a>
        </div>
    </div>
    @else
    <div class="rv-form-card">
        @if($errors->any())
        <div class="alert alert-danger" style="font-size:.82rem;border-radius:10px;border:none;background:rgba(239,68,68,.08);color:#dc2626">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            @foreach($errors->all() as $err)
                {{ $err }}<br>
            @endforeach
        </div>
        @endif

        @if($zones->isEmpty())
        <div class="text-center py-4 text-muted">
            <i class="bi bi-calendar-x" style="font-size:2rem;opacity:.3;display:block;margin-bottom:.5rem"></i>
            <div style="font-size:.85rem">{{ $locale === 'tr' ? 'Bu restoran henüz rezervasyon sistemi kurmamış.' : 'This restaurant has not set up the reservation system yet.' }}</div>
        </div>
        @else
        <form method="POST" action="{{ route('public.reservation.store', $tenant->id) }}">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label class="form-label">{{ $locale === 'tr' ? 'Bölge' : 'Zone' }} *</label>
                    <select id="zoneSelect" class="form-select" required onchange="updateTables()">
                        <option value="">{{ $locale === 'tr' ? 'Bölge seçin' : 'Select zone' }}</option>
                        @foreach($zones as $z)
                        <option value="{{ $z->id }}" {{ old('_zone') == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">{{ $locale === 'tr' ? 'Masa' : 'Table' }} *</label>
                    <select name="table_id" id="tableSelect" class="form-select" required>
                        <option value="">{{ $locale === 'tr' ? 'Önce bölge seçin' : 'Select zone first' }}</option>
                    </select>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label class="form-label">{{ $locale === 'tr' ? 'Adınız Soyadınız' : 'Full Name' }} *</label>
                    <input type="text" name="customer_name" class="form-control" required maxlength="120" value="{{ old('customer_name') }}" placeholder="{{ $locale === 'tr' ? 'Adınız...' : 'Your name...' }}">
                </div>
                <div class="col-sm-6">
                    <label class="form-label">{{ $locale === 'tr' ? 'Telefon' : 'Phone' }} *</label>
                    <input type="tel" name="customer_phone" class="form-control" required maxlength="30" value="{{ old('customer_phone') }}" placeholder="05xx xxx xx xx">
                </div>
                <div class="col-sm-6">
                    <label class="form-label">{{ $locale === 'tr' ? 'E-posta' : 'Email' }}</label>
                    <input type="email" name="customer_email" class="form-control" maxlength="120" value="{{ old('customer_email') }}" placeholder="{{ $locale === 'tr' ? 'İsteğe bağlı' : 'Optional' }}">
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-sm-4">
                    <label class="form-label">{{ $locale === 'tr' ? 'Tarih' : 'Date' }} *</label>
                    <input type="date" name="reservation_date" class="form-control" required min="{{ date('Y-m-d') }}" value="{{ old('reservation_date', date('Y-m-d')) }}">
                </div>
                <div class="col-sm-4">
                    <label class="form-label">{{ $locale === 'tr' ? 'Başlangıç' : 'Start' }} *</label>
                    <input type="time" name="start_time" class="form-control" required value="{{ old('start_time', '19:00') }}">
                </div>
                <div class="col-sm-4">
                    <label class="form-label">{{ $locale === 'tr' ? 'Bitiş' : 'End' }} *</label>
                    <input type="time" name="end_time" class="form-control" required value="{{ old('end_time', '21:00') }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ $locale === 'tr' ? 'Not' : 'Notes' }}</label>
                <textarea name="notes" class="form-control" rows="2" maxlength="500" placeholder="{{ $locale === 'tr' ? 'Özel istekleriniz...' : 'Special requests...' }}">{{ old('notes') }}</textarea>
            </div>
            <button type="submit" class="rv-submit-btn">
                <i class="bi bi-calendar-check me-1"></i>{{ $locale === 'tr' ? 'Rezervasyon Yap' : 'Make Reservation' }}
            </button>
        </form>
        @endif
    </div>
    @endif

    <div class="text-center mt-3">
        <a href="{{ route('public.menu', $tenant->id) }}" class="rv-back-link"><i class="bi bi-arrow-left"></i> {{ $locale === 'tr' ? 'Menüye dön' : 'Back to menu' }}</a>
    </div>
</div>

<script>
var tablesData = @json($tables->map(fn($group, $zoneId) => $group->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'capacity' => $t->capacity]))->toArray());
var tableSelectPlaceholder = @json($locale === 'tr' ? 'Masa seçin' : 'Select table');
var personLabel = @json($locale === 'tr' ? 'kişi' : 'persons');

function updateTables() {
    var zoneId = document.getElementById('zoneSelect').value;
    var sel = document.getElementById('tableSelect');
    sel.innerHTML = '';

    if (!zoneId || !tablesData[zoneId]) {
        sel.innerHTML = '<option value="">' + @json($locale === 'tr' ? 'Önce bölge seçin' : 'Select zone first') + '</option>';
        return;
    }

    sel.innerHTML = '<option value="">' + tableSelectPlaceholder + '</option>';
    tablesData[zoneId].forEach(function(t) {
        var opt = document.createElement('option');
        opt.value = t.id;
        opt.textContent = t.name + ' (' + t.capacity + ' ' + personLabel + ')';
        sel.appendChild(opt);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('zoneSelect').value) updateTables();
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
