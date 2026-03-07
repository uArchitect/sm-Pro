<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            'firma_adi'    => 'required|string|max:255',
            'restoran_adi' => 'required|string|max:255',
            'restoran_adresi' => 'required|string|max:255',
            'restoran_telefonu' => 'required|string|max:255',
        ], [
            'firma_adi.required'    => __('validation.required.firma_adi'),
            'restoran_adi.required' => __('validation.required.restoran_adi'),
            'restoran_adresi.required' => __('validation.required.restoran_adresi'),
            'restoran_telefonu.required' => __('validation.required.restoran_telefonu'),
        ]);

        $tenantId = session('tenant_id');

        DB::table('tenants')->where('id', $tenantId)->update([
            'firma_adi'    => $request->firma_adi,
            'restoran_adi' => $request->restoran_adi,
            'restoran_adresi' => $request->restoran_adresi, 
            'restoran_telefonu' => $request->restoran_telefonu,
            'updated_at'   => now(),
        ]);

        Log::info('Tenant bilgileri güncellendi.', ['tenant_id' => $tenantId]);

        return redirect()->route('company.edit')->with('success', __('messages.tenant_updated'));
    }
}
