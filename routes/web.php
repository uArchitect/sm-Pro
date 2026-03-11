<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\EventController;

// Demo menü — potansiyel müşterilerin test sayfasını görmesi için (Fake RESTORANT seeder gerekir)
Route::get('/demo', function () {
    $tenant = DB::table('tenants')
        ->where('restoran_adi', 'Fake RESTORANT')
        ->where('is_active', true)
        ->first();
    if (!$tenant) {
        return redirect()->route('home')->with('demo_unavailable', true);
    }
    return redirect()->route('public.menu', ['tenantId' => $tenant->id, 'preview' => 1]);
})->name('demo');

Route::get('/', fn () => view('landing'))->name('home');

// Public static pages
Route::get('/fiyatlar', fn () => view('pages.fiyatlar'))->name('pricing');
Route::get('/ozellikler', fn () => view('pages.ozellikler'))->name('features');
Route::get('/hakkimizda', fn () => view('pages.hakkimizda'))->name('about');
Route::get('/iletisim', fn () => view('pages.iletisim'))->name('contact');
Route::get('/gizlilik-politikasi', fn () => view('pages.gizlilik-politikasi'))->name('privacy');
Route::get('/kullanim-kosullari', fn () => view('pages.kullanim-kosullari'))->name('terms');

Route::get('/sitemap.xml', function () {
    $pages = collect([
        ['loc' => url('/'), 'lastmod' => now()->toDateString(), 'priority' => '1.0'],
        ['loc' => route('pricing'), 'lastmod' => now()->toDateString(), 'priority' => '0.9'],
        ['loc' => route('features'), 'lastmod' => now()->toDateString(), 'priority' => '0.8'],
        ['loc' => route('about'), 'lastmod' => now()->toDateString(), 'priority' => '0.6'],
        ['loc' => route('contact'), 'lastmod' => now()->toDateString(), 'priority' => '0.6'],
        ['loc' => route('privacy'), 'lastmod' => now()->toDateString(), 'priority' => '0.3'],
        ['loc' => route('terms'), 'lastmod' => now()->toDateString(), 'priority' => '0.3'],
        ['loc' => route('demo'), 'lastmod' => now()->toDateString(), 'priority' => '0.8'],
        ['loc' => route('login'), 'lastmod' => now()->toDateString(), 'priority' => '0.6'],
        ['loc' => route('register'), 'lastmod' => now()->toDateString(), 'priority' => '0.7'],
    ]);

    return response()
        ->view('sitemap.xml', compact('pages'))
        ->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('sitemap');

// Locale switcher (session-based, no auth required)
Route::post('/locale', function (\Illuminate\Http\Request $request) {
    $request->validate(['locale' => 'required|string|in:en,tr']);
    session(['locale' => $request->locale]);
    cookie()->queue('locale', $request->locale, 60 * 24 * 30);
    $redirect = $request->input('redirect', '');
    if ($redirect && str_starts_with($redirect, '/')) {
        return redirect()->to($redirect);
    }
    return redirect()->back();
})->name('locale.switch');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated + Tenant-scoped
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Owner only
    Route::middleware('role:owner')->group(function () {
        Route::get('/company', [TenantController::class, 'edit'])->name('company.edit');
        Route::put('/company', [TenantController::class, 'update'])->name('company.update');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    // Categories — owner, admin, personel
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/{id}/inline-update', [CategoryController::class, 'inlineUpdate'])->name('categories.inline-update');
    Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

    // Products — owner, admin, personel
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{id}/inline-update', [ProductController::class, 'inlineUpdate'])->name('products.inline-update');
    Route::post('/products/reorder', [ProductController::class, 'reorder'])->name('products.reorder');

    // Support (all roles)
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::get('/support/create', [SupportController::class, 'create'])->name('support.create');
    Route::post('/support', [SupportController::class, 'store'])->name('support.store');
    Route::get('/support/{id}', [SupportController::class, 'show'])->name('support.show');
    Route::post('/support/{id}/reply', [SupportController::class, 'reply'])->name('support.reply');

    // Premium gate page
    Route::get('/premium', fn () => view('premium-gate'))->name('premium.gate');

    // Premium features
    Route::middleware('premium')->group(function () {
        Route::get('/sliders', [SliderController::class, 'index'])->name('sliders.index');
        Route::post('/sliders', [SliderController::class, 'store'])->name('sliders.store');
        Route::delete('/sliders/{id}', [SliderController::class, 'destroy'])->name('sliders.destroy');
        Route::post('/sliders/reorder', [SliderController::class, 'reorder'])->name('sliders.reorder');

        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('events.destroy');
    });

    // QR kod (auth)
    Route::get('/menu/qr', [QRController::class, 'menuQr'])->name('menu.qr');
    Route::get('/products/{id}/qr', [QRController::class, 'show'])->name('products.qr');
});

// Developer panel (auth + developer rolü zorunlu)
Route::middleware(['auth', 'role:developer'])->prefix('developer')->name('developer.')->group(function () {
    Route::get('/',                         [DeveloperController::class, 'index'])->name('index');
    Route::get('/tenant/{id}',              [DeveloperController::class, 'tenant'])->name('tenant');
    Route::put('/tenant/{id}',              [DeveloperController::class, 'updateTenant'])->name('tenant.update');
    Route::delete('/tenant/{id}',           [DeveloperController::class, 'destroyTenant'])->name('tenant.destroy');
    Route::post('/tenant/{id}/toggle',      [DeveloperController::class, 'toggleTenant'])->name('tenant.toggle');
    Route::post('/tenant/{id}/impersonate', [DeveloperController::class, 'impersonate'])->name('tenant.impersonate');
    Route::get('/users',                    [DeveloperController::class, 'users'])->name('users');
    Route::delete('/users/{id}',            [DeveloperController::class, 'destroyUser'])->name('users.destroy');
    Route::post('/tenant/{id}/package',     [DeveloperController::class, 'togglePackage'])->name('tenant.package');
    Route::get('/tickets',                  [DeveloperController::class, 'tickets'])->name('tickets');
    Route::get('/tickets/{id}',             [DeveloperController::class, 'ticketShow'])->name('tickets.show');
    Route::post('/tickets/{id}/reply',      [DeveloperController::class, 'ticketReply'])->name('tickets.reply');
    Route::get('/settings',                 [DeveloperController::class, 'settings'])->name('settings');
    Route::post('/settings',                [DeveloperController::class, 'updateSettings'])->name('settings.update');
});

// Impersonate exit (accessible while impersonating)
Route::post('/developer/stop-impersonate', [DeveloperController::class, 'stopImpersonate'])
    ->name('developer.stop-impersonate')
    ->middleware('auth');

// Public sayfalar (auth yok — kalıcı URL'ler, QR baskısına uygundur)
Route::get('/menu/{tenantId}', [QRController::class, 'publicMenu'])->name('public.menu');
Route::get('/menu/{tenantId}/product/{productId}', [QRController::class, 'publicProduct'])->name('public.product');
Route::post('/menu/{tenantId}/review', [QRController::class, 'submitReview'])->name('public.review');
