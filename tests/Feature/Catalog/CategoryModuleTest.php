<?php

namespace Tests\Feature\Catalog;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Category\Models\Category;
use Modules\Category\Services\Admin\CategoryService;
use Modules\Product\Models\Product;
use Tests\TestCase;

class CategoryModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_path_is_built_from_tree(): void
    {
        $root = Category::query()->create([
            'name' => 'Root',
            'slug' => 'root',
        ]);

        $child = Category::query()->create([
            'name' => 'Child',
            'slug' => 'child',
            'parent_id' => $root->id,
        ]);

        $this->assertSame((string) $root->id, $root->fresh()->path);
        $this->assertSame("{$root->id}/{$child->id}", $child->fresh()->path);
    }

    public function test_category_delete_is_restricted_when_products_exist(): void
    {
        $category = Category::query()->create([
            'name' => 'Shoes',
            'slug' => 'shoes',
        ]);

        $product = Product::query()->create([
            'name' => 'Runner',
            'slug' => 'runner',
            'primary_category_id' => $category->id,
        ]);
        $product->categories()->attach($category->id);

        $result = app(CategoryService::class)->canBeDeleted($category);
        $this->assertFalse($result['allowed']);
        $this->assertNotNull($result['reason']);
    }
}
