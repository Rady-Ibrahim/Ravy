<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Category\Models\Category;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_tree_returns_nested_children(): void
    {
        $root = Category::query()->create([
            'name' => 'Women',
            'slug' => 'women',
            'menu_order' => 0,
            'sort_order' => 0,
        ]);

        Category::query()->create([
            'name' => 'Dresses',
            'slug' => 'dresses',
            'parent_id' => $root->id,
            'menu_order' => 1,
            'sort_order' => 0,
        ]);

        $response = $this->getJson('/api/v1/categories?tree=1');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'slug', 'children'],
                ],
            ]);

        $women = collect($response->json('data'))->firstWhere('slug', 'women');
        $this->assertNotNull($women);
        $this->assertCount(1, $women['children']);
        $this->assertSame('dresses', $women['children'][0]['slug']);
    }

    public function test_category_breadcrumb_returns_root_to_current(): void
    {
        $root = Category::query()->create([
            'name' => 'Women',
            'slug' => 'women',
        ]);

        $child = Category::query()->create([
            'name' => 'Dresses',
            'slug' => 'dresses',
            'parent_id' => $root->id,
        ]);

        $response = $this->getJson('/api/v1/categories/'.$child->slug.'/breadcrumb');

        $response->assertOk();
        $slugs = collect($response->json('data'))->pluck('slug')->all();
        $this->assertSame(['women', 'dresses'], $slugs);
    }

    public function test_category_show_includes_filter_and_brand_payloads(): void
    {
        Category::query()->create([
            'name' => 'Shoes',
            'slug' => 'shoes',
        ]);

        $response = $this->getJson('/api/v1/categories/shoes');

        $response->assertOk()
            ->assertJsonStructure([
                'category' => ['slug', 'show_in_sidebar', 'menu_order'],
                'filters',
                'brands',
                'sorting_options',
                'products' => ['data', 'links', 'meta'],
            ]);

        $this->assertIsArray($response->json('filters'));
        $this->assertIsArray($response->json('brands'));
    }
}
