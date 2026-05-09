<?php

namespace Modules\Category\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Category\Models\Category;

class CategoryDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Main categories
        $women = Category::query()->updateOrCreate(
            ['slug' => 'women'],
            [
                'name' => 'Women',
                'description' => 'Womenswear',
                'is_active' => true,
                'sort_order' => 1,
                'meta_title' => 'Women Fashion',
                'meta_description' => 'Browse women fashion collection.',
            ]
        );

        $men = Category::query()->updateOrCreate(
            ['slug' => 'men'],
            [
                'name' => 'Men',
                'description' => 'Menswear',
                'is_active' => true,
                'sort_order' => 2,
                'meta_title' => 'Men Fashion',
                'meta_description' => 'Browse men fashion collection.',
            ]
        );

        $kids = Category::query()->updateOrCreate(
            ['slug' => 'kids'],
            [
                'name' => 'Kids',
                'description' => 'Kids fashion',
                'is_active' => true,
                'sort_order' => 3,
                'meta_title' => 'Kids Fashion',
                'meta_description' => 'Browse kids fashion collection.',
            ]
        );

        $bags = Category::query()->updateOrCreate(
            ['slug' => 'bags'],
            [
                'name' => 'Bags',
                'description' => 'Bags and handbags',
                'is_active' => true,
                'sort_order' => 4,
                'meta_title' => 'Bags',
                'meta_description' => 'Browse our bags collection.',
            ]
        );

        $shoes = Category::query()->updateOrCreate(
            ['slug' => 'shoes'],
            [
                'name' => 'Shoes',
                'description' => 'Footwear collection',
                'is_active' => true,
                'sort_order' => 5,
                'meta_title' => 'Shoes',
                'meta_description' => 'Browse our shoes collection.',
            ]
        );

        $bestSeller = Category::query()->updateOrCreate(
            ['slug' => 'best-seller'],
            [
                'name' => 'Best Seller',
                'description' => 'Best selling products',
                'is_active' => true,
                'sort_order' => 6,
                'meta_title' => 'Best Seller',
                'meta_description' => 'Browse our best selling products.',
            ]
        );

        $newArrival = Category::query()->updateOrCreate(
            ['slug' => 'new-arrival'],
            [
                'name' => 'New Arrival',
                'description' => 'New arrival products',
                'is_active' => true,
                'sort_order' => 7,
                'meta_title' => 'New Arrival',
                'meta_description' => 'Browse our new arrival products.',
            ]
        );

        // Brands category
        $brands = Category::query()->updateOrCreate(
            ['slug' => 'brands'],
            [
                'name' => 'Brands',
                'description' => 'Shop by brands',
                'is_active' => true,
                'sort_order' => 8,
                'meta_title' => 'Shop By Brands',
                'meta_description' => 'Shop by your favorite brands.',
            ]
        );

        // Brand subcategories
        $brandNames = ['Hermes', 'Cuccoo', 'Prada', 'Ysl', 'Dior', 'Versace'];
        $sortOrder = 1;

        foreach ($brandNames as $brandName) {
            Category::query()->updateOrCreate(
                ['slug' => Str::slug($brandName)],
                [
                    'name' => $brandName,
                    'parent_id' => $brands->id,
                    'description' => ucfirst($brandName) . ' products',
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ]
            );
        }

        Category::query()->get()->each(function (Category $category): void {
            if (! $category->slug) {
                $category->update(['slug' => Str::slug($category->name)]);
            }
        });
    }
}
