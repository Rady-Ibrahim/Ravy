<?php

namespace Modules\Product\Services\Api;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\Product\Models\Product;

class ProductFilterService
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Product::query()
            ->with(['brand', 'images'])
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

        if (array_key_exists('is_new', $filters)) {
            $query->where('is_new', (bool) $filters['is_new']);
        }

        $featuredFlag = $filters['is_featured'] ?? $filters['featured'] ?? null;
        if ($featuredFlag !== null) {
            $query->where('is_featured', (bool) $featuredFlag);
        }

        $search = trim((string) ($filters['search'] ?? $filters['q'] ?? ''));
        if ($search !== '') {
            $query->where(function (Builder $q) use ($search): void {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('short_description', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $attributeMap = [
            'color' => 'color',
            'size' => 'size',
            'material' => 'material',
        ];
        foreach ($attributeMap as $param => $code) {
            if (! empty($filters[$param])) {
                $this->applyVariantAttributeFilter($query, $code, (string) $filters[$param]);
            }
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

    private function applyVariantAttributeFilter(Builder $query, string $code, string $slug): void
    {
        $query->whereHas('variants', function (Builder $variantQuery) use ($code, $slug): void {
            $variantQuery->where('variants.is_active', true)
                ->whereHas('attributeValues', function (Builder $attributeValueQuery) use ($code, $slug): void {
                    $attributeValueQuery->where('category_attribute_values.slug', $slug)
                        ->whereHas('attribute', function (Builder $attributeQuery) use ($code): void {
                            $attributeQuery->where('code', $code);
                        });
                });
        });
    }
}
