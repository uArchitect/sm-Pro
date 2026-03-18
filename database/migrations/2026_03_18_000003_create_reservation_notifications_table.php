<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->string('type', 30)->default('new_reservation');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            $table->index(['tenant_id', 'is_read', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_notifications');
    }
};
