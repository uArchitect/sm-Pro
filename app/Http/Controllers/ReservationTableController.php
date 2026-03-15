<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationTableController extends Controller
{
    public function index()
    {
        $tenantId = session('tenant_id');

        $zones = DB::table('reservation_zones')
            ->where('tenant_id', $tenantId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $tables = DB::table('reservation_tables')
            ->join('reservation_zones', 'reservation_tables.zone_id', '=', 'reservation_zones.id')
            ->where('reservation_tables.tenant_id', $tenantId)
            ->select('reservation_tables.*', 'reservation_zones.name as zone_name')
            ->orderBy('reservation_zones.sort_order')
            ->orderBy('reservation_zones.name')
            ->orderBy('reservation_tables.sort_order')
            ->orderBy('reservation_tables.name')
            ->get();

        return view('reservation.tables.index', compact('zones', 'tables'));
    }

    public function create()
    {
        $tenantId = session('tenant_id');

        $zones = DB::table('reservation_zones')
            ->where('tenant_id', $tenantId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        if ($zones->isEmpty()) {
            return redirect()->route('reservation.zones.index')
                ->with('error', __('reservation.add_zone_first'));
        }

        return view('reservation.tables.create', compact('zones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'zone_id'  => 'required|integer',
            'names'    => 'required|array',
            'names.*'  => 'nullable|string|max:80',
            'capacity' => 'nullable|integer|min:1|max:99',
        ], [], ['names.*' => __('reservation.table_name')]);

        $names = array_values(array_filter(array_map(function ($n) {
            return is_string($n) ? trim($n) : '';
        }, (array) $request->names)));

        if (empty($names)) {
            return back()->withErrors(['name' => __('reservation.at_least_one_table')])->withInput();
        }

        $tenantId = session('tenant_id');

        $zone = DB::table('reservation_zones')
            ->where('id', $request->zone_id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$zone) {
            return back()->withErrors(['zone_id' => __('reservation.zone_not_found')])->withInput();
        }

        $maxOrder = DB::table('reservation_tables')
            ->where('tenant_id', $tenantId)
            ->where('zone_id', $request->zone_id)
            ->max('sort_order') ?? 0;

        $capacity = (int) ($request->capacity ?: 2);
        $now = now();
        $rows = [];
        foreach ($names as $i => $name) {
            if ($name === '') {
                continue;
            }
            $rows[] = [
                'tenant_id'   => $tenantId,
                'zone_id'     => $request->zone_id,
                'name'        => $name,
                'capacity'    => $capacity,
                'sort_order'  => $maxOrder + $i + 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        if (!empty($rows)) {
            DB::table('reservation_tables')->insert($rows);
        }

        $count = count($rows);
        $message = $count === 1 ? __('reservation.table_saved') : __('reservation.tables_saved', ['count' => $count]);
        return redirect()->route('reservation.tables.index')->with('success', $message);
    }

    public function edit(int $id)
    {
        $tenantId = session('tenant_id');

        $table = DB::table('reservation_tables')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$table) {
            abort(404);
        }

        $zones = DB::table('reservation_zones')
            ->where('tenant_id', $tenantId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('reservation.tables.edit', compact('table', 'zones'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'zone_id'  => 'required|integer',
            'name'     => 'required|string|max:80',
            'capacity' => 'nullable|integer|min:1|max:99',
        ]);

        $tenantId = session('tenant_id');

        $zone = DB::table('reservation_zones')
            ->where('id', $request->zone_id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$zone) {
            return back()->withErrors(['zone_id' => __('reservation.zone_not_found')])->withInput();
        }

        $updated = DB::table('reservation_tables')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->update([
                'zone_id'    => $request->zone_id,
                'name'       => $request->name,
                'capacity'   => (int) ($request->capacity ?: 2),
                'updated_at' => now(),
            ]);

        if (!$updated) {
            abort(404);
        }

        return redirect()->route('reservation.tables.index')->with('success', __('reservation.table_updated'));
    }

    public function destroy(int $id)
    {
        $tenantId = session('tenant_id');

        $table = DB::table('reservation_tables')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$table) {
            abort(404);
        }

        DB::table('reservation_tables')->where('id', $id)->where('tenant_id', $tenantId)->delete();

        return redirect()->route('reservation.tables.index')->with('success', __('reservation.table_deleted'));
    }
}
