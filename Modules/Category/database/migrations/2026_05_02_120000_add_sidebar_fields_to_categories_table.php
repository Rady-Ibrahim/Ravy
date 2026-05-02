<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->boolean('show_in_sidebar')->default(true)->after('sort_order');
            $table->integer('menu_order')->default(0)->after('show_in_sidebar');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropColumn(['show_in_sidebar', 'menu_order']);
        });
    }
};
