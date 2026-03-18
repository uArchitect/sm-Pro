<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_settings', function (Blueprint $table) {
            $table->boolean('show_review')->default(true)->after('font_family');
            $table->boolean('show_lang_switcher')->default(true)->after('show_review');
            $table->boolean('show_search')->default(true)->after('show_lang_switcher');
            $table->boolean('show_category_pills')->default(true)->after('show_search');
            $table->boolean('show_address')->default(true)->after('show_category_pills');
            $table->boolean('show_social')->default(true)->after('show_address');
            $table->boolean('show_footer')->default(true)->after('show_social');
        });
    }

    public function down(): void
    {
        Schema::table('menu_settings', function (Blueprint $table) {
            $table->dropColumn([
                'show_review',
                'show_lang_switcher',
                'show_search',
                'show_category_pills',
                'show_address',
                'show_social',
                'show_footer',
            ]);
        });
    }
};
