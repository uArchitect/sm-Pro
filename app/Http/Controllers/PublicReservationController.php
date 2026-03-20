<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicReservationController extends Controller
{
    public function form(int $tenantId)
    {
        $tenant = DB::table('tenants')->where('id', $tenantId)->where('is_active', true)->first();
        if (!$tenant) abort(404);

        $hasPremium = in_array($tenant->package ?? 'free', ['premium', 'enterprise']);
        if (!$hasPremium) abort(404);

        $zones = DB::table('reservation_zones')
            ->where('tenant_id', $tenantId)
            ->orderBy('sort_order')
            ->get();

        $tables = DB::table('reservation_tables')
            ->where('tenant_id', $tenantId)
            ->orderBy('zone_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('zone_id');

        $locale = app()->getLocale();

        return view('public.reservation', compact('tenant', 'zones', 'tables', 'locale'));
    }

    public function store(Request $request, int $tenantId)
    {
        $tenant = DB::table('tenants')->where('id', $tenantId)->where('is_active', true)->first();
        if (!$tenant) abort(404);

        $hasPremium = in_array($tenant->package ?? 'free', ['premium', 'enterprise']);
        if (!$hasPremium) abort(404);

        $locale = app()->getLocale();

        $data = $request->validate([
            'table_id'         => 'required|integer',
            'guest_count'      => 'required|integer|min:1|max:999',
            'customer_name'    => 'required|string|max:120',
            'customer_phone'   => 'required|regex:/^[0-9\+\(\)\s\-]{7,20}$/',
            'customer_email'   => 'nullable|email|max:120',
            'reservation_date' => 'required|date|after_or_equal:today',
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i|after:start_time',
            'notes'            => 'nullable|string|max:500',
        ], [
            'customer_phone.regex' => $locale === 'tr'
                ? 'Lütfen geçerli bir telefon numarası girin.'
                : 'Please enter a valid phone number.',
        ]);

        $table = DB::table('reservation_tables')
            ->where('id', $data['table_id'])
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$table) {
            return back()->withErrors(['table_id' => __('reservation.table_not_available')])->withInput();
        }

        // Kapasite kontrolü: misafir sayısı masanın kapasitesini aşamaz
        if ($data['guest_count'] > $table->capacity) {
            $msg = app()->getLocale() === 'tr'
                ? "Bu masa en fazla {$table->capacity} kişiliktir."
                : "This table has a maximum capacity of {$table->capacity} persons.";
            return back()->withErrors(['guest_count' => $msg])->withInput();
        }

        $conflictFound = false;

        DB::transaction(function () use ($tenantId, $data, &$conflictFound) {
            // lockForUpdate: eş zamanlı iki isteğin aynı anda çakışma kontrolünden
            // geçip çift rezervasyon oluşturmasını engeller (race condition fix).
            $conflict = DB::table('reservations')
                ->where('tenant_id', $tenantId)
                ->where('table_id', $data['table_id'])
                ->where('reservation_date', $data['reservation_date'])
                ->where('status', '!=', 'cancelled')
                ->where(function ($q) use ($data) {
                    $q->where('start_time', '<', $data['end_time'])
                      ->where('end_time', '>', $data['start_time']);
                })
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                $conflictFound = true;
                return;
            }

            $reservationId = DB::table('reservations')->insertGetId([
                'tenant_id'        => $tenantId,
                'table_id'         => $data['table_id'],
                'guest_count'      => $data['guest_count'],
                'customer_name'    => $data['customer_name'],
                'customer_phone'   => $data['customer_phone'],
                'customer_email'   => $data['customer_email'] ?? null,
                'reservation_date' => $data['reservation_date'],
                'start_time'       => $data['start_time'],
                'end_time'         => $data['end_time'],
                'status'           => 'pending',
                'notes'            => $data['notes'] ?? null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            DB::table('reservation_notifications')->insert([
                'tenant_id'      => $tenantId,
                'reservation_id' => $reservationId,
                'type'           => 'new_reservation',
                'is_read'        => false,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        });

        if ($conflictFound) {
            return back()->withErrors(['table_id' => __('reservation.time_conflict')])->withInput();
        }

        return back()->with('reservation_success', true);
    }
}
