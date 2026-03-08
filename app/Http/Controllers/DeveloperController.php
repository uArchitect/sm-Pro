<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeveloperController extends Controller
{
    public function index()
    {
        $this->authDev();

        $tenants = DB::table('tenants')
            ->orderByDesc('created_at')
            ->get();

        $tenants = $tenants->map(function ($t) {
            $t->user_count     = DB::table('users')->where('tenant_id', $t->id)->count();
            $t->category_count = DB::table('categories')->where('tenant_id', $t->id)->count();
            $t->product_count  = DB::table('products')->where('tenant_id', $t->id)->count();
            $t->review_count   = DB::table('reviews')->where('tenant_id', $t->id)->count();
            $t->qr_visit_count = DB::table('qr_visits')->where('tenant_id', $t->id)->count();
            $t->owner          = DB::table('users')
                ->where('tenant_id', $t->id)
                ->where('role', 'owner')
                ->first();
            return $t;
        });

        $stats = [
            'total_tenants'    => $tenants->count(),
            'active_tenants'   => $tenants->where('is_active', true)->count(),
            'premium_tenants'  => $tenants->where('package', 'premium')->count(),
            'basic_tenants'    => $tenants->where('package', '!=', 'premium')->count(),
            'total_users'      => DB::table('users')->where('role', '!=', 'developer')->count(),
            'total_categories' => DB::table('categories')->count(),
            'total_products'   => DB::table('products')->count(),
            'total_reviews'    => DB::table('reviews')->count(),
            'total_qr_visits'  => DB::table('qr_visits')->count(),
            'today_qr_visits'  => DB::table('qr_visits')->whereDate('visited_at', today())->count(),
            'today_reviews'    => DB::table('reviews')->whereDate('created_at', today())->count(),
            'new_tenants_week' => DB::table('tenants')->where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $recentReviews = DB::table('reviews')
            ->join('tenants', 'reviews.tenant_id', '=', 'tenants.id')
            ->select('reviews.*', 'tenants.restoran_adi')
            ->orderByDesc('reviews.created_at')
            ->limit(5)
            ->get();

        return view('developer.index', compact('tenants', 'stats', 'recentReviews'));
    }

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

        $reviewStats = DB::table('reviews')
            ->where('tenant_id', $id)
            ->selectRaw('COUNT(*) as total, COALESCE(AVG(rating), 0) as avg_rating')
            ->first();

        $qrStats = [
            'total'   => DB::table('qr_visits')->where('tenant_id', $id)->count(),
            'today'   => DB::table('qr_visits')->where('tenant_id', $id)->whereDate('visited_at', today())->count(),
            'week'    => DB::table('qr_visits')->where('tenant_id', $id)->where('visited_at', '>=', now()->subDays(7))->count(),
        ];

        $recentReviews = DB::table('reviews')
            ->where('tenant_id', $id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('developer.tenant', compact('tenant', 'users', 'categories', 'products', 'reviewStats', 'qrStats', 'recentReviews'));
    }

    /** Toggle tenant active/passive */
    public function toggleTenant(int $id)
    {
        $this->authDev();

        $tenant = DB::table('tenants')->find($id);
        if (!$tenant) abort(404);

        $newStatus = !$tenant->is_active;
        DB::table('tenants')->where('id', $id)->update([
            'is_active'  => $newStatus,
            'updated_at' => now(),
        ]);

        $statusText = $newStatus ? 'aktif' : 'pasif';
        return back()->with('success', "{$tenant->restoran_adi} başarıyla {$statusText} edildi.");
    }

    /** Impersonate: developer logs in as tenant's owner */
    public function impersonate(int $id)
    {
        $this->authDev();

        $tenant = DB::table('tenants')->find($id);
        if (!$tenant) abort(404);

        $owner = DB::table('users')
            ->where('tenant_id', $id)
            ->where('role', 'owner')
            ->first();

        if (!$owner) {
            return back()->with('error', 'Bu restoranın owner hesabı bulunamadı.');
        }

        session(['impersonating_from' => Auth::id()]);

        $user = \App\Models\User::find($owner->id);
        Auth::login($user);
        session(['tenant_id' => $tenant->id]);

        return redirect()->route('dashboard');
    }

    /** Stop impersonating and return to developer account */
    public function stopImpersonate()
    {
        $devId = session('impersonating_from');
        if (!$devId) {
            return redirect()->route('dashboard');
        }

        $dev = \App\Models\User::find($devId);
        if (!$dev || $dev->role !== 'developer') {
            return redirect()->route('dashboard');
        }

        session()->forget('impersonating_from');
        session()->forget('tenant_id');
        Auth::login($dev);

        return redirect()->route('developer.index')->with('success', 'Developer hesabınıza geri döndünüz.');
    }

    /** All users across the platform */
    public function users()
    {
        $this->authDev();

        $users = DB::table('users')
            ->leftJoin('tenants', 'users.tenant_id', '=', 'tenants.id')
            ->select('users.*', 'tenants.restoran_adi')
            ->where('users.role', '!=', 'developer')
            ->orderByDesc('users.created_at')
            ->get();

        return view('developer.users', compact('users'));
    }

    /** Delete a user (non-developer, non-self) */
    public function destroyUser(int $id)
    {
        $this->authDev();

        $user = DB::table('users')->find($id);
        if (!$user || in_array($user->role, ['developer', 'owner'], true)) {
            return back()->with('error', 'Bu kullanıcı silinemez.');
        }

        DB::table('users')->where('id', $id)->delete();

        return back()->with('success', "{$user->name} silindi.");
    }

    /** Update tenant info from developer panel */
    public function updateTenant(Request $request, int $id)
    {
        $this->authDev();

        $tenant = DB::table('tenants')->find($id);
        if (!$tenant) abort(404);

        $request->validate([
            'restoran_adi'      => 'required|string|max:255',
            'restoran_adresi'   => 'nullable|string|max:500',
            'restoran_telefonu' => 'nullable|string|max:30',
            'package'           => 'nullable|in:basic,premium',
        ]);

        DB::table('tenants')->where('id', $id)->update([
            'firma_adi'         => $request->restoran_adi,
            'restoran_adi'      => $request->restoran_adi,
            'restoran_adresi'   => $request->restoran_adresi,
            'restoran_telefonu' => $request->restoran_telefonu,
            'package'           => $request->input('package', $tenant->package ?? 'basic'),
            'updated_at'        => now(),
        ]);

        return back()->with('success', 'Restoran bilgileri güncellendi.');
    }

    public function destroyTenant(int $id)
    {
        $this->authDev();

        $tenant = DB::table('tenants')->find($id);
        if (!$tenant) {
            abort(404);
        }

        $mediaPaths = array_filter(array_merge(
            [$tenant->logo],
            DB::table('sliders')->where('tenant_id', $id)->whereNotNull('image')->pluck('image')->all(),
            DB::table('events')->where('tenant_id', $id)->whereNotNull('image')->pluck('image')->all(),
            DB::table('categories')->where('tenant_id', $id)->whereNotNull('image')->pluck('image')->all(),
            DB::table('products')->where('tenant_id', $id)->whereNotNull('image')->pluck('image')->all(),
        ));

        DB::transaction(function () use ($id) {
            DB::table('support_tickets')->where('tenant_id', $id)->delete();
            DB::table('sliders')->where('tenant_id', $id)->delete();
            DB::table('events')->where('tenant_id', $id)->delete();
            DB::table('reviews')->where('tenant_id', $id)->delete();
            DB::table('qr_visits')->where('tenant_id', $id)->delete();
            DB::table('products')->where('tenant_id', $id)->delete();
            DB::table('categories')->where('tenant_id', $id)->delete();
            DB::table('users')->where('tenant_id', $id)->delete();
            DB::table('tenants')->where('id', $id)->delete();
        });

        foreach ($mediaPaths as $path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()->route('developer.index')->with('success', 'Restoran ve tüm verileri silindi.');
    }

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
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . Auth::id(),
            'password' => 'nullable|string|min:8|confirmed',
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

        return back()->with('success', 'Ayarlar güncellendi.');
    }

    public function togglePackage(int $id)
    {
        $this->authDev();

        $tenant = DB::table('tenants')->find($id);
        if (!$tenant) abort(404);

        $newPackage = ($tenant->package ?? 'basic') === 'premium' ? 'basic' : 'premium';
        DB::table('tenants')->where('id', $id)->update([
            'package'    => $newPackage,
            'updated_at' => now(),
        ]);

        return back()->with('success', "{$tenant->restoran_adi} paketi '{$newPackage}' olarak güncellendi.");
    }

    public function tickets()
    {
        $this->authDev();

        $tickets = DB::table('support_tickets')
            ->join('tenants', 'support_tickets.tenant_id', '=', 'tenants.id')
            ->join('users', 'support_tickets.user_id', '=', 'users.id')
            ->select('support_tickets.*', 'tenants.restoran_adi', 'users.name as user_name')
            ->orderByDesc('support_tickets.created_at')
            ->get();

        return view('developer.tickets', compact('tickets'));
    }

    public function ticketShow(int $id)
    {
        $this->authDev();

        $ticket = DB::table('support_tickets')
            ->join('tenants', 'support_tickets.tenant_id', '=', 'tenants.id')
            ->join('users', 'support_tickets.user_id', '=', 'users.id')
            ->select('support_tickets.*', 'tenants.restoran_adi', 'users.name as user_name', 'users.email as user_email')
            ->where('support_tickets.id', $id)
            ->first();

        if (!$ticket) abort(404);

        return view('developer.ticket-show', compact('ticket'));
    }

    public function ticketReply(Request $request, int $id)
    {
        $this->authDev();

        $request->validate([
            'admin_reply' => 'required|string|max:5000',
        ]);

        $ticket = DB::table('support_tickets')->find($id);
        if (!$ticket) abort(404);

        DB::table('support_tickets')->where('id', $id)->update([
            'admin_reply' => $request->admin_reply,
            'status'      => 'answered',
            'replied_at'  => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('success', __('support.reply_sent'));
    }

    private function authDev(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'developer') 
        {
            abort(403);
        }
    }
}
