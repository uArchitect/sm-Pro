<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.login', ['showRegister' => true]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'firma_adi'    => 'required|string|max:255',
            'restoran_adi' => 'required|string|max:255',
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:8|confirmed',
        ], [
            'firma_adi.required'    => __('validation.required.firma_adi'),
            'restoran_adi.required' => __('validation.required.restoran_adi'),
            'name.required'         => __('validation.required.name'),
            'email.required'        => __('validation.required.email'),
            'email.email'           => __('validation.required.email.email'),
            'email.unique'          => __('validation.required.email.unique'),
            'password.required'     => __('validation.required.password'),
            'password.min'          => __('validation.required.password.min'),
            'password.confirmed'    => __('validation.required.password.confirmed'),
        ]);

        $tenantId = DB::table('tenants')->insertGetId([
            'firma_adi'    => $request->firma_adi,
            'restoran_adi' => $request->restoran_adi,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $userId = DB::table('users')->insertGetId([
            'tenant_id'  => $tenantId,
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'owner',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = \App\Models\User::find($userId);
        Auth::login($user);

        Log::info('Yeni tenant ve owner kaydı oluşturuldu.', [
            'tenant_id' => $tenantId,
            'user_id'   => $userId,
        ]);

        return redirect()->route('dashboard');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => __('validation.required.email'),
            'email.email'       => __('validation.required.email.email'),
            'password.required' => __('validation.required.password'),
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            Log::warning('Başarısız giriş denemesi.', ['email' => $request->email]);
            throw ValidationException::withMessages([
                'email' => __('validation.auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        Log::info('Kullanıcı giriş yaptı.', [
            'role'      => $user->role,
            'tenant_id' => $user->tenant_id,
            'user_id'   => Auth::id(),
        ]);

        if ($user->role === 'developer') {
            return redirect()->route('developer.index');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Log::info('Kullanıcı çıkış yaptı.', [
            'tenant_id' => Auth::user()?->tenant_id,
            'user_id'   => Auth::id(),
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
