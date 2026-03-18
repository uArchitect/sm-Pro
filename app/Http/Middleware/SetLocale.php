<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    protected array $fallbackLocales = ['en', 'tr'];

    public function handle(Request $request, Closure $next): Response
    {
        $allowed = array_keys(config('app.available_locales', []));
        if (empty($allowed)) {
            $allowed = $this->fallbackLocales;
        }

        $path = $request->getPathInfo();

        // ── 1. Legacy ?lang= parameter → 301 redirect ──
        $langParam = $request->query('lang');
        if (is_string($langParam) && in_array($langParam, $allowed, true)) {
            return $this->redirectLegacyLang($request, $langParam);
        }

        // ── 2. URL-based locale for /en/ prefix ──
        if ($this->isEnglishPath($path)) {
            App::setLocale('en');
            return $next($request);
        }

        // ── 3. Public TR pages (routes that have an EN counterpart) → force TR ──
        $routeName = $request->route()?->getName();
        if ($routeName && Route::has('en.' . $routeName)) {
            App::setLocale('tr');
            return $next($request);
        }

        // ── 4. Admin / other pages → session & cookie behaviour ──
        $sessionLocale = $request->session()->get('locale');
        $cookieLocale  = $request->cookie('locale');

        $locale = $sessionLocale ?? $cookieLocale ?? $this->guessLocaleByCountry($request);

        if (!$sessionLocale && $cookieLocale && in_array($cookieLocale, $allowed, true)) {
            $request->session()->put('locale', $cookieLocale);
        }

        if (!$sessionLocale && !$cookieLocale && in_array($locale, $allowed, true)) {
            $request->session()->put('locale', $locale);
            Cookie::queue('locale', $locale, 60 * 24 * 30);
        }

        if (!in_array($locale, $allowed, true)) {
            $locale = config('app.fallback_locale', 'tr');
        }

        App::setLocale($locale);

        return $next($request);
    }

    private function isEnglishPath(string $path): bool
    {
        return $path === '/en' || str_starts_with($path, '/en/');
    }

    private function redirectLegacyLang(Request $request, string $lang): Response
    {
        $route     = $request->route();
        $routeName = $route?->getName();
        $params    = $route ? $route->parameters() : [];
        $isEn      = $routeName && str_starts_with($routeName, 'en.');

        if ($lang === 'en' && !$isEn && $routeName) {
            $enRoute = 'en.' . $routeName;
            if (Route::has($enRoute)) {
                return redirect()->route($enRoute, $params, 301);
            }
        }

        if ($lang === 'tr' && $isEn && $routeName) {
            $trRoute = substr($routeName, 3);
            if (Route::has($trRoute)) {
                return redirect()->route($trRoute, $params, 301);
            }
        }

        $url         = $request->url();
        $otherParams = $request->except('lang');

        if (!empty($otherParams)) {
            $url .= '?' . http_build_query($otherParams);
        }

        return redirect($url, 301);
    }

    protected function guessLocaleByCountry(Request $request): string
    {
        $country = strtoupper((string) (
            $request->header('CF-IPCountry')
            ?? $request->header('CloudFront-Viewer-Country')
            ?? $request->header('X-Country-Code')
            ?? ''
        ));

        if ($country === 'TR') {
            return 'tr';
        }

        if ($country !== '') {
            return 'en';
        }

        $accept = $request->header('Accept-Language', '');
        if (stripos($accept, 'tr') !== false) {
            return 'tr';
        }

        return config('app.fallback_locale', 'tr');
    }
}
