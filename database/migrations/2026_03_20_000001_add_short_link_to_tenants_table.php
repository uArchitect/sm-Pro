<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('tenants', 'short_link')) {
            return;
        }

        Schema::table('tenants', function (Blueprint $table) {
            $table->string('short_link')->nullable()->after('whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('short_link');
        });
    }
};
