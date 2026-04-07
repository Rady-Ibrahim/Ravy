<?php

namespace Modules\Category\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Modules\Category\Http\Requests\Api\IndexCategoryRequest;
use Modules\Category\Http\Resources\Api\CategoryResource;
use Modules\Category\Models\Category;
use Modules\Category\Services\Api\CategoryFilterService;

class CategoryController extends Controller
{
    public function index(IndexCategoryRequest $request): JsonResponse
    {
        $query = Category::query()->orderBy('sort_order')->orderBy('name');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->validated('search').'%');
        }

        if ($request->has('is_active')) {
            $query->where('is_active', (bool) $request->boolean('is_active'));
        } else {
            $query->where('is_active', true);
        }

        $categories = $query->paginate((int) $request->integer('per_page', 12));

        return response()->json($this->paginationPayload($categories));
    }

    public function show(string $slug, CategoryFilterService $service): JsonResponse
    {
        $category = Category::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();
        $data = $service->dataForCategory($category);

        return response()->json([
            'category' => CategoryResource::make($category)->resolve(),
            'filters' => $data['filters'],
            'brands' => $data['brands'],
            'sorting_options' => $data['sorting_options'],
            'products' => $data['products'],
        ]);
    }

    private function paginationPayload(LengthAwarePaginator $paginator): array
    {
        return [
            'data' => CategoryResource::collection(collect($paginator->items()))->resolve(),
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
