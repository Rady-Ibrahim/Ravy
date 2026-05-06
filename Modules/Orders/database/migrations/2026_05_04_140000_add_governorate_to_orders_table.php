<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('governorate_id')->nullable()->after('user_id')->constrained('governorates')->nullOnDelete();
            $table->decimal('shipping_calculated_cost', 8, 2)->nullable()->after('shipping_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['governorate_id']);
            $table->dropColumn(['governorate_id', 'shipping_calculated_cost']);
        });
    }
};
