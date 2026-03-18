<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationNotificationController extends Controller
{
    public function index()
    {
        $tenantId = session('tenant_id');

        $reservations = DB::table('reservations')
            ->join('reservation_tables', 'reservations.table_id', '=', 'reservation_tables.id')
            ->join('reservation_zones', 'reservation_tables.zone_id', '=', 'reservation_zones.id')
            ->where('reservations.tenant_id', $tenantId)
            ->select(
                'reservations.*',
                'reservation_tables.name as table_name',
                'reservation_tables.capacity',
                'reservation_zones.name as zone_name'
            )
            ->orderByDesc('reservations.created_at')
            ->paginate(20);

        DB::table('reservation_notifications')
            ->where('tenant_id', $tenantId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'updated_at' => now()]);

        return view('reservation.notifications', compact('reservations'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $tenantId = session('tenant_id');

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $updated = DB::table('reservations')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->update([
                'status'     => $request->status,
                'updated_at' => now(),
            ]);

        if (!$updated) abort(404);

        return back()->with('success', __('reservation.status_updated'));
    }

    public function destroy(int $id)
    {
        $tenantId = session('tenant_id');

        DB::table('reservations')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->delete();

        return back()->with('success', __('reservation.reservation_deleted'));
    }

    public function unreadCount()
    {
        $tenantId = session('tenant_id');

        $count = DB::table('reservation_notifications')
            ->where('tenant_id', $tenantId)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
