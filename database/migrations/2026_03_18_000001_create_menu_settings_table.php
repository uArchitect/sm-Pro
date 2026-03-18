<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->unique();
            $table->enum('layout', ['accordion', 'tabs', 'grid', 'elegant'])->default('accordion');
            $table->string('primary_color', 7)->default('#4F46E5');
            $table->string('secondary_color', 7)->default('#6366F1');
            $table->string('background_color', 7)->default('#f8fafc');
            $table->string('card_color', 7)->default('#ffffff');
            $table->string('text_color', 7)->default('#1e293b');
            $table->string('header_bg', 7)->default('#ffffff');
            $table->string('header_text_color', 7)->default('#0f172a');
            $table->string('font_family', 50)->default('Inter');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_settings');
    }
};
