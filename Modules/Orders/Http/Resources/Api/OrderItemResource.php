<?php

namespace Modules\Orders\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $variant = $this->whenLoaded('variant') ?? $this->variant;
        $product = $this->whenLoaded('product') ?? $this->product;

        $imagePath = null;
        if ($product?->relationLoaded('images') || $product?->images) {
            $productImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
            $imagePath = $productImage?->path;
        }

        if (! $imagePath && $variant?->image) {
            $imagePath = $variant->image;
        }

        $sizes = [];
        $colors = [];
        if ($variant?->relationLoaded('attributeValues') || $variant?->attributeValues) {
            foreach ($variant->attributeValues as $value) {
                $attributeCode = $value->attribute?->code;
                $extra = is_array($value->extra) ? $value->extra : (is_string($value->extra) ? json_decode($value->extra, true) : []);

                $item = [
                    'id' => $value->id,
                    'name' => $value->value,
                    'code' => $extra['code'] ?? null,
                    'code_from' => $extra['code_from'] ?? null,
                    'code_to' => $extra['code_to'] ?? null,
                    'hex' => $extra['hex'] ?? null,
                ];

                if ($attributeCode === 'size') {
                    $sizes[] = $item;
                }

                if ($attributeCode === 'color') {
                    $colors[] = $item;
                }
            }
        }

        $categoryName = null;
        if ($product?->relationLoaded('primaryCategory') || $product?->primaryCategory) {
            $categoryName = $product->primaryCategory?->name;
        }

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'variant_id' => $this->variant_id,
            'name' => $this->name_snapshot,
            'sku' => $this->sku_snapshot,
            'qty' => (int) $this->qty,
            'unit_price' => (float) $this->unit_price,
            'line_total' => (float) $this->line_total,
            'product_image' => $imagePath ? url('storage/' . ltrim($imagePath, '/')) : null,
            'product_category' => $categoryName,
            'sizes' => $sizes,
            'colors' => $colors,
        ];
    }
}
