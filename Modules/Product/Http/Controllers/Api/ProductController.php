<?php

namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Modules\Product\Http\Requests\Api\IndexProductRequest;
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

        return response()->json([
            'data' => ProductResource::make($product)->resolve(),
        ]);
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
