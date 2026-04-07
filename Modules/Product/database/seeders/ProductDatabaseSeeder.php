<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Category\Models\Category;
use Modules\Product\Models\Brand;
use Modules\Product\Models\CategoryAttribute;
use Modules\Product\Models\CategoryAttributeValue;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductImage;
use Modules\Product\Services\Admin\VariantService;

class ProductDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $fashion = Category::query()->where('slug', 'fashion')->first();
        $men = Category::query()->where('slug', 'men')->first();
        $women = Category::query()->where('slug', 'women')->first();

        if (! $fashion || ! $men || ! $women) {
            $this->command?->warn('Categories are missing. Run CategoryDatabaseSeeder first.');

            return;
        }

        $brandA = Brand::query()->updateOrCreate(
            ['slug' => 'ravy-studio'],
            ['name' => 'Ravy Studio', 'is_active' => true]
        );
        $brandB = Brand::query()->updateOrCreate(
            ['slug' => 'urban-thread'],
            ['name' => 'Urban Thread', 'is_active' => true]
        );

        $colorAttr = CategoryAttribute::query()->updateOrCreate(
            ['category_id' => $fashion->id, 'code' => 'color'],
            ['name' => 'Color', 'type' => 'select', 'is_filterable' => true, 'sort_order' => 1]
        );
        $sizeAttr = CategoryAttribute::query()->updateOrCreate(
            ['category_id' => $fashion->id, 'code' => 'size'],
            ['name' => 'Size', 'type' => 'select', 'is_filterable' => true, 'sort_order' => 2]
        );

        $black = $this->upsertValue($colorAttr->id, 'Black');
        $beige = $this->upsertValue($colorAttr->id, 'Beige');
        $white = $this->upsertValue($colorAttr->id, 'White');
        $small = $this->upsertValue($sizeAttr->id, 'S');
        $medium = $this->upsertValue($sizeAttr->id, 'M');
        $large = $this->upsertValue($sizeAttr->id, 'L');

        $coat = Product::query()->updateOrCreate(
            ['slug' => 'wool-coat-men'],
            [
                'name' => 'Wool Coat Men',
                'primary_category_id' => $men->id,
                'brand_id' => $brandA->id,
                'description' => 'Premium wool coat for winter.',
                'short_description' => 'Classic winter coat',
                'is_active' => true,
                'is_featured' => true,
                'is_new' => true,
                'sort_order' => 10,
            ]
        );
        $coat->categories()->syncWithoutDetaching([$fashion->id, $men->id]);
        $coat->productAttributes()->updateOrCreate(
            ['attribute_key' => 'material'],
            ['attribute_value' => 'Wool blend']
        );

        $bag = Product::query()->updateOrCreate(
            ['slug' => 'daily-tote-bag'],
            [
                'name' => 'Daily Tote Bag',
                'primary_category_id' => $women->id,
                'brand_id' => $brandB->id,
                'description' => 'Spacious tote for everyday use.',
                'short_description' => 'Everyday tote',
                'is_active' => true,
                'is_featured' => false,
                'is_new' => true,
                'sort_order' => 20,
            ]
        );
        $bag->categories()->syncWithoutDetaching([$fashion->id, $women->id]);
        $bag->productAttributes()->updateOrCreate(
            ['attribute_key' => 'material'],
            ['attribute_value' => 'Canvas']
        );

        $variantService = app(VariantService::class);

        $this->upsertVariant($variantService, $coat->id, 'COAT-BLK-M', 2200, 2490, 15, [$black, $medium]);
        $this->upsertVariant($variantService, $coat->id, 'COAT-BEI-L', 2250, 2490, 8, [$beige, $large]);
        $this->upsertVariant($variantService, $bag->id, 'BAG-WHT-S', 950, 1090, 20, [$white, $small]);
        $this->upsertVariant($variantService, $bag->id, 'BAG-BLK-M', 990, 1090, 12, [$black, $medium]);

        ProductImage::query()->firstOrCreate(
            ['product_id' => $coat->id, 'is_primary' => true],
            ['path' => 'catalog/products/demo-coat.jpg', 'disk' => 'public', 'type' => 'image', 'alt' => 'Wool Coat']
        );
        ProductImage::query()->firstOrCreate(
            ['product_id' => $bag->id, 'is_primary' => true],
            ['path' => 'catalog/products/demo-bag.jpg', 'disk' => 'public', 'type' => 'image', 'alt' => 'Daily Tote']
        );
    }

    private function upsertValue(int $attributeId, string $value): int
    {
        $slug = strtolower(str_replace(' ', '-', $value));
        $record = CategoryAttributeValue::query()->updateOrCreate(
            ['attribute_id' => $attributeId, 'slug' => $slug],
            ['value' => $value]
        );

        return (int) $record->id;
    }

    /**
     * @param  array<int,int>  $attributeValueIds
     */
    private function upsertVariant(
        VariantService $variantService,
        int $productId,
        string $sku,
        float $price,
        float $compareAtPrice,
        int $stock,
        array $attributeValueIds
    ): void {
        $hash = $variantService->makeAttributesHash($attributeValueIds);
        $variant = \Modules\Product\Models\Variant::query()
            ->where('product_id', $productId)
            ->where('attributes_hash', $hash)
            ->first();

        if ($variant) {
            $variantService->update($variant, [
                'sku' => $sku,
                'price' => $price,
                'compare_at_price' => $compareAtPrice,
                'stock' => $stock,
                'is_active' => true,
            ], $attributeValueIds);

            return;
        }

        $variantService->create([
            'product_id' => $productId,
            'sku' => $sku,
            'price' => $price,
            'compare_at_price' => $compareAtPrice,
            'stock' => $stock,
            'is_active' => true,
        ], $attributeValueIds);
    }
}
