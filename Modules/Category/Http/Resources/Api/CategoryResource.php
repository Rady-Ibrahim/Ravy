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
            'image_url' => $this->mediaUrl($this->image),
            'banner_url' => $this->mediaUrl($this->banner),
            'icon_url' => $this->mediaUrl($this->icon),
            'is_active' => (bool) $this->is_active,
            'show_in_sidebar' => (bool) $this->show_in_sidebar,
            'sort_order' => (int) ($this->sort_order ?? 0),
            'menu_order' => (int) ($this->menu_order ?? 0),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'products_count' => (int) ($this->products_count ?? 0),
            'children_count' => $this->whenCounted('children'),
            'children' => $this->when(
                $this->relationLoaded('children'),
                fn(): array => CategoryResource::collection($this->children)->resolve()
            ),
        ];
    }

    private function mediaUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return asset('public/storage/' . $path);
    }
}
