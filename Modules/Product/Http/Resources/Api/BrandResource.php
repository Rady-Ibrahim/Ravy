<?php

namespace Modules\Product\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'logo' => $this->logo,
            'is_active' => $this->is_active,
            'products_count' => $this->whenCounted('products'),
            'url' => route('api.brands.show', $this->slug),
        ];
    }
}
