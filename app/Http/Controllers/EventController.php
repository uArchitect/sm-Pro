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
            'image'       => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:4096',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $tenantId = session('tenant_id');
        $path = null;

        try {
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store("tenants/{$tenantId}/events", 'public');
                if ($path === false) {
                    return back()->withErrors(['image' => __('messages.upload_failed')])->withInput();
                }
            }

            DB::transaction(function () use ($tenantId, $request, $path) {
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
            });
        } catch (\Throwable $e) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }

            throw $e;
        }

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
            'image'       => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:4096',
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

        $newImagePath = null;
        $oldImageToDelete = null;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $request->file('image')->store("tenants/{$tenantId}/events", 'public');
                if ($newImagePath === false) {
                    return back()->withErrors(['image' => __('messages.upload_failed')])->withInput();
                }
                $data['image'] = $newImagePath;
                $oldImageToDelete = $event->image ?: null;
            }

            DB::transaction(function () use ($id, $tenantId, $data) {
                DB::table('events')->where('id', $id)->where('tenant_id', $tenantId)->update($data);
            });

            if ($oldImageToDelete) {
                Storage::disk('public')->delete($oldImageToDelete);
            }
        } catch (\Throwable $e) {
            if ($newImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $e;
        }

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

        DB::transaction(function () use ($id, $tenantId) {
            DB::table('events')->where('id', $id)->where('tenant_id', $tenantId)->delete();
        });

        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        return redirect()->route('events.index')->with('success', __('events.deleted'));
    }
}
