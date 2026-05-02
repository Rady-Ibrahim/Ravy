<?php

namespace Modules\Product\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $primaryImage = $this->whenLoaded('images', function () {
            return $this->images
                ->sortBy([
                    ['is_primary', 'desc'],
                    ['sort_order', 'asc'],
                    ['id', 'asc'],
                ])
                ->first();
        });

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'is_new' => (bool) $this->is_new,
            'is_featured' => (bool) $this->is_featured,
            'min_price' => $this->min_price !== null ? (float) $this->min_price : null,
            'max_price' => $this->max_price !== null ? (float) $this->max_price : null,
            'brand' => $this->whenLoaded('brand', function (): ?array {
                if (! $this->brand) {
                    return null;
                }

                return [
                    'id' => $this->brand->id,
                    'name' => $this->brand->name,
                    'slug' => $this->brand->slug,
                ];
            }),
            'image' => $primaryImage ? [
                'id' => $primaryImage->id,
                'path' => $primaryImage->path,
                'url' => $this->mediaUrl($primaryImage->disk, $primaryImage->path),
                'alt' => $primaryImage->alt,
                'is_primary' => (bool) $primaryImage->is_primary,
            ] : null,
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
