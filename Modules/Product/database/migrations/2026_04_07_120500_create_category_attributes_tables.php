<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('category_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('type')->default('select');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_filterable')->default(true)->index();
            $table->timestamps();

            $table->unique(['category_id', 'code']);
        });

        Schema::create('category_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('category_attributes')->cascadeOnDelete();
            $table->string('value');
            $table->string('slug');
            $table->json('extra')->nullable();
            $table->timestamps();

            $table->unique(['attribute_id', 'slug']);
        });

        Schema::create('variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('variants')->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained('category_attribute_values')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['variant_id', 'attribute_value_id']);
            $table->index('attribute_value_id');
        });

        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('attribute_key');
            $table->string('attribute_value');
            $table->timestamps();

            $table->index(['product_id', 'attribute_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('variant_attributes');
        Schema::dropIfExists('category_attribute_values');
        Schema::dropIfExists('category_attributes');
    }
};
