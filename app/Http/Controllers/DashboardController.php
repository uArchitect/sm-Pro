<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $tenantId = session('tenant_id');

        $tenant = DB::table('tenants')->find($tenantId);

        $stats = [
            'users'      => DB::table('users')->where('tenant_id', $tenantId)->count(),
            'categories' => DB::table('categories')->where('tenant_id', $tenantId)->count(),
            'products'   => DB::table('products')->where('tenant_id', $tenantId)->count(),
        ];

        $stats['qr_today'] = DB::table('qr_visits')
            ->where('tenant_id', $tenantId)
            ->whereDate('visited_at', today())
            ->count();

        $stats['qr_total'] = DB::table('qr_visits')
            ->where('tenant_id', $tenantId)
            ->count();

        $reviewStats = DB::table('reviews')
            ->where('tenant_id', $tenantId)
            ->selectRaw('COUNT(*) as total, COALESCE(AVG(rating), 0) as avg_rating')
            ->first();

        $stats['reviews_count'] = $reviewStats->total;
        $stats['reviews_avg']   = round($reviewStats->avg_rating, 1);

        return view('dashboard.index', compact('tenant', 'stats'));
    }
}
