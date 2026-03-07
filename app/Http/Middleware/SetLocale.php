<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales - used when config is cached without available_locales.
     */
    protected array $fallbackLocales = ['en', 'tr'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale')
            ?? $request->cookie('locale')
            ?? config('app.locale');

        $allowed = array_keys(config('app.available_locales', []));
        if (empty($allowed)) {
            $allowed = $this->fallbackLocales;
        }

        if (in_array($locale, $allowed, true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
