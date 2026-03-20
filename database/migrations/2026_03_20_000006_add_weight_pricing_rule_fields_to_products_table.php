<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'base_weight_grams')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedInteger('base_weight_grams')->nullable()->after('weight_grams');
            });
        }

        if (!Schema::hasColumn('products', 'extra_weight_step_grams')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedInteger('extra_weight_step_grams')->nullable()->after('base_weight_grams');
            });
        }

        if (!Schema::hasColumn('products', 'extra_weight_step_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('extra_weight_step_price', 10, 2)->nullable()->after('extra_weight_step_grams');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'extra_weight_step_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('extra_weight_step_price');
            });
        }

        if (Schema::hasColumn('products', 'extra_weight_step_grams')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('extra_weight_step_grams');
            });
        }

        if (Schema::hasColumn('products', 'base_weight_grams')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('base_weight_grams');
            });
        }
    }
};

