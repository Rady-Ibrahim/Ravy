<?php

namespace Modules\Category\Observers;

use Modules\Category\Models\Category;
use Modules\Category\Services\CategoryTreeService;

class CategoryObserver
{
    public function created(Category $category): void
    {
        app(CategoryTreeService::class)->rebuildPath($category->fresh(['parent', 'children']));
    }

    public function updated(Category $category): void
    {
        if ($category->wasChanged('parent_id')) {
            app(CategoryTreeService::class)->rebuildPath($category->fresh(['parent', 'children']));
        }
    }
}
