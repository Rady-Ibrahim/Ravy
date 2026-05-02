<?php

namespace Modules\Category\Services\Api;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Category\Models\Category;
use Modules\Product\Http\Resources\Api\ProductResource;
use Modules\Product\Models\Brand;
use Modules\Product\Models\Product;

class CategoryFilterService
{
    /**
     * @param  array<string, mixed>  $filters  validated ShowCategoryRequest
     * @return array{filters: array<int, array<string, mixed>>, brands: array<int, array<string, mixed>>, sorting_options: array<int, array<string, string>>, products: array<string, mixed>}
     */
    public function dataForCategory(Category $category, array $filters): array
    {
        $includeDescendants = (bool) ($filters['include_descendants'] ?? true);
        $categoryIds = $this->categoryScopeIds($category, $includeDescendants);

        $facetProductIds = $this->baseProductIdsQuery($categoryIds);
        $brands = $this->buildBrandFacets($facetProductIds);
        $filtersPayload = $this->buildAttributeFacets($facetProductIds);

        $productsQuery = Product::query()
            ->with(['brand', 'images', 'variants'])
            ->where('is_active', true)
            ->whereHas('categories', function (Builder $q) use ($categoryIds): void {
                $q->whereIn('categories.id', $categoryIds);
            });

        if (! empty($filters['brand'])) {
            $productsQuery->whereHas('brand', function (Builder $q) use ($filters): void {
                $q->where('slug', $filters['brand']);
            });
        }

        $attributeMap = [
            'color' => 'color',
            'size' => 'size',
            'material' => 'material',
        ];
        foreach ($attributeMap as $param => $code) {
            if (! empty($filters[$param])) {
                $this->applyVariantAttributeFilter($productsQuery, $code, (string) $filters[$param]);
            }
        }

        if (isset($filters['price_min'])) {
            $productsQuery->where('min_price', '>=', (float) $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $productsQuery->where('max_price', '<=', (float) $filters['price_max']);
        }

        $sort = $filters['sort'] ?? 'latest';
        match ($sort) {
            'price_asc' => $productsQuery->orderBy('min_price'),
            'price_desc' => $productsQuery->orderByDesc('max_price'),
            'best_seller' => $productsQuery->orderByDesc('total_sales'),
            'trending' => $productsQuery->orderByDesc('score'),
            default => $productsQuery->latest('id'),
        };

        $perPage = (int) ($filters['per_page'] ?? 12);
        $paginator = $productsQuery->paginate($perPage)->withQueryString();

        return [
            'filters' => $filtersPayload,
            'brands' => $brands,
            'sorting_options' => [
                ['key' => 'latest', 'label' => 'Latest'],
                ['key' => 'price_asc', 'label' => 'Price: Low to High'],
                ['key' => 'price_desc', 'label' => 'Price: High to Low'],
                ['key' => 'best_seller', 'label' => 'Best Seller'],
                ['key' => 'trending', 'label' => 'Trending'],
            ],
            'products' => $this->paginationPayload($paginator),
        ];
    }

    /**
     * @return array<int, int>
     */
    private function categoryScopeIds(Category $category, bool $includeDescendants): array
    {
        if (! $includeDescendants) {
            return [(int) $category->id];
        }

        $path = $category->path ?: (string) $category->id;

        return Category::query()
            ->where('is_active', true)
            ->where(function (Builder $q) use ($category, $path): void {
                $q->where('categories.id', $category->id)
                    ->orWhere('categories.path', 'like', $path.'/%');
            })
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function baseProductIdsQuery(array $categoryIds): QueryBuilder
    {
        return Product::query()
            ->select('products.id')
            ->where('products.is_active', true)
            ->whereHas('categories', function (Builder $q) use ($categoryIds): void {
                $q->whereIn('categories.id', $categoryIds);
            })
            ->toBase();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildBrandFacets(QueryBuilder $productIdsSubquery): array
    {
        return Brand::query()
            ->select(['brands.id', 'brands.name', 'brands.slug', 'brands.logo'])
            ->join('products', 'products.brand_id', '=', 'brands.id')
            ->whereIn('products.id', $productIdsSubquery)
            ->where('brands.is_active', true)
            ->whereNull('brands.deleted_at')
            ->groupBy('brands.id', 'brands.name', 'brands.slug', 'brands.logo')
            ->selectRaw('COUNT(DISTINCT products.id) as products_count')
            ->orderBy('brands.name')
            ->get()
            ->map(fn ($row): array => [
                'id' => (int) $row->id,
                'name' => $row->name,
                'slug' => $row->slug,
                'logo' => $row->logo,
                'products_count' => (int) $row->products_count,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildAttributeFacets(QueryBuilder $productIdsSubquery): array
    {
        $rows = DB::table('category_attribute_values as cav')
            ->join('category_attributes as ca', 'ca.id', '=', 'cav.attribute_id')
            ->join('variant_attributes as va', 'va.attribute_value_id', '=', 'cav.id')
            ->join('variants as v', 'v.id', '=', 'va.variant_id')
            ->join('products as p', 'p.id', '=', 'v.product_id')
            ->where('ca.is_filterable', true)
            ->where('p.is_active', true)
            ->whereNull('p.deleted_at')
            ->where('v.is_active', true)
            ->whereNull('v.deleted_at')
            ->whereIn('p.id', $productIdsSubquery)
            ->select(
                'ca.id as attribute_id',
                'ca.name as attribute_name',
                'ca.code as attribute_code',
                'ca.sort_order as attribute_sort_order',
                'cav.id as value_id',
                'cav.value as value_label',
                'cav.slug as value_slug',
                'cav.extra as value_extra',
            )
            ->selectRaw('COUNT(DISTINCT p.id) as products_count')
            ->groupBy(
                'ca.id',
                'ca.name',
                'ca.code',
                'ca.sort_order',
                'cav.id',
                'cav.value',
                'cav.slug',
                'cav.extra',
            )
            ->orderBy('ca.sort_order')
            ->orderBy('cav.value')
            ->get();

        $grouped = [];
        foreach ($rows as $row) {
            $code = $row->attribute_code;
            if (! isset($grouped[$code])) {
                $grouped[$code] = [
                    'key' => $code,
                    'label' => $row->attribute_name,
                    'values' => [],
                ];
            }

            $extra = $row->value_extra;
            if (is_string($extra) && $extra !== '') {
                $decoded = json_decode($extra, true);
                $extra = json_last_error() === JSON_ERROR_NONE ? $decoded : $extra;
            }

            $grouped[$code]['values'][] = [
                'id' => (int) $row->value_id,
                'value' => $row->value_label,
                'slug' => $row->value_slug,
                'count' => (int) $row->products_count,
                'extra' => $extra,
            ];
        }

        return array_values($grouped);
    }

    private function applyVariantAttributeFilter(Builder $query, string $code, string $slug): void
    {
        $query->whereHas('variants', function (Builder $vq) use ($code, $slug): void {
            $vq->where('variants.is_active', true)
                ->whereHas('attributeValues', function (Builder $aq) use ($code, $slug): void {
                    $aq->where('category_attribute_values.slug', $slug)
                        ->whereHas('attribute', function (Builder $attr) use ($code): void {
                            $attr->where('code', $code);
                        });
                });
        });
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
