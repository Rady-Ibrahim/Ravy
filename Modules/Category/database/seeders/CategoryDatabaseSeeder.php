<?php

namespace Modules\Category\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Category\Models\Category;

class CategoryDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $fashion = Category::query()->updateOrCreate(
            ['slug' => 'fashion'],
            [
                'name' => 'Fashion',
                'description' => 'Main fashion catalog',
                'is_active' => true,
                'sort_order' => 1,
                'meta_title' => 'Fashion Products',
                'meta_description' => 'Browse fashion categories and styles.',
            ]
        );

        $men = Category::query()->updateOrCreate(
            ['slug' => 'men'],
            [
                'name' => 'Men',
                'parent_id' => $fashion->id,
                'description' => 'Menswear',
                'is_active' => true,
                'sort_order' => 10,
            ]
        );

        $women = Category::query()->updateOrCreate(
            ['slug' => 'women'],
            [
                'name' => 'Women',
                'parent_id' => $fashion->id,
                'description' => 'Womenswear',
                'is_active' => true,
                'sort_order' => 20,
            ]
        );

        Category::query()->updateOrCreate(
            ['slug' => 'bags'],
            [
                'name' => 'Bags',
                'parent_id' => $women->id,
                'description' => 'Bags and accessories',
                'is_active' => true,
                'sort_order' => 30,
            ]
        );

        Category::query()->updateOrCreate(
            ['slug' => 'coats'],
            [
                'name' => 'Coats',
                'parent_id' => $men->id,
                'description' => 'Outerwear and coats',
                'is_active' => true,
                'sort_order' => 40,
            ]
        );

        Category::query()->get()->each(function (Category $category): void {
            if (! $category->slug) {
                $category->update(['slug' => Str::slug($category->name)]);
            }
        });
    }
}
