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

        return view('dashboard.index', compact('tenant', 'stats'));
    }
}
