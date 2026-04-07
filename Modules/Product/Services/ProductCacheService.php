<?php

namespace Modules\Product\Services;

use Illuminate\Support\Facades\DB;
use Modules\Product\Models\Product;
use Modules\Product\Models\Variant;

class ProductCacheService
{
    public function refreshPriceRange(int $productId): void
    {
        $range = Variant::query()
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        Product::query()
            ->whereKey($productId)
            ->update([
                'min_price' => $range?->min_price,
                'max_price' => $range?->max_price,
            ]);
    }

    public function refreshAttributesSummary(int $productId): void
    {
        $rows = DB::table('variant_attributes')
            ->join('variants', 'variants.id', '=', 'variant_attributes.variant_id')
            ->join('category_attribute_values', 'category_attribute_values.id', '=', 'variant_attributes.attribute_value_id')
            ->join('category_attributes', 'category_attributes.id', '=', 'category_attribute_values.attribute_id')
            ->where('variants.product_id', $productId)
            ->select(
                'category_attributes.code as attribute_code',
                'category_attributes.name as attribute_name',
                'category_attribute_values.value as value'
            )
            ->get();

        $summary = [];
        foreach ($rows as $row) {
            $summary[$row->attribute_code] ??= [
                'name' => $row->attribute_name,
                'values' => [],
            ];

            if (! in_array($row->value, $summary[$row->attribute_code]['values'], true)) {
                $summary[$row->attribute_code]['values'][] = $row->value;
            }
        }

        Product::query()->whereKey($productId)->update([
            'attributes_summary' => $summary,
        ]);
    }
}
