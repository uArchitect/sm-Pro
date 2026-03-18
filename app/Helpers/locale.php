<?php

use Illuminate\Support\Facades\Route;

/**
 * Generate the URL for the alternate language version of the current page.
 */
function alternate_url(string $locale): string
{
    $route = request()->route();
    if (!$route) {
        return $locale === 'en' ? url('/en') : url('/');
    }

    $name   = $route->getName();
    $params = $route->parameters();

    if (!$name) {
        return $locale === 'en' ? url('/en') : url('/');
    }

    $isEnRoute = str_starts_with($name, 'en.');

    if ($locale === 'en') {
        if ($isEnRoute) {
            return route($name, $params);
        }
        $enRoute = 'en.' . $name;
        return Route::has($enRoute) ? route($enRoute, $params) : url('/en');
    }

    if ($isEnRoute) {
        $trRoute = substr($name, 3); // strip 'en.' prefix
        return Route::has($trRoute) ? route($trRoute, $params) : url('/');
    }

    return route($name, $params);
}
