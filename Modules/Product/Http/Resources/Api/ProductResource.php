<?php

namespace Modules\Product\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Category\Http\Resources\Api\CategoryResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'is_active' => (bool) $this->is_active,
            'is_featured' => (bool) $this->is_featured,
            'is_new' => (bool) $this->is_new,
            'featured_until' => optional($this->featured_until)?->toISOString(),
            'sort_order' => (int) ($this->sort_order ?? 0),
            'total_sales' => (int) ($this->total_sales ?? 0),
            'views_count' => (int) ($this->views_count ?? 0),
            'score' => $this->score !== null ? (float) $this->score : null,
            'min_price' => $this->min_price !== null ? (float) $this->min_price : null,
            'max_price' => $this->max_price !== null ? (float) $this->max_price : null,
            'attributes_summary' => $this->attributes_summary ?? [],
            'brand' => $this->whenLoaded('brand', function (): ?array {
                if (! $this->brand) {
                    return null;
                }

                return [
                    'id' => $this->brand->id,
                    'name' => $this->brand->name,
                    'slug' => $this->brand->slug,
                    'logo' => $this->brand->logo,
                    'is_active' => (bool) $this->brand->is_active,
                ];
            }),
            'primary_category' => $this->whenLoaded('primaryCategory', fn () => $this->primaryCategory ? CategoryResource::make($this->primaryCategory)->resolve() : null),
            'categories' => $this->whenLoaded('categories', fn () => CategoryResource::collection($this->categories)->resolve()),
            'images' => $this->whenLoaded('images', function (): array {
                return $this->images->map(function ($image): array {
                    return [
                        'id' => $image->id,
                        'variant_id' => $image->variant_id,
                        'path' => $image->path,
                        'url' => $this->mediaUrl($image->disk, $image->path),
                        'disk' => $image->disk,
                        'type' => $image->type,
                        'alt' => $image->alt,
                        'sort_order' => (int) ($image->sort_order ?? 0),
                        'is_primary' => (bool) $image->is_primary,
                    ];
                })->values()->all();
            }),
            'variants' => $this->whenLoaded('variants', function (): array {
                return $this->variants->map(function ($variant): array {
                    return [
                        'id' => $variant->id,
                        'sku' => $variant->sku,
                        'price' => (float) $variant->price,
                        'compare_at_price' => $variant->compare_at_price !== null ? (float) $variant->compare_at_price : null,
                        'stock' => (int) $variant->stock,
                        'is_active' => (bool) $variant->is_active,
                        'attributes' => $variant->relationLoaded('attributeValues')
                            ? $variant->attributeValues->map(function ($value): array {
                                return [
                                    'id' => $value->id,
                                    'attribute' => $value->relationLoaded('attribute') && $value->attribute
                                        ? [
                                            'id' => $value->attribute->id,
                                            'name' => $value->attribute->name,
                                            'code' => $value->attribute->code,
                                            'type' => $value->attribute->type,
                                        ]
                                        : null,
                                    'value' => $value->value,
                                    'slug' => $value->slug,
                                    'extra' => $value->extra,
                                ];
                            })->values()->all()
                            : [],
                    ];
                })->values()->all();
            }),
        ];
    }

    private function mediaUrl(?string $disk, ?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if ($disk === 'public') {
            return asset('storage/'.$path);
        }

        return null;
    }
}

