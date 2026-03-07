<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('ordering_enabled')->default(false)->after('logo');
            $table->string('instagram')->nullable()->after('ordering_enabled');
            $table->string('facebook')->nullable()->after('instagram');
            $table->string('twitter')->nullable()->after('facebook');
            $table->string('whatsapp')->nullable()->after('twitter');
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('customer_name', 100)->nullable();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('qr_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('visited_at')->useCurrent();
            $table->index(['tenant_id', 'visited_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_visits');
        Schema::dropIfExists('reviews');

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['ordering_enabled', 'instagram', 'facebook', 'twitter', 'whatsapp']);
        });
    }
};
