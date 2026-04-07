<?php

namespace Modules\Category\Services\Api;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Product\Http\Resources\Api\ProductResource;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;

class CategoryFilterService
{
    public function dataForCategory(Category $category): array
    {
        $products = Product::query()
            ->with(['brand', 'images', 'variants'])
            ->where('is_active', true)
            ->whereHas('categories', function ($query) use ($category): void {
                $query->where('categories.id', $category->id);
            });

        return [
            'filters' => [],
            'brands' => [],
            'sorting_options' => [
                ['key' => 'latest', 'label' => 'Latest'],
                ['key' => 'price_asc', 'label' => 'Price: Low to High'],
                ['key' => 'price_desc', 'label' => 'Price: High to Low'],
                ['key' => 'best_seller', 'label' => 'Best Seller'],
                ['key' => 'trending', 'label' => 'Trending'],
            ],
            'products' => $this->paginationPayload($products->paginate(12)),
        ];
    }

    private function paginationPayload(LengthAwarePaginator $paginator): array
    {
        return [
            'data' => ProductResource::collection(collect($paginator->items()))->resolve(),
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'path' => $paginator->path(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
        ];
    }
}
