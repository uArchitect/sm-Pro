<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRController extends Controller
{
    /**
     * Ürüne özel QR kodu göster (dashboard içi, auth gerekli)
     */
    public function show(int $id)
    {
        $tenantId = session('tenant_id');
        $product  = DB::table('products')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$product) {
            abort(404);
        }

        $url    = route('public.product', ['tenantId' => $tenantId, 'productId' => $id]);
        $qrCode = QrCode::format('svg')->size(260)->margin(1)->generate($url);

        return view('products.qr', compact('product', 'qrCode', 'url'));
    }

    /**
     * Tenant'ın kalıcı menü QR sayfası (dashboard içi, auth gerekli)
     * URL: /menu/{tenantId}  →  baskıya hazır, değişmez.
     */
    public function menuQr()
    {
        $tenantId = session('tenant_id');
        $tenant   = DB::table('tenants')->find($tenantId);

        $menuUrl = route('public.menu', ['tenantId' => $tenantId]);
        $qrCode  = QrCode::format('svg')->size(300)->margin(1)->generate($menuUrl);

        return view('qr.menu', compact('tenant', 'qrCode', 'menuUrl'));
    }

    /**
     * Public ürün detay sayfası (QR tarama sonrası, auth yok)
     */
    public function publicProduct(int $tenantId, int $productId)
    {
        $product = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.id', $productId)
            ->where('products.tenant_id', $tenantId)
            ->select('products.*', 'categories.name as category_name')
            ->first();

        if (!$product) {
            abort(404);
        }

        $tenant = DB::table('tenants')->find($tenantId);

        return view('public.product', compact('product', 'tenant'));
    }

    /**
     * Public tam menü sayfası (QR tarama sonrası, auth yok)
     * URL: /menu/{tenantId}  →  kalıcı, değişmez
     */
    public function publicMenu(int $tenantId)
    {
        $tenant = DB::table('tenants')->find($tenantId);

        if (!$tenant) {
            abort(404);
        }

        $categories = DB::table('categories')
            ->where('tenant_id', $tenantId)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $products = DB::table('products')
            ->where('tenant_id', $tenantId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('category_id');

        return view('public.menu', compact('tenant', 'categories', 'products'));
    }
}
