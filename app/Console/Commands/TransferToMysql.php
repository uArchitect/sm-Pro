<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TransferToMysql extends Command
{
    protected $signature   = 'db:transfer-sqlite';
    protected $description = 'SQLite\'daki mevcut veriyi MySQL\'e aktarır';

    public function handle(): int
    {
        $sqlitePath = database_path('database.sqlite');

        if (!file_exists($sqlitePath)) {
            $this->error('SQLite dosyası bulunamadı: ' . $sqlitePath);
            return self::FAILURE;
        }

        config(['database.connections.sqlite_src' => [
            'driver'                  => 'sqlite',
            'database'                => $sqlitePath,
            'prefix'                  => '',
            'foreign_key_constraints' => false,
        ]]);

        $src = DB::connection('sqlite_src');

        $this->info('SQLite → MySQL veri aktarımı başlıyor...');
        $this->newLine();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // ── Tenants ──────────────────────────────────────────────
        $tenants = $src->table('tenants')->get()->map(fn($r) => (array)$r)->toArray();
        if (count($tenants)) {
            DB::table('tenants')->truncate();
            foreach (array_chunk($tenants, 100) as $chunk) {
                DB::table('tenants')->insert($chunk);
            }
            $this->line('  ✓ tenants: ' . count($tenants) . ' kayıt');
        }

        // ── Users ─────────────────────────────────────────────────
        $users = $src->table('users')->get()->map(fn($r) => (array)$r)->toArray();
        if (count($users)) {
            DB::table('users')->truncate();
            foreach (array_chunk($users, 100) as $chunk) {
                DB::table('users')->insert($chunk);
            }
            $this->line('  ✓ users: ' . count($users) . ' kayıt');
        }

        // ── Categories ────────────────────────────────────────────
        $categories = $src->table('categories')->get()->map(fn($r) => (array)$r)->toArray();
        if (count($categories)) {
            DB::table('categories')->truncate();
            foreach (array_chunk($categories, 100) as $chunk) {
                DB::table('categories')->insert($chunk);
            }
            $this->line('  ✓ categories: ' . count($categories) . ' kayıt');
        }

        // ── Products ──────────────────────────────────────────────
        $products = $src->table('products')->get()->map(fn($r) => (array)$r)->toArray();
        if (count($products)) {
            DB::table('products')->truncate();
            foreach (array_chunk($products, 100) as $chunk) {
                DB::table('products')->insert($chunk);
            }
            $this->line('  ✓ products: ' . count($products) . ' kayıt');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->newLine();
        $this->info('✅  Aktarım tamamlandı!');

        return self::SUCCESS;
    }
}
