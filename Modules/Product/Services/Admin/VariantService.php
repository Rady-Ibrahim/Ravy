<?php

namespace Modules\Product\Services\Admin;

use Illuminate\Support\Arr;
use Modules\Product\Models\Variant;
use Modules\Product\Services\ProductCacheService;

class VariantService
{
    public function __construct(
        private readonly ProductCacheService $cacheService
    ) {}

    /**
     * @param  array<int, int|string>  $attributeValueIds
     */
    public function makeAttributesHash(array $attributeValueIds): string
    {
        $normalized = array_map(fn ($id) => (int) $id, $attributeValueIds);
        sort($normalized);

        return sha1(implode('-', $normalized));
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, int|string>  $attributeValueIds
     */
    public function create(array $data, array $attributeValueIds): Variant
    {
        $hash = $this->makeAttributesHash($attributeValueIds);
        $data['attributes_hash'] = $hash;

        /** @var Variant $variant */
        $variant = Variant::query()->create(Arr::only($data, [
            'product_id',
            'sku',
            'price',
            'compare_at_price',
            'stock',
            'is_active',
            'attributes_hash',
        ]));

        $variant->attributeValues()->sync($attributeValueIds);
        $this->cacheService->refreshPriceRange((int) $variant->product_id);
        $this->cacheService->refreshAttributesSummary((int) $variant->product_id);

        return $variant;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, int|string>  $attributeValueIds
     */
    public function update(Variant $variant, array $data, array $attributeValueIds): Variant
    {
        $hash = $this->makeAttributesHash($attributeValueIds);
        $data['attributes_hash'] = $hash;

        $variant->update(Arr::only($data, [
            'sku',
            'price',
            'compare_at_price',
            'stock',
            'is_active',
            'attributes_hash',
            'image',
        ]));

        $variant->attributeValues()->sync($attributeValueIds);
        $this->cacheService->refreshPriceRange((int) $variant->product_id);
        $this->cacheService->refreshAttributesSummary((int) $variant->product_id);

        return $variant->fresh('attributeValues.attribute');
    }
}
