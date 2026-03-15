<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationZoneController extends Controller
{
    public function index()
    {
        $tenantId = session('tenant_id');

        $zones = DB::table('reservation_zones')
            ->where('tenant_id', $tenantId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $zoneCounts = DB::table('reservation_tables')
            ->where('tenant_id', $tenantId)
            ->selectRaw('zone_id, COUNT(*) as cnt')
            ->groupBy('zone_id')
            ->pluck('cnt', 'zone_id');

        return view('reservation.zones.index', compact('zones', 'zoneCounts'));
    }

    public function create()
    {
        return view('reservation.zones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $tenantId = session('tenant_id');
        $maxOrder = DB::table('reservation_zones')->where('tenant_id', $tenantId)->max('sort_order') ?? 0;

        DB::table('reservation_zones')->insert([
            'tenant_id'   => $tenantId,
            'name'        => $request->name,
            'sort_order'  => $maxOrder + 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()->route('reservation.zones.index')->with('success', __('reservation.zone_saved'));
    }

    public function edit(int $id)
    {
        $tenantId = session('tenant_id');

        $zone = DB::table('reservation_zones')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$zone) {
            abort(404);
        }

        return view('reservation.zones.edit', compact('zone'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $tenantId = session('tenant_id');

        $updated = DB::table('reservation_zones')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->update([
                'name'       => $request->name,
                'updated_at' => now(),
            ]);

        if (!$updated) {
            abort(404);
        }

        return redirect()->route('reservation.zones.index')->with('success', __('reservation.zone_updated'));
    }

    public function destroy(int $id)
    {
        $tenantId = session('tenant_id');

        $zone = DB::table('reservation_zones')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$zone) {
            abort(404);
        }

        // Cascade: zone silinince masalar ve rezervasyonlar da silinir (FK)
        DB::table('reservation_zones')->where('id', $id)->where('tenant_id', $tenantId)->delete();

        return redirect()->route('reservation.zones.index')->with('success', __('reservation.zone_deleted'));
    }
}
