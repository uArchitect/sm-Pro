<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rezervasyon tablolarında Türkçe karakter (ı, ğ, ü, ş vb.) hatası için
     * tabloları utf8mb4 yapıyor.
     */
    public function up(): void
    {
        $tables = ['reservation_zones', 'reservation_tables', 'reservations'];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            DB::statement("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }
    }

    public function down(): void
    {
        // Geri almak gerekirse latin1 yapılabilir; genelde bırakılır.
    }
};
