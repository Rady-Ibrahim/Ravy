<?php

namespace Modules\Category\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            'path' => $this->path,
            'description' => $this->description,
            'image' => $this->image,
            'banner' => $this->banner,
            'icon' => $this->icon,
            'is_active' => (bool) $this->is_active,
            'sort_order' => (int) ($this->sort_order ?? 0),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'products_count' => (int) ($this->products_count ?? 0),
        ];
    }
}

