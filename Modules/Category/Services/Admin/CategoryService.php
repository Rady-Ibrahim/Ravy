<?php

namespace Modules\Category\Services\Admin;

use Modules\Category\Models\Category;

class CategoryService
{
    public function canBeDeleted(Category $category): array
    {
        $productsCount = $category->products()->count();
        if ($productsCount > 0) {
            return [
                'allowed' => false,
                'reason' => __("Cannot delete this category because it still contains :count products.", ['count' => $productsCount]),
            ];
        }

        if ($category->children()->count() > 0) {
            return [
                'allowed' => false,
                'reason' => __('Cannot delete this category because it has child categories.'),
            ];
        }

        return ['allowed' => true, 'reason' => null];
    }
}
