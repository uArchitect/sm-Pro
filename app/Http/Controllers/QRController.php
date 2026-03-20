<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\ShortLinkService;

class QRController extends Controller
{
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

    public function menuQr()
    {
        $tenantId = session('tenant_id');
        $tenant   = DB::table('tenants')->find($tenantId);

        if (!$tenant) {
            abort(404);
        }

        $menuUrl = route('public.menu', ['tenantId' => $tenantId]);
        $qrCode  = QrCode::format('svg')->size(300)->margin(1)->generate($menuUrl);

        // Kısa link yoksa ilk ziyarette otomatik oluştur
        if (empty($tenant->short_link)) {
            $short = app(ShortLinkService::class)->shorten($menuUrl);
            if ($short) {
                DB::table('tenants')->where('id', $tenantId)->update(['short_link' => $short]);
                $tenant->short_link = $short;
            }
        }

        return view('qr.menu', compact('tenant', 'qrCode', 'menuUrl'));
    }

    public function regenerateShortLink(): \Illuminate\Http\JsonResponse
    {
        $tenantId = session('tenant_id');

        // Mevcut short_link'i sil
        DB::table('tenants')->where('id', $tenantId)->update(['short_link' => null]);

        $menuUrl = route('public.menu', ['tenantId' => $tenantId]);
        $short   = app(ShortLinkService::class)->shorten($menuUrl);

        if ($short) {
            DB::table('tenants')->where('id', $tenantId)->update(['short_link' => $short]);
        }

        return response()->json([
            'success'    => (bool) $short,
            'short_link' => $short,
        ]);
    }

    public function publicProduct(int $tenantId, int $productId)
    {
        $tenant = DB::table('tenants')->find($tenantId);
        if (!$tenant) {
            abort(404);
        }

        if (!$tenant->is_active) {
            abort(503, __('messages.tenant_not_available'));
        }

        $product = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.id', $productId)
            ->where('products.tenant_id', $tenantId)
            ->select('products.*', 'categories.name as category_name')
            ->first();

        if (!$product) {
            abort(404);
        }

        $this->trackProductView($tenantId, $productId);

        return view('public.product', compact('product', 'tenant'));
    }

    public function publicMenu(int $tenantId)
    {
        $tenant = DB::table('tenants')->find($tenantId);

        if (!$tenant) {
            abort(404);
        }

        if (!$tenant->is_active) {
            abort(503, __('messages.tenant_not_available'));
        }

        $this->trackVisit($tenantId);

        $categories = DB::table('categories')
            ->where('tenant_id', $tenantId)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $subCategories = DB::table('categories')
            ->where('tenant_id', $tenantId)
            ->whereNotNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('parent_id');

        $allCategoryIds = DB::table('categories')
            ->where('tenant_id', $tenantId)
            ->pluck('id');

        $products = DB::table('products')
            ->where('tenant_id', $tenantId)
            ->whereIn('category_id', $allCategoryIds)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('category_id');

        $sliders = collect();
        $activeEvent = null;

        $isPremiumTenant = in_array($tenant->package ?? 'basic', ['premium', 'enterprise']);

        if ($isPremiumTenant) {
            $sliders = DB::table('sliders')
                ->where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            $activeEvent = DB::table('events')
                ->where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->where('start_date', '<=', today())
                ->where(function ($q) {
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>=', today());
                })
                ->orderByDesc('created_at')
                ->first();
        }

        $isDemoMenu = isset($tenant->restoran_adi) && $tenant->restoran_adi === 'Fake RESTORANT';

        $menuSettings = null;
        $hasReservation = false;
        if ($isPremiumTenant) {
            $menuSettings = DB::table('menu_settings')
                ->where('tenant_id', $tenantId)
                ->first();

            $hasReservation = DB::table('reservation_zones')
                ->where('tenant_id', $tenantId)
                ->exists();
        }

        return view('public.menu', compact(
            'tenant', 'categories', 'subCategories', 'products',
            'sliders', 'activeEvent', 'isDemoMenu', 'menuSettings', 'hasReservation'
        ));
    }

    public function submitReview(Request $request, int $tenantId)
    {
        $tenant = DB::table('tenants')->find($tenantId);
        if (!$tenant || !$tenant->is_active) {
            abort(404);
        }

        $request->validate([
            'customer_name' => 'nullable|string|max:100',
            'rating'        => 'required|integer|min:1|max:5',
            'comment'       => 'nullable|string|max:1000',
        ]);

        $ip = $request->ip();
        $alreadyReviewed = DB::table('reviews')
            ->where('tenant_id', $tenantId)
            ->where('ip_address', $ip)
            ->whereDate('created_at', today())
            ->exists();

        if ($alreadyReviewed) {
            return redirect()->route('public.menu', $tenantId)
                ->with('review_error', 'already_reviewed');
        }

        DB::table('reviews')->insert([
            'tenant_id'     => $tenantId,
            'customer_name' => $request->customer_name ?: null,
            'rating'        => $request->rating,
            'comment'       => $request->comment ?: null,
            'ip_address'    => $ip,
            'created_at'    => now(),
        ]);

        return redirect()->route('public.menu', $tenantId)
            ->with('review_success', true);
    }

    private function trackProductView(int $tenantId, int $productId): void
    {
        $ip = request()->ip();

        $alreadyViewed = DB::table('product_views')
            ->where('tenant_id', $tenantId)
            ->where('product_id', $productId)
            ->where('ip_address', $ip)
            ->whereDate('viewed_at', today())
            ->exists();

        if (!$alreadyViewed) {
            DB::table('product_views')->insert([
                'tenant_id'  => $tenantId,
                'product_id' => $productId,
                'ip_address' => $ip,
                'viewed_at'  => now(),
            ]);
        }
    }

    private function trackVisit(int $tenantId): void
    {
        $ip = request()->ip();

        $alreadyVisited = DB::table('qr_visits')
            ->where('tenant_id', $tenantId)
            ->where('ip_address', $ip)
            ->whereDate('visited_at', today())
            ->exists();

        if (!$alreadyVisited) {
            DB::table('qr_visits')->insert([
                'tenant_id'  => $tenantId,
                'ip_address' => $ip,
                'visited_at' => now(),
            ]);
        }
    }
}
