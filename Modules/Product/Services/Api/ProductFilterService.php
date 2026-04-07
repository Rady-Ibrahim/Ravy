<?php

namespace Modules\Product\Services\Api;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Product\Models\Product;

class ProductFilterService
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Product::query()
            ->with(['brand', 'images', 'variants'])
            ->where('is_active', true);

        if (! empty($filters['category'])) {
            $query->whereHas('categories', function ($q) use ($filters): void {
                $q->where('categories.slug', $filters['category']);
            });
        }

        if (! empty($filters['brand'])) {
            $query->whereHas('brand', function ($q) use ($filters): void {
                $q->where('slug', $filters['brand']);
            });
        }

        if (isset($filters['price_min'])) {
            $query->where('min_price', '>=', (float) $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('max_price', '<=', (float) $filters['price_max']);
        }

        $sort = $filters['sort'] ?? 'latest';
        match ($sort) {
            'price_asc' => $query->orderBy('min_price'),
            'price_desc' => $query->orderByDesc('max_price'),
            'best_seller' => $query->orderByDesc('total_sales'),
            'trending' => $query->orderByDesc('score'),
            default => $query->latest('id'),
        };

        $perPage = (int) ($filters['per_page'] ?? 12);

        return $query->paginate($perPage)->withQueryString();
    }
}
