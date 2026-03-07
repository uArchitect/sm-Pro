<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{
    public function edit()
    {
        $tenant = DB::table('tenants')->find(session('tenant_id'));
        if (!$tenant) {
            abort(404);
        }
        return view('tenant.edit', compact('tenant'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'restoran_adi'      => 'required|string|max:255',
            'restoran_adresi'   => 'nullable|string|max:500',
            'restoran_telefonu' => 'nullable|string|max:30',
            'logo'              => 'nullable|file|mimes:png,svg|max:2048',
            'instagram'         => 'nullable|string|max:255',
            'facebook'          => 'nullable|string|max:255',
            'twitter'           => 'nullable|string|max:255',
            'whatsapp'          => 'nullable|string|max:255',
        ], [
            'restoran_adi.required' => __('validation.required.restoran_adi'),
        ]);

        $tenantId = session('tenant_id');

        $data = [
            'firma_adi'         => $request->restoran_adi,
            'restoran_adi'      => $request->restoran_adi,
            'restoran_adresi'   => $request->restoran_adresi,
            'restoran_telefonu' => $request->restoran_telefonu,
            'ordering_enabled'  => $request->boolean('ordering_enabled'),
            'instagram'         => $request->instagram,
            'facebook'          => $request->facebook,
            'twitter'           => $request->twitter,
            'whatsapp'          => $request->whatsapp,
            'updated_at'        => now(),
        ];

        $tenant = DB::table('tenants')->find($tenantId);

        if ($request->hasFile('logo')) {
            if ($tenant && $tenant->logo) {
                Storage::disk('public')->delete($tenant->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        } elseif ($request->boolean('remove_logo')) {
            if ($tenant && $tenant->logo) {
                Storage::disk('public')->delete($tenant->logo);
            }
            $data['logo'] = null;
        }

        DB::table('tenants')->where('id', $tenantId)->update($data);

        Log::info('Tenant bilgileri güncellendi.', ['tenant_id' => $tenantId]);

        return redirect()->route('company.edit')->with('success', __('messages.tenant_updated'));
    }
}
