<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Developer role has no tenant — route to their own panel
        if ($user->role === 'developer') {
            return redirect()->route('developer.index');
        }

        if (!$user->tenant_id) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => __('messages.no_tenant')]);
        }

        session(['tenant_id' => $user->tenant_id]);

        return $next($request);
    }
}
