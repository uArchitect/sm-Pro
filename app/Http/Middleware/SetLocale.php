<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales - used when config is cached without available_locales.
     */
    protected array $fallbackLocales = ['en', 'tr'];

    public function handle(Request $request, Closure $next): Response
    {
        $allowed = array_keys(config('app.available_locales', []));
        if (empty($allowed)) {
            $allowed = $this->fallbackLocales;
        }

        $requestedLocale = $request->query('lang');

        if (is_string($requestedLocale) && in_array($requestedLocale, $allowed, true)) {
            $locale = $requestedLocale;
            $request->session()->put('locale', $locale);
            Cookie::queue('locale', $locale, 60 * 24 * 30);
        } else {
            $locale = $request->session()->get('locale')
                ?? $request->cookie('locale')
                ?? $this->guessLocaleByCountry($request);
        }

        if (!in_array($locale, $allowed, true)) {
            $locale = config('app.fallback_locale', 'tr');
        }

        App::setLocale($locale);

        return $next($request);
    }

    /**
     * Geo-header → country-based pick; no header → check Accept-Language
     * but lean towards app default (tr) since this is a Turkish product.
     */
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
