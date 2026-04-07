<?php

namespace Tests\Feature\Catalog;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;
use Modules\Product\Models\Variant;
use Modules\Product\Services\Api\ProductRankingService;
use Tests\TestCase;

class ProductModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_index_supports_basic_filters_and_pagination_contract(): void
    {
        $category = Category::query()->create(['name' => 'Clothes', 'slug' => 'clothes']);
        $product = Product::query()->create([
            'name' => 'Shirt',
            'slug' => 'shirt',
            'is_active' => true,
            'primary_category_id' => $category->id,
            'min_price' => 10,
            'max_price' => 20,
        ]);
        $product->categories()->attach($category->id);

        $response = $this->getJson('/api/v1/products?category=clothes&price_min=5&price_max=30&per_page=5');

        $response->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_variant_combination_hash_must_be_unique_per_product(): void
    {
        $product = Product::query()->create(['name' => 'Hat', 'slug' => 'hat']);

        Variant::query()->create([
            'product_id' => $product->id,
            'sku' => 'HAT-RED-M',
            'price' => 40,
            'stock' => 5,
            'is_active' => true,
            'attributes_hash' => sha1('1-5'),
        ]);

        $this->expectException(QueryException::class);

        Variant::query()->create([
            'product_id' => $product->id,
            'sku' => 'HAT-RED-M-2',
            'price' => 45,
            'stock' => 2,
            'is_active' => true,
            'attributes_hash' => sha1('1-5'),
        ]);
    }

    public function test_ranking_service_updates_score_cache(): void
    {
        $product = Product::query()->create([
            'name' => 'Coat',
            'slug' => 'coat',
            'total_sales' => 100,
            'views_count' => 80,
            'is_featured' => true,
            'is_new' => true,
            'sort_order' => 10,
        ]);

        app(ProductRankingService::class)->recalculateScores();

        $this->assertGreaterThan(0, (float) $product->fresh()->score);
    }
}
