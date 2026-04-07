<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('primary_category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_new')->default(false)->index();
            $table->timestamp('featured_until')->nullable()->index();
            $table->integer('sort_order')->default(0)->index();
            $table->unsignedBigInteger('total_sales')->default(0)->index();
            $table->unsignedBigInteger('views_count')->default(0)->index();
            $table->decimal('score', 12, 4)->default(0)->index();
            $table->decimal('min_price', 12, 2)->nullable()->index();
            $table->decimal('max_price', 12, 2)->nullable()->index();
            $table->json('attributes_summary')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
