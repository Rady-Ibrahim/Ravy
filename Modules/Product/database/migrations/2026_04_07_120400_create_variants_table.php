<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->decimal('price', 12, 2)->index();
            $table->decimal('compare_at_price', 12, 2)->nullable();
            $table->integer('stock')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->string('attributes_hash', 64);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['product_id', 'attributes_hash']);
            $table->index(['price', 'stock']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};
