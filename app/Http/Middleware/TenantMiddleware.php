<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->role === 'developer') {
            return redirect()->route('developer.index');
        }

        if (!$user->tenant_id) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => __('messages.no_tenant')]);
        }

        $tenant = DB::table('tenants')->find($user->tenant_id);

        if (!$tenant) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => __('messages.no_tenant')]);
        }

        if (!$tenant->is_active && !session('impersonating_from')) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => __('messages.tenant_inactive'),
            ]);
        }

        session(['tenant_id' => $user->tenant_id]);

        return $next($request);
    }
}
