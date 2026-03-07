<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DeveloperController extends Controller
{
    /** Platform özet dashboard */
    public function index()
    {
        $this->authDev();

        $tenants = DB::table('tenants')
            ->orderByDesc('created_at')
            ->get();

        // Her tenant için istatistik ekle
        $tenants = $tenants->map(function ($t) {
            $t->user_count     = DB::table('users')->where('tenant_id', $t->id)->count();
            $t->category_count = DB::table('categories')->where('tenant_id', $t->id)->count();
            $t->product_count  = DB::table('products')->where('tenant_id', $t->id)->count();
            $t->owner          = DB::table('users')
                ->where('tenant_id', $t->id)
                ->where('role', 'owner')
                ->first();
            return $t;
        });

        $stats = [
            'total_tenants'    => $tenants->count(),
            'total_users'      => DB::table('users')->where('role', '!=', 'developer')->count(),
            'total_categories' => DB::table('categories')->count(),
            'total_products'   => DB::table('products')->count(),
        ];

        return view('developer.index', compact('tenants', 'stats'));
    }

    /** Belirli bir tenant'ın detay sayfası */
    public function tenant(int $id)
    {
        $this->authDev();

        $tenant = DB::table('tenants')->find($id);
        if (!$tenant) abort(404);

        $users = DB::table('users')
            ->where('tenant_id', $id)
            ->orderBy('role')
            ->get();

        $categories = DB::table('categories')
            ->where('tenant_id', $id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $products = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.tenant_id', $id)
            ->select('products.*', 'categories.name as category_name')
            ->orderBy('products.sort_order')
            ->get();

        return view('developer.tenant', compact('tenant', 'users', 'categories', 'products'));
    }

    /** Tenant'ı sil (cascade: users, categories, products) */
    public function destroyTenant(int $id)
    {
        $this->authDev();

        DB::table('products')->where('tenant_id', $id)->delete();
        DB::table('categories')->where('tenant_id', $id)->delete();
        DB::table('users')->where('tenant_id', $id)->delete();
        DB::table('tenants')->where('id', $id)->delete();

        return redirect()->route('developer.index')->with('success', __('messages.tenant_deleted'));
    }

    /** Developer hesabı oluşturma / şifre değiştirme formu */
    public function settings()
    {
        $this->authDev();
        $dev = Auth::user();
        return view('developer.settings', compact('dev'));
    }

    public function updateSettings(Request $request)
    {
        $this->authDev();

        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email,' . Auth::id(),
            'password'              => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name'       => $request->name,
            'email'      => $request->email,
            'updated_at' => now(),
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', Auth::id())->update($data);

        return back()->with('success', __('messages.settings_updated'));
    }

    /** Guard: sadece developer rolü erişebilir */
    private function authDev(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'developer') {
            abort(403);
        }
    }
}
