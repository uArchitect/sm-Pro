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
            'names'   => 'required|array',
            'names.*' => 'nullable|string|max:100',
        ], [], ['names.*' => __('reservation.zone_name')]);

        $names = array_values(array_filter(array_map(function ($n) {
            return is_string($n) ? trim($n) : '';
        }, (array) $request->names)));

        if (empty($names)) {
            return back()->withErrors(['name' => __('reservation.at_least_one_zone')])->withInput();
        }

        $tenantId = session('tenant_id');
        $maxOrder = DB::table('reservation_zones')->where('tenant_id', $tenantId)->max('sort_order') ?? 0;

        $now = now();
        $rows = [];
        foreach ($names as $i => $name) {
            if ($name === '') {
                continue;
            }
            $rows[] = [
                'tenant_id'   => $tenantId,
                'name'        => $name,
                'sort_order'  => $maxOrder + $i + 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        if (!empty($rows)) {
            DB::table('reservation_zones')->insert($rows);
        }

        $count = count($rows);
        $message = $count === 1 ? __('reservation.zone_saved') : __('reservation.zones_saved', ['count' => $count]);
        return redirect()->route('reservation.zones.index')->with('success', $message);
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
