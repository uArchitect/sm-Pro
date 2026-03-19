<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PremiumMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = session('tenant_id');

        if (!$tenantId) {
            return redirect()->route('dashboard');
        }

        $tenant = DB::table('tenants')->find($tenantId);

        if (!$tenant || !in_array($tenant->package ?? 'basic', ['premium', 'enterprise'])) {
            return redirect()->route('premium.gate');
        }

        return $next($request);
    }
}
