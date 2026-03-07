# Internationalization (i18n) Implementation Report

**Project:** sm-Pro (Laravel 12)  
**Date:** March 7, 2025  
**Scope:** English language support, locale configuration, language switcher, and i18n readiness for future languages.

---

## 1. Executive Summary

The codebase was **Turkish-only** with no existing multilingual support. The following was implemented:

- **English (en)** and **Turkish (tr)** language files under `lang/en` and `lang/tr`
- **Locale middleware** that sets the application locale from session (or config)
- **Language switcher** in the main app layout (topbar) to toggle between en/tr
- **Replacement of hard-coded strings** in layouts, dashboard, auth, and products with Laravel’s `__()` / `trans()` helpers
- **Controller flash and validation messages** switched to translation keys
- **Configuration** for available locales and fallback

The app now supports **application-wide language selection** via session and is ready to add more languages by adding locale codes to config and new `lang/{locale}` folders.

---

## 2. Findings Before Implementation

### 2.1 Laravel Version & Compatibility

- **Laravel:** 12.x (composer.json: `"laravel/framework": "^12.0"`)
- **PHP:** ^8.2
- **i18n:** No third-party i18n packages required; Laravel’s built-in translation is used and is compatible with Laravel 12.

### 2.2 Existing i18n State

| Item | Status |
|------|--------|
| `config/app.php` locale | Present: `locale` and `fallback_locale` from env (default `en`) |
| Project language files | **None** – no `lang/` or `resources/lang` in project |
| Use of `__()`, `trans()`, `@lang` | **None** in `app/` or `resources/views` |
| Locale middleware / session | **None** |
| Hard-coded locale in views | **Yes** – `html lang="tr"` and `now()->locale('tr')` in layouts |

### 2.3 Hard-coded Strings

- **Layouts:** `app.blade.php`, `dev.blade.php` – Turkish UI labels, app name “Sipariş Masanda”, date in Turkish locale
- **Auth:** `auth/login.blade.php` – All form labels, buttons, and hero text in Turkish
- **Dashboard:** `dashboard/index.blade.php` – Section titles, stats, buttons in Turkish
- **Products:** `products/index.blade.php` – Table headers, buttons, modal, toast in Turkish
- **Controllers:** Flash messages and validation messages in Turkish (e.g. “Ürün başarıyla eklendi.”, “Firma adı zorunludur.”)

---

## 3. Changes Made

### 3.1 Configuration

**File: `config/app.php`**

- Added `available_locales` for the language switcher and validation:

```php
'available_locales' => [
    'en' => 'English',
    'tr' => 'Türkçe',
],
```

**Environment**

- `APP_LOCALE` and `APP_FALLBACK_LOCALE` already in `.env` (default `en`). No change required; they drive the default and fallback locale.

### 3.2 Language Files

Laravel 12 resolves the lang path as `resourcePath('lang')` if it exists, otherwise `basePath('lang')`. This project uses **`lang/` at project root** (no `resources/lang`), so all language files are under:

- `lang/en/`
- `lang/tr/`

**Structure:**

| File | Purpose |
|------|--------|
| `common.php` | App name, panel, logout, save, cancel, edit, delete, actions, active, photo, etc. |
| `nav.php` | Sidebar and nav labels (dashboard, company, staff, categories, products, QR, roles) |
| `auth.php` | Login/register titles, form labels, placeholders, buttons, hero text |
| `dashboard.php` | Dashboard title, breadcrumb, stats, company info, quick actions |
| `products.php` | Product list, add/edit, price, category, delete confirm, validation message |
| `categories.php` | Category list, add, total, name |
| `users.php` | Staff list, add, “owner cannot be deleted” |
| `tenant.php` | Company info title, “updated” message |
| `messages.php` | All controller flash messages (success/error) and no_tenant |
| `validation.php` | Custom validation messages and auth failed |

Validation keys under `validation.required.*` and `validation.auth.failed` are used in controllers for registration, login, and tenant forms.

### 3.3 Middleware

**File: `app/Http/Middleware/SetLocale.php`**

- Reads locale from `session('locale')`, then `cookie('locale')`, then `config('app.locale')`
- Ensures the value exists in `config('app.available_locales')`
- Calls `App::setLocale($locale)` so all `__()` and `trans()` use the chosen language

**File: `bootstrap/app.php`**

- Registered alias: `'locale' => \App\Http\Middleware\SetLocale::class`
- Appended to the `web` middleware group so every web request sets locale after session is started

### 3.4 Language Switcher & Route

**Route: `routes/web.php`**

```php
Route::post('/locale', function (\Illuminate\Http\Request $request) {
    $request->validate(['locale' => 'required|string|in:en,tr']);
    session(['locale' => $request->locale]);
    if ($request->has('redirect')) {
        return redirect()->to($request->redirect);
    }
    return redirect()->back();
})->name('locale.switch');
```

**Layout: `resources/views/layouts/app.blade.php`**

- Topbar: form that POSTs to `locale.switch` with `locale` (en/tr) and optional `redirect`
- Button shows the “other” language (e.g. “Türkçe” when current is English) and uses the translate icon
- `html lang="{{ str_replace('_', '-', app()->getLocale()) }}"`
- Date in topbar: `{{ now()->locale(app()->getLocale())->isoFormat('D MMM YYYY') }}`
- All sidebar and topbar labels use `__('nav.nav.*')` and `__('common.*')`

### 3.5 Views Updated to Use Translations

- **layouts/app.blade.php** – Title, app name, sidebar nav, role labels, logout title, page title, date, language switcher
- **layouts/dev.blade.php** – Date uses `app()->getLocale()`
- **auth/login.blade.php** – Title, brand, hero, pills, footer, form labels/placeholders, login/register titles, buttons, links
- **dashboard/index.blade.php** – Title, breadcrumb, “menu live”, stats labels, company info labels, quick action labels
- **products/index.blade.php** – Page title, “total”, add button, table headers, edit/delete titles, empty state, modal labels, cancel/save, toast, JS alert text for validation and delete confirm (via `json_encode` for safe JS)

### 3.6 Controllers Updated to Use Translations

- **AuthController** – All validation messages use `__('validation.required.*')` and `__('validation.auth.failed')`
- **TenantController** – Validation messages and success flash use `__('validation.required.*')` and `__('messages.tenant_updated')`
- **TenantMiddleware** – Error message uses `__('messages.no_tenant')`
- **ProductController** – Success flashes use `__('messages.product_added')`, `product_updated`, `product_deleted`
- **CategoryController** – Success flashes use `__('messages.category_added')`, `category_updated`, `category_deleted`
- **UserController** – Success/error flashes use `__('messages.staff_added')`, `staff_deleted`, `no_owner_delete`
- **DeveloperController** – Success flashes use `__('messages.tenant_deleted')`, `settings_updated`

---

## 4. Fallback Logic

- **Default locale:** `config('app.locale')` (from `APP_LOCALE`, default `en`)
- **Fallback locale:** `config('app.fallback_locale')` (from `APP_FALLBACK_LOCALE`, default `en`)
- If a key is missing in the current locale, Laravel uses the fallback locale automatically
- SetLocale middleware only applies locales that exist in `available_locales`, so invalid session values do not break the app

---

## 5. Database

No database changes were required. Locale is stored in **session** only. Optional future improvements:

- **User preference:** Add a `locale` column to `users` and set `App::setLocale($user->locale)` when authenticated
- **Site setting:** Store default locale in a `settings` table and use it when session has no locale

---

## 6. Adding More Languages (e.g. German)

1. Add the locale to config:

```php
// config/app.php
'available_locales' => [
    'en' => 'English',
    'tr' => 'Türkçe',
    'de' => 'Deutsch',
],
```

2. Create `lang/de/` and copy from `lang/en/` (or `lang/tr/`) and translate:

```
lang/de/common.php
lang/de/nav.php
lang/de/auth.php
... (same files as en/tr)
```

3. Update the locale route validation:

```php
// routes/web.php
'locale' => 'required|string|in:en,tr,de',
```

4. Optionally add a dropdown in the layout that lists all `config('app.available_locales')` instead of a single toggle.

---

## 7. Potential Issues & Remaining Work

### 7.1 Views Not Fully Converted

The following views still contain hard-coded Turkish (or mixed) strings and should be converted to `__()` when those screens are used:

- `resources/views/categories/index.blade.php`
- `resources/views/categories/create.blade.php`
- `resources/views/categories/edit.blade.php`
- `resources/views/products/create.blade.php`
- `resources/views/products/edit.blade.php`
- `resources/views/users/index.blade.php`
- `resources/views/users/create.blade.php`
- `resources/views/tenant/edit.blade.php`
- `resources/views/developer/*.blade.php`
- `resources/views/landing.blade.php`
- `resources/views/public/menu.blade.php`
- `resources/views/public/product.blade.php`
- `resources/views/qr/menu.blade.php`
- `resources/views/auth/register.blade.php` (if used separately)

Recommendation: Use the same pattern as in dashboard and products index: define keys in `lang/en/*.php` and `lang/tr/*.php` and replace every user-facing string with `{{ __('file.key') }}` or `@lang('file.key')`.

### 7.2 Public / QR Views

Public menu and product views are used by end customers (e.g. via QR). Options:

- Detect locale from URL segment (e.g. `/menu/123/en`), or
- Use a query parameter (e.g. `?lang=en`), or
- Use browser preference and store in session when they first hit a public page

Right now, public routes still use the app locale (session/config). If the tenant or menu should have its own language, that logic would need to be added (e.g. tenant-level default locale or locale in URL).

### 7.3 JavaScript Strings

Products index has a few strings passed from Blade into JS (e.g. validation message, “Saving...”, “Save”). These are already translated in Blade and passed as output of `__()`. Any future JS-only UI (e.g. SPA) would need a different approach (e.g. passing a JSON object of translations or using a package like `laravel-lang/js`).

### 7.4 Pluralization

Laravel supports `trans_choice()` / `__choice()`. The products “total” string uses a simple `:count` replacement. If you need proper pluralization (e.g. “1 product” vs “2 products”), add something like:

```php
// lang/en/products.php
'total' => '{0} No products|{1} 1 product|[2,*] :count products',
```

and use `trans_choice('products.total', $count)`.

### 7.5 Log Messages

Log calls in controllers (e.g. `Log::info('Tenant bilgileri güncellendi.')`) are still in Turkish. They can be left as-is (internal) or moved to translation keys if you want log language to follow locale.

---

## 8. Quick Reference: Translation Usage

| Context | Usage |
|--------|--------|
| Blade | `{{ __('file.key') }}` or `@lang('file.key')` |
| Blade with params | `{{ __('products.total', ['count' => $n]) }}` |
| Controller | `__('messages.product_added')` or `trans('messages.product_added')` |
| Validation messages | `__('validation.required.email')` |
| HTML in translation | In Blade use `{!! __('auth.hero_title') !!}` (key contains `<br>`, `<em>`) |

---

## 9. Files Touched (Summary)

| Path | Change |
|------|--------|
| `config/app.php` | Added `available_locales` |
| `bootstrap/app.php` | Registered SetLocale middleware and appended to web group |
| `routes/web.php` | Added POST `locale.switch` route |
| `app/Http/Middleware/SetLocale.php` | **New** |
| `lang/en/*.php` | **New** (10 files) |
| `lang/tr/*.php` | **New** (10 files) |
| `app/Http/Controllers/AuthController.php` | Validation and (no) flash messages use __() |
| `app/Http/Controllers/TenantController.php` | Validation and success use __() |
| `app/Http/Controllers/ProductController.php` | Success flashes use __() |
| `app/Http/Controllers/CategoryController.php` | Success flashes use __() |
| `app/Http/Controllers/UserController.php` | Success/error flashes use __() |
| `app/Http/Controllers/DeveloperController.php` | Success flashes use __() |
| `app/Http/Middleware/TenantMiddleware.php` | Error message use __() |
| `resources/views/layouts/app.blade.php` | i18n + language switcher |
| `resources/views/layouts/dev.blade.php` | Date locale |
| `resources/views/auth/login.blade.php` | Full i18n |
| `resources/views/dashboard/index.blade.php` | Full i18n |
| `resources/views/products/index.blade.php` | Full i18n |

---

## 10. Conclusion

The application is now **i18n-ready** with:

- **English** and **Turkish** supported via `lang/en` and `lang/tr`
- **Application-wide language selection** via session and a topbar switcher
- **Fallback** to `en` and config-driven available locales
- **No database changes**; optional user/site locale can be added later
- **Scalable** addition of new languages by adding locale to config and new `lang/{locale}/*.php` files

Completing the remaining views (categories, users, tenant, developer, landing, public) using the same translation pattern will make the entire UI multilingual and consistent with the implemented approach.
