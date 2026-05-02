<?php

namespace Modules\Category\Services\Api;

use Illuminate\Support\Collection;
use Modules\Category\Http\Resources\Api\CategoryResource;
use Modules\Category\Models\Category;

class CategoryTreeApiService
{
    /**
     * @param  Collection<int, Category>  $categories
     * @return array<int, array<string, mixed>>
     */
    public function nestedTree(Collection $categories, ?int $parentId = null, int $depth = 0, ?int $maxDepth = null): array
    {
        if ($maxDepth !== null && $depth > $maxDepth) {
            return [];
        }

        return $categories
            ->filter(fn (Category $c): bool => $c->parent_id === $parentId)
            ->sortBy(fn (Category $c): array => [$c->menu_order, $c->sort_order, $c->name])
            ->values()
            ->map(function (Category $category) use ($categories, $depth, $maxDepth): array {
                $array = CategoryResource::make($category)->resolve();
                $array['children'] = $this->nestedTree($categories, $category->id, $depth + 1, $maxDepth);

                return $array;
            })
            ->all();
    }
}
