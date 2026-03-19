<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuSettingsController extends Controller
{
    private const DEFAULTS = [
        'layout'            => 'accordion',
        'primary_color'     => '#4F46E5',
        'secondary_color'   => '#6366F1',
        'background_color'  => '#f8fafc',
        'card_color'        => '#ffffff',
        'text_color'        => '#1e293b',
        'header_bg'         => '#ffffff',
        'header_text_color' => '#0f172a',
        'font_family'       => 'Inter',
        'show_review'        => true,
        'show_lang_switcher' => true,
        'show_search'        => true,
        'show_category_pills'=> true,
        'show_address'       => true,
        'show_social'        => true,
        'show_footer'        => true,
    ];

    private const TOGGLE_FIELDS = [
        'show_review',
        'show_lang_switcher',
        'show_search',
        'show_category_pills',
        'show_address',
        'show_social',
        'show_footer',
    ];

    public function index()
    {
        $tenantId = session('tenant_id');
        if (!$tenantId) abort(403);

        $tenant = DB::table('tenants')->find($tenantId);
        if (!$tenant) abort(404);

        $settings = DB::table('menu_settings')
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$settings) {
            $settings = (object) array_merge(self::DEFAULTS, ['tenant_id' => $tenantId]);
        }

        return view('menu-settings.index', compact('settings', 'tenant'));
    }

    public function update(Request $request)
    {
        $tenantId = session('tenant_id');
        if (!$tenantId) abort(403);

        $data = $request->validate([
            'layout'            => 'required|in:accordion,tabs,grid,elegant',
            'primary_color'     => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color'   => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'background_color'  => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'card_color'        => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'text_color'        => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'header_bg'         => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'header_text_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'font_family'       => 'required|string|max:50',
        ]);

        foreach (self::TOGGLE_FIELDS as $field) {
            $data[$field] = $request->boolean($field);
        }

        $exists = DB::table('menu_settings')->where('tenant_id', $tenantId)->exists();

        if ($exists) {
            DB::table('menu_settings')
                ->where('tenant_id', $tenantId)
                ->update(array_merge($data, ['updated_at' => now()]));
        } else {
            DB::table('menu_settings')->insert(
                array_merge($data, [
                    'tenant_id'  => $tenantId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        return redirect()->route('menu-settings.index')
            ->with('success', __('menu_settings.saved'));
    }
}
