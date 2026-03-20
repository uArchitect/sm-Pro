<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Ürünlere stok durumu
        if (!Schema::hasColumn('products', 'is_available')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('is_available')->default(true)->after('sort_order');
            });
        }

        // 2) Ürün görüntüleme istatistikleri
        if (!Schema::hasTable('product_views')) {
            Schema::create('product_views', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id');
                $table->unsignedBigInteger('product_id');
                $table->string('ip_address', 45)->nullable();
                $table->timestamp('viewed_at');

                $table->index(['tenant_id', 'product_id']);
                $table->index(['product_id', 'viewed_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_views');

        if (Schema::hasColumn('products', 'is_available')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('is_available');
            });
        }
    }
};
