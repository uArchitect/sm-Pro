<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $tenantId = session('tenant_id');

        $events = DB::table('events')
            ->where('tenant_id', $tenantId)
            ->orderByDesc('start_date')
            ->get();

        return view('events.index', compact('events'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'image'       => 'nullable|image|max:4096',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $tenantId = session('tenant_id');
        $path = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store("tenants/{$tenantId}/events", 'public');
        }

        DB::table('events')->insert([
            'tenant_id'   => $tenantId,
            'title'       => $request->title,
            'description' => $request->description,
            'image'       => $path,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()->route('events.index')->with('success', __('events.saved'));
    }

    public function edit(int $id)
    {
        $tenantId = session('tenant_id');

        $event = DB::table('events')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$event) {
            abort(404);
        }

        return view('events.edit', compact('event'));
    }

    public function update(Request $request, int $id)
    {
        $tenantId = session('tenant_id');

        $event = DB::table('events')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$event) {
            abort(404);
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'image'       => 'nullable|image|max:4096',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'is_active'   => 'nullable',
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'is_active'   => $request->has('is_active') ? 1 : 0,
            'updated_at'  => now(),
        ];

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $request->file('image')->store("tenants/{$tenantId}/events", 'public');
        }

        DB::table('events')->where('id', $id)->update($data);

        return redirect()->route('events.index')->with('success', __('events.updated'));
    }

    public function destroy(int $id)
    {
        $tenantId = session('tenant_id');

        $event = DB::table('events')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$event) {
            abort(404);
        }

        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        DB::table('events')->where('id', $id)->delete();

        return redirect()->route('events.index')->with('success', __('events.deleted'));
    }
}
