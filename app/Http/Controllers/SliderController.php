<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        $tenantId = session('tenant_id');

        $sliders = DB::table('sliders')
            ->where('tenant_id', $tenantId)
            ->orderBy('sort_order')
            ->get();

        return view('sliders.index', compact('sliders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image'       => 'required|file|mimes:jpg,jpeg,png,gif,webp,svg|max:4096',
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $tenantId = session('tenant_id');
        $maxOrder = DB::table('sliders')->where('tenant_id', $tenantId)->max('sort_order') ?? 0;

        $path = null;

        try {
            $path = $request->file('image')->store("tenants/{$tenantId}/sliders", 'public');
            if ($path === false) {
                return back()->withErrors(['image' => __('messages.upload_failed')]);
            }

            DB::transaction(function () use ($tenantId, $request, $path, $maxOrder) {
                DB::table('sliders')->insert([
                    'tenant_id'   => $tenantId,
                    'image'       => $path,
                    'title'       => $request->title,
                    'description' => $request->description,
                    'sort_order'  => $maxOrder + 1,
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

        return back()->with('success', __('sliders.saved'));
    }

    public function destroy(int $id)
    {
        $tenantId = session('tenant_id');

        $slider = DB::table('sliders')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$slider) {
            abort(404);
        }

        DB::transaction(function () use ($id, $tenantId) {
            DB::table('sliders')->where('id', $id)->where('tenant_id', $tenantId)->delete();
        });

        Storage::disk('public')->delete($slider->image);

        return back()->with('success', __('sliders.deleted'));
    }

    public function reorder(Request $request)
    {
        $tenantId = session('tenant_id');
        $order = $request->input('order', []);

        foreach ($order as $i => $id) {
            DB::table('sliders')
                ->where('id', $id)
                ->where('tenant_id', $tenantId)
                ->update(['sort_order' => $i + 1, 'updated_at' => now()]);
        }

        return response()->json(['success' => true]);
    }
}
