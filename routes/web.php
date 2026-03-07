<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\DeveloperController;

Route::get('/', fn () => view('landing'))->name('home');

// Locale switcher (session-based, no auth required)
Route::post('/locale', function (\Illuminate\Http\Request $request) {
    $request->validate(['locale' => 'required|string|in:en,tr']);
    session(['locale' => $request->locale]);
    if ($request->has('redirect')) {
        return redirect()->to($request->redirect);
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

    // QR kod (auth)
    Route::get('/menu/qr', [QRController::class, 'menuQr'])->name('menu.qr');
});

// Developer panel (auth gerekli, developer rolü)
Route::middleware('auth')->prefix('developer')->name('developer.')->group(function () {
    Route::get('/',                         [DeveloperController::class, 'index'])->name('index');
    Route::get('/tenant/{id}',              [DeveloperController::class, 'tenant'])->name('tenant');
    Route::delete('/tenant/{id}',           [DeveloperController::class, 'destroyTenant'])->name('tenant.destroy');
    Route::get('/settings',                 [DeveloperController::class, 'settings'])->name('settings');
    Route::post('/settings',                [DeveloperController::class, 'updateSettings'])->name('settings.update');
});

// Public sayfalar (auth yok — kalıcı URL'ler, QR baskısına uygundur)
Route::get('/menu/{tenantId}', [QRController::class, 'publicMenu'])->name('public.menu');
