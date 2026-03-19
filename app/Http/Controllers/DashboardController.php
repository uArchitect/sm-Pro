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
        if (!$tenant) {
            abort(404);
        }

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

        $setup = [
            'has_category' => $stats['categories'] > 0,
            'has_product'  => $stats['products'] > 0,
            'has_logo'     => !empty($tenant->logo),
            'has_social'   => !empty($tenant->instagram) || !empty($tenant->facebook)
                           || !empty($tenant->twitter)   || !empty($tenant->whatsapp),
        ];
        $setup['total']     = 4;
        $setup['progress']  = ($setup['has_category'] ? 1 : 0)
                            + ($setup['has_product']  ? 1 : 0)
                            + ($setup['has_logo']     ? 1 : 0)
                            + ($setup['has_social']   ? 1 : 0);
        $setup['completed'] = $setup['progress'] === $setup['total'];

        return view('dashboard.index', compact('tenant', 'stats', 'setup'));
    }
}
