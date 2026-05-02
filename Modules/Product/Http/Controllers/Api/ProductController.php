<?php

namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Modules\Product\Http\Requests\Api\IndexProductRequest;
use Modules\Product\Http\Resources\Api\ProductCardResource;
use Modules\Product\Http\Resources\Api\ProductResource;
use Modules\Product\Models\Product;
use Modules\Product\Services\Api\ProductFilterService;

class ProductController extends Controller
{
    public function index(IndexProductRequest $request, ProductFilterService $service): JsonResponse
    {
        $products = $service->paginate($request->validated());

        return response()->json($this->paginationPayload($products));
    }

    public function show(string $slug): JsonResponse
    {
        $product = Product::query()
            ->with(['brand', 'primaryCategory', 'categories', 'variants.attributeValues.attribute', 'images'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedProducts = Product::query()
            ->with(['brand', 'images'])
            ->where('is_active', true)
            ->whereKeyNot($product->id)
            ->when($product->primary_category_id, function ($q) use ($product): void {
                $q->where(function ($nested) use ($product): void {
                    $nested->where('primary_category_id', $product->primary_category_id)
                        ->orWhereHas('categories', function ($categoryQuery) use ($product): void {
                            $categoryQuery->where('categories.id', $product->primary_category_id);
                        });
                });
            }, function ($q) use ($product): void {
                if ($product->brand_id) {
                    $q->where('brand_id', $product->brand_id);
                }
            })
            ->orderByDesc('score')
            ->latest('id')
            ->limit(8)
            ->get();

        return response()->json([
            'data' => ProductResource::make($product)->resolve(),
            'related_products' => ProductCardResource::collection($relatedProducts)->resolve(),
        ]);
    }

    public function incrementViews(string $slug): JsonResponse
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $product->increment('views_count');

        return response()->json([
            'message' => __('Product views updated successfully.'),
            'views_count' => (int) $product->fresh()->views_count,
        ]);
    }

    private function paginationPayload(LengthAwarePaginator $paginator): array
    {
        return [
            'data' => ProductCardResource::collection(collect($paginator->items()))->resolve(),
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
