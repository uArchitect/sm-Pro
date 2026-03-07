<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $tenantId = session('tenant_id');
        $users = DB::table('users')
            ->where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:admin,personel',
        ], [
            'name.required'     => 'Ad soyad zorunludur.',
            'email.required'    => 'E-posta zorunludur.',
            'email.email'       => 'Geçerli bir e-posta adresi girin.',
            'email.unique'      => 'Bu e-posta adresi zaten kullanılıyor.',
            'password.required' => 'Şifre zorunludur.',
            'password.min'      => 'Şifre en az 8 karakter olmalıdır.',
            'password.confirmed'=> 'Şifreler eşleşmiyor.',
            'role.required'     => 'Rol seçimi zorunludur.',
            'role.in'           => 'Geçersiz rol.',
        ]);

        $tenantId = session('tenant_id');

        $userId = DB::table('users')->insertGetId([
            'tenant_id'  => $tenantId,
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('Yeni personel eklendi.', [
            'tenant_id'  => $tenantId,
            'new_user_id'=> $userId,
        ]);

        return redirect()->route('users.index')->with('success', __('messages.staff_added'));
    }

    public function destroy(int $id)
    {
        $tenantId = session('tenant_id');

        $user = DB::table('users')->where('id', $id)->where('tenant_id', $tenantId)->first();

        if (!$user) {
            abort(404);
        }

        if ($user->role === 'owner') {
            return redirect()->route('users.index')->withErrors(['delete' => __('messages.no_owner_delete')]);
        }

        DB::table('users')->where('id', $id)->where('tenant_id', $tenantId)->delete();

        Log::info('Personel silindi.', [
            'tenant_id'      => $tenantId,
            'deleted_user_id'=> $id,
        ]);

        return redirect()->route('users.index')->with('success', __('messages.staff_deleted'));
    }
}
