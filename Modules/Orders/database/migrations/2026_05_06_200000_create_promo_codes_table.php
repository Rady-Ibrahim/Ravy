<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('discount_type', ['fixed', 'percentage']);
            $table->decimal('discount_value', 8, 2);
            $table->decimal('min_amount', 8, 2)->nullable();
            $table->decimal('max_discount_amount', 8, 2)->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['code', 'is_active']);
            $table->index(['expires_at']);
            $table->index(['is_active', 'used_count']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
