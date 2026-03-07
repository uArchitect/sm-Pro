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
        return view('tenant.edit', compact('tenant'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'restoran_adi'      => 'required|string|max:255',
            'restoran_adresi'   => 'required|string|max:255',
            'restoran_telefonu' => 'required|string|max:255',
            'logo'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'instagram'         => 'nullable|string|max:255',
            'facebook'          => 'nullable|string|max:255',
            'twitter'           => 'nullable|string|max:255',
            'whatsapp'          => 'nullable|string|max:255',
        ], [
            'restoran_adi.required'     => __('validation.required.restoran_adi'),
            'restoran_adresi.required'  => __('validation.required.restoran_adresi'),
            'restoran_telefonu.required'=> __('validation.required.restoran_telefonu'),
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

        if ($request->hasFile('logo')) {
            $tenant = DB::table('tenants')->find($tenantId);
            if ($tenant->logo) {
                Storage::disk('public')->delete($tenant->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->boolean('remove_logo') && !$request->hasFile('logo')) {
            $tenant = $tenant ?? DB::table('tenants')->find($tenantId);
            if ($tenant->logo) {
                Storage::disk('public')->delete($tenant->logo);
            }
            $data['logo'] = null;
        }

        DB::table('tenants')->where('id', $tenantId)->update($data);

        Log::info('Tenant bilgileri güncellendi.', ['tenant_id' => $tenantId]);

        return redirect()->route('company.edit')->with('success', __('messages.tenant_updated'));
    }
}
