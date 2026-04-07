<?php

namespace Modules\Category\Services;

use Modules\Category\Models\Category;

class CategoryTreeService
{
    public function rebuildPath(Category $category): void
    {
        $path = $category->parent?->path
            ? "{$category->parent->path}/{$category->id}"
            : (string) $category->id;

        if ($category->path !== $path) {
            $category->updateQuietly(['path' => $path]);
        }

        $category->children()->get()->each(function (Category $child): void {
            $this->rebuildPath($child);
        });
    }
}
