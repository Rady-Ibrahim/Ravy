<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\Category\Models\Category;
use Modules\Product\Models\Brand;
use Modules\Product\Models\CategoryAttribute;
use Modules\Product\Models\CategoryAttributeValue;
use Modules\Product\Models\Product;
use Modules\Product\Models\Variant;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_index_supports_new_filters_and_search(): void
    {
        $category = Category::query()->create(['name' => 'Shoes', 'slug' => 'shoes']);
        $brand = Brand::query()->create(['name' => 'Prada', 'slug' => 'prada', 'is_active' => true]);

        $matching = Product::query()->create([
            'name' => 'Prada Summer Sandal',
            'slug' => 'prada-summer-sandal',
            'primary_category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
            'is_new' => true,
            'is_featured' => true,
            'min_price' => 100,
            'max_price' => 150,
        ]);
        $matching->categories()->attach($category->id);

        $other = Product::query()->create([
            'name' => 'Old Sneaker',
            'slug' => 'old-sneaker',
            'primary_category_id' => $category->id,
            'is_active' => true,
            'is_new' => false,
            'is_featured' => false,
            'min_price' => 80,
            'max_price' => 90,
        ]);
        $other->categories()->attach($category->id);

        $response = $this->getJson('/api/v1/products?is_new=1&is_featured=1&search=Prada');

        $response->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);

        $slugs = collect($response->json('data'))->pluck('slug')->all();
        $this->assertSame(['prada-summer-sandal'], $slugs);
    }

    public function test_products_index_supports_variant_attribute_filters(): void
    {
        $category = Category::query()->create(['name' => 'Clothes', 'slug' => 'clothes']);
        $product = Product::query()->create([
            'name' => 'Blue Tee',
            'slug' => 'blue-tee',
            'primary_category_id' => $category->id,
            'is_active' => true,
        ]);
        $product->categories()->attach($category->id);

        $variant = Variant::query()->create([
            'product_id' => $product->id,
            'sku' => 'BLUE-TEE-M',
            'price' => 40,
            'stock' => 5,
            'is_active' => true,
            'attributes_hash' => sha1('blue-m'),
        ]);

        $colorAttribute = CategoryAttribute::query()->create([
            'category_id' => $category->id,
            'name' => 'Color',
            'code' => 'color',
            'type' => 'select',
            'sort_order' => 0,
            'is_filterable' => true,
        ]);
        $sizeAttribute = CategoryAttribute::query()->create([
            'category_id' => $category->id,
            'name' => 'Size',
            'code' => 'size',
            'type' => 'select',
            'sort_order' => 1,
            'is_filterable' => true,
        ]);

        $blue = CategoryAttributeValue::query()->create([
            'attribute_id' => $colorAttribute->id,
            'value' => 'Blue',
            'slug' => 'blue',
        ]);
        $medium = CategoryAttributeValue::query()->create([
            'attribute_id' => $sizeAttribute->id,
            'value' => 'M',
            'slug' => 'm',
        ]);

        $variant->attributeValues()->attach([$blue->id, $medium->id]);

        $response = $this->getJson('/api/v1/products?color=blue&size=m');

        $response->assertOk();
        $slugs = collect($response->json('data'))->pluck('slug')->all();
        $this->assertSame(['blue-tee'], $slugs);
    }

    public function test_product_show_returns_related_products(): void
    {
        $category = Category::query()->create(['name' => 'Bags', 'slug' => 'bags']);
        $brand = Brand::query()->create(['name' => 'YSL', 'slug' => 'ysl', 'is_active' => true]);

        $main = Product::query()->create([
            'name' => 'Main Bag',
            'slug' => 'main-bag',
            'primary_category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
            'score' => 9,
        ]);
        $main->categories()->attach($category->id);

        $related = Product::query()->create([
            'name' => 'Related Bag',
            'slug' => 'related-bag',
            'primary_category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
            'score' => 8,
        ]);
        $related->categories()->attach($category->id);

        $response = $this->getJson('/api/v1/products/main-bag');

        $response->assertOk()
            ->assertJsonStructure([
                'data',
                'related_products',
            ]);

        $relatedSlugs = collect($response->json('related_products'))->pluck('slug')->all();
        $this->assertContains('related-bag', $relatedSlugs);
        $this->assertNotContains('main-bag', $relatedSlugs);
    }

    public function test_product_view_endpoint_increments_views_count(): void
    {
        $category = Category::query()->create(['name' => 'Shoes', 'slug' => 'shoes']);
        $product = Product::query()->create([
            'name' => 'Heel',
            'slug' => 'heel',
            'primary_category_id' => $category->id,
            'is_active' => true,
            'views_count' => 0,
        ]);
        $product->categories()->attach($category->id);

        $response = $this->postJson('/api/v1/products/heel/view');

        $response->assertOk()
            ->assertJsonStructure(['message', 'views_count']);

        $this->assertSame(1, $response->json('views_count'));
        $this->assertSame(1, (int) $product->fresh()->views_count);
    }

    public function test_wishlist_toggle_endpoint_attaches_and_detaches_product(): void
    {
        $category = Category::query()->create(['name' => 'Accessories', 'slug' => 'accessories']);
        $product = Product::query()->create([
            'name' => 'Bracelet',
            'slug' => 'bracelet',
            'primary_category_id' => $category->id,
            'is_active' => true,
        ]);
        $product->categories()->attach($category->id);

        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $attachResponse = $this->withToken($token)->postJson('/api/v1/products/bracelet/wishlist');
        $attachResponse->assertOk()->assertJsonPath('attached', true);
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $detachResponse = $this->withToken($token)->postJson('/api/v1/products/bracelet/wishlist');
        $detachResponse->assertOk()->assertJsonPath('attached', false);
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_wishlist_endpoint_returns_product_card_resource_collection(): void
    {
        $category = Category::query()->create(['name' => 'Bags', 'slug' => 'bags']);
        $brand = Brand::query()->create(['name' => 'Gucci', 'slug' => 'gucci', 'is_active' => true]);

        $product = Product::query()->create([
            'name' => 'Leather Bag',
            'slug' => 'leather-bag',
            'primary_category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
            'is_new' => true,
            'min_price' => 1200,
            'max_price' => 1500,
        ]);
        $product->categories()->attach($category->id);

        $user = User::factory()->create();
        $user->wishlistProducts()->attach($product->id);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/v1/wishlist');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'is_new', 'is_featured', 'min_price', 'max_price', 'brand', 'image'],
                ],
            ])
            ->assertJsonPath('data.0.slug', 'leather-bag');
    }
}
