<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function index()
    {
        $tenantId = session('tenant_id');

        $reviews = DB::table('reviews')
            ->where('tenant_id', $tenantId)
            ->orderByDesc('created_at')
            ->get();

        $stats = DB::table('reviews')
            ->where('tenant_id', $tenantId)
            ->selectRaw('COUNT(*) as total, COALESCE(AVG(rating), 0) as avg_rating')
            ->first();

        $distribution = DB::table('reviews')
            ->where('tenant_id', $tenantId)
            ->selectRaw('rating, COUNT(*) as cnt')
            ->groupBy('rating')
            ->pluck('cnt', 'rating')
            ->toArray();

        return view('reviews.index', compact('reviews', 'stats', 'distribution'));
    }

    public function destroy(int $id)
    {
        $tenantId = session('tenant_id');

        $review = DB::table('reviews')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$review) {
            abort(404);
        }

        DB::table('reviews')->where('id', $id)->where('tenant_id', $tenantId)->delete();

        Log::info('Değerlendirme silindi.', ['tenant_id' => $tenantId, 'review_id' => $id]);

        return redirect()->route('reviews.index')->with('success', __('reviews.deleted'));
    }
}
