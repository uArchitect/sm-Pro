<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rezervasyon sistemi (Premium): Bölgeler → Masalar → Rezervasyonlar
     */
    public function up(): void
    {
        // Bölgeler (Cam kenarı, Orta bölge, vb.)
        Schema::create('reservation_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name', 100);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['tenant_id', 'sort_order']);
        });

        // Masalar (her masa bir bölgeye bağlı)
        Schema::create('reservation_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('zone_id')->constrained('reservation_zones')->cascadeOnDelete();
            $table->string('name', 80); // Masa 1, Masa A, vb.
            $table->unsignedSmallInteger('capacity')->default(2)->comment('Kişi kapasitesi');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['tenant_id', 'zone_id']);
        });

        // Rezervasyonlar (müşteri rezervasyonu; saat bazlı dolu/boş için)
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('table_id')->constrained('reservation_tables')->cascadeOnDelete();
            $table->string('customer_name', 120);
            $table->string('customer_phone', 30);
            $table->string('customer_email', 120)->nullable();
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status', 20)->default('pending')->comment('pending, confirmed, cancelled');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'reservation_date']);
            $table->index(['table_id', 'reservation_date', 'start_time', 'end_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('reservation_tables');
        Schema::dropIfExists('reservation_zones');
    }
};
