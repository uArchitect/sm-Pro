<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

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
            Storage::disk('uploads')->delete($path);
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

    /**
     * Run storage:link from a secure developer route.
     */
    public function runStorageLink()
    {
        $this->authDev();

        // Sadece production veya staging'de çalıştırmak istiyorsan buraya env kontrolü ekleyebilirsin.
        try {
            Artisan::call('storage:link');
            $output = Artisan::output();
        } catch (\Throwable $e) {
            return back()->with('error', 'storage:link komutu çalıştırılamadı: ' . $e->getMessage());
        }

        return back()->with('success', 'storage:link komutu çalıştı. Çıktı: ' . trim($output));
    }

    /**
     * Önbellek temizleme (cache, view, config, route veya hepsi).
     */
    public function clearCache(Request $request)
    {
        $this->authDev();

        $action = $request->input('action', 'all');

        $commands = [
            'cache'  => ['cache:clear', 'Uygulama önbelleği temizlendi.'],
            'view'   => ['view:clear', 'View önbelleği temizlendi.'],
            'config' => ['config:clear', 'Config önbelleği temizlendi.'],
            'route'  => ['route:clear', 'Route önbelleği temizlendi.'],
            'all'    => ['optimize:clear', 'Tüm önbellekler temizlendi.'],
        ];

        if (!isset($commands[$action])) {
            return back()->with('error', 'Geçersiz işlem.');
        }

        try {
            Artisan::call($commands[$action][0]);
            $msg = $commands[$action][1];
            $out = trim(Artisan::output());
            if ($out) {
                $msg .= ' ' . $out;
            }

            if (function_exists('opcache_reset')) {
                opcache_reset();
                $msg .= ' OPcache sıfırlandı.';
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            return back()->with('error', 'Önbellek temizlenemedi: ' . $e->getMessage());
        }
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

        $messages = $this->buildTicketConversation($ticket);

        return view('developer.ticket-show', compact('ticket', 'messages'));
    }

    public function ticketReply(Request $request, int $id)
    {
        $this->authDev();

        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $ticket = DB::table('support_tickets')->find($id);
        if (!$ticket) abort(404);

        $thread = $this->decodeSupportThread((string) ($ticket->admin_reply ?? ''));
        $thread[] = [
            'sender'   => 'developer',
            'user_id'  => Auth::id(),
            'name'     => Auth::user()?->name ?? 'Developer',
            'message'  => trim((string) $request->message),
            'datetime' => now()->toIso8601String(),
        ];

        DB::table('support_tickets')->where('id', $id)->update([
            'admin_reply' => json_encode($thread, JSON_UNESCAPED_UNICODE),
            'status'      => 'answered',
            'replied_at'  => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('success', __('support.reply_sent'));
    }

    /**
     * Web-based migration manager — lists pending & ran migrations, runs forward-only.
     */
    public function migrations()
    {
        $this->authDev();

        $migrationPath = database_path('migrations');
        $files = collect(File::glob($migrationPath . '/*.php'))
            ->map(fn ($f) => pathinfo($f, PATHINFO_FILENAME))
            ->sort()
            ->values();

        $ran = $this->getRanMigrations();

        $pending = $files->filter(fn ($f) => !$ran->contains('migration', $f))
            ->map(fn ($file) => (object) [
                'name' => $file,
                'ran'  => false,
            ])
            ->values();

        $pendingCount = $pending->count();
        $ranCount     = $ran->count();
        $lastBatch    = $ran->max('batch') ?? 0;

        return view('developer.migrations', compact('pending', 'pendingCount', 'ranCount', 'lastBatch'));
    }

    /**
     * Run pending migrations — all or a single specified file. Forward-only, never destructive.
     */
    public function runMigrations(Request $request)
    {
        $this->authDev();

        $file = $request->input('file');

        if ($file && !preg_match('/^[\w\-]+$/', $file)) {
            return back()->with('error', 'Geçersiz dosya adı.');
        }

        if (!Schema::hasTable('migrations')) {
            try {
                Artisan::call('migrate:install');
            } catch (\Throwable $e) {
                return back()->with('error', 'migrations tablosu oluşturulamadı: ' . $e->getMessage());
            }
        }

        if ($file) {
            return $this->runSingleMigration($file);
        }

        return $this->runAllPendingMigrations();
    }

    private function runSingleMigration(string $file)
    {
        $fullPath = database_path('migrations/' . $file . '.php');
        if (!File::exists($fullPath)) {
            return back()->with('error', 'Migration dosyası bulunamadı: ' . $file);
        }

        $ran = $this->getRanMigrations();
        if ($ran->contains('migration', $file)) {
            return back()->with('error', 'Bu migration zaten çalıştırılmış: ' . $file);
        }

        $relativePath = 'database/migrations/' . $file . '.php';
        try {
            Artisan::call('migrate', [
                '--path'  => $relativePath,
                '--force' => true,
            ]);
            $output = trim(Artisan::output());
            return back()->with('success', "Migration çalıştırıldı: {$file}" . ($output ? "\n" . $output : ''));
        } catch (\Throwable $e) {
            if ($this->isTableExistsError($e)) {
                $this->markMigrationAsRan($file);
                return back()->with('success', "Tablo zaten mevcut, migration kayıt altına alındı: {$file}");
            }
            return back()->with('error', "Migration hatası ({$file}): " . $e->getMessage());
        }
    }

    private function runAllPendingMigrations()
    {
        $ran = $this->getRanMigrations();
        $files = collect(File::glob(database_path('migrations/*.php')))
            ->map(fn ($f) => pathinfo($f, PATHINFO_FILENAME))
            ->sort()
            ->values();

        $pending = $files->filter(fn ($f) => !$ran->contains('migration', $f));

        if ($pending->isEmpty()) {
            return back()->with('success', 'Bekleyen migration yok, tüm tablolar güncel.');
        }

        $success  = [];
        $skipped  = [];
        $errors   = [];

        foreach ($pending as $file) {
            $relativePath = 'database/migrations/' . $file . '.php';
            try {
                Artisan::call('migrate', [
                    '--path'  => $relativePath,
                    '--force' => true,
                ]);
                $success[] = $file;
            } catch (\Throwable $e) {
                if ($this->isTableExistsError($e)) {
                    $this->markMigrationAsRan($file);
                    $skipped[] = $file;
                } else {
                    $errors[] = $file . ': ' . $e->getMessage();
                }
            }
        }

        $parts = [];
        if (count($success)) {
            $parts[] = count($success) . ' migration yüklendi';
        }
        if (count($skipped)) {
            $parts[] = count($skipped) . ' migration atlandı (tablo zaten mevcuttu)';
        }

        $msg = implode(', ', $parts) . '.';

        if (count($errors)) {
            return back()
                ->with('success', count($parts) ? $msg : null)
                ->with('error', 'Hatalar: ' . implode(' | ', $errors));
        }

        return back()->with('success', $msg);
    }

    private function isTableExistsError(\Throwable $e): bool
    {
        $message = $e->getMessage();
        return str_contains($message, 'already exists')
            || str_contains($message, '42S01');
    }

    private function markMigrationAsRan(string $file): void
    {
        $lastBatch = DB::table('migrations')->max('batch') ?? 0;
        DB::table('migrations')->insert([
            'migration' => $file,
            'batch'     => $lastBatch + 1,
        ]);
    }

    private function getRanMigrations(): \Illuminate\Support\Collection
    {
        try {
            if (!Schema::hasTable('migrations')) {
                return collect();
            }
            return DB::table('migrations')->get();
        } catch (\Throwable) {
            return collect();
        }
    }

    private function authDev(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'developer') 
        {
            abort(403);
        }
    }

    private function buildTicketConversation(object $ticket): array
    {
        $messages = [[
            'sender'   => 'user',
            'name'     => $ticket->user_name ?: 'User',
            'message'  => (string) $ticket->message,
            'datetime' => $ticket->created_at,
        ]];

        $thread = $this->decodeSupportThread((string) ($ticket->admin_reply ?? ''));
        foreach ($thread as $item) {
            if (!is_array($item) || empty($item['message'])) {
                continue;
            }
            $isUser = ($item['sender'] ?? '') === 'user';
            $messages[] = [
                'sender'   => $isUser ? 'user' : 'developer',
                'name'     => $item['name'] ?? ($isUser ? ($ticket->user_name ?: 'User') : 'Developer'),
                'message'  => (string) $item['message'],
                'datetime' => $item['datetime'] ?? $ticket->updated_at,
            ];
        }

        // Backward compatibility for old single reply data
        if (empty($thread) && !empty($ticket->admin_reply)) {
            $messages[] = [
                'sender'   => 'developer',
                'name'     => 'Developer',
                'message'  => (string) $ticket->admin_reply,
                'datetime' => $ticket->replied_at ?? $ticket->updated_at,
            ];
        }

        return $messages;
    }

    private function decodeSupportThread(string $raw): array
    {
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }
}
