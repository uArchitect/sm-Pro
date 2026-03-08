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
        $locale = $request->session()->get('locale')
            ?? $request->cookie('locale')
            ?? config('app.locale');

        if (is_string($requestedLocale) && in_array($requestedLocale, $allowed, true)) {
            $locale = $requestedLocale;
            $request->session()->put('locale', $locale);
            Cookie::queue('locale', $locale, 60 * 24 * 30);
        }

        if (!in_array($locale, $allowed, true)) {
            $locale = config('app.fallback_locale', 'tr');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
