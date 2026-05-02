<?php

namespace Modules\Category\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Modules\Category\Http\Requests\Api\IndexCategoryRequest;
use Modules\Category\Http\Requests\Api\ShowCategoryRequest;
use Modules\Category\Http\Resources\Api\CategoryResource;
use Modules\Category\Models\Category;
use Modules\Category\Services\Api\CategoryFilterService;
use Modules\Category\Services\Api\CategoryTreeApiService;

class CategoryController extends Controller
{
    public function index(IndexCategoryRequest $request, CategoryTreeApiService $treeService): JsonResponse
    {
        $query = Category::query()
            ->withCount('children')
            ->orderBy('menu_order')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->validated('search').'%');
        }

        if ($request->has('is_active')) {
            $query->where('is_active', (bool) $request->boolean('is_active'));
        } else {
            $query->where('is_active', true);
        }

        if ($request->boolean('sidebar')) {
            $query->where('show_in_sidebar', true);
        }

        if ($request->filled('parent_id')) {
            $query->where('parent_id', (int) $request->validated('parent_id'));
        }

        if ($request->boolean('tree')) {
            $categories = $query->get();
            $maxDepth = $request->has('max_depth') ? (int) $request->validated('max_depth') : null;
            $rootsParentId = $request->filled('parent_id') ? (int) $request->validated('parent_id') : null;

            return response()->json([
                'data' => $treeService->nestedTree($categories, $rootsParentId, 0, $maxDepth),
            ]);
        }

        if ($request->validated('include') === 'children') {
            $query->with(['children' => function ($q): void {
                $q->where('is_active', true)
                    ->withCount('children')
                    ->orderBy('menu_order')
                    ->orderBy('sort_order')
                    ->orderBy('name');
            }]);
        }

        $categories = $query->paginate((int) $request->integer('per_page', 12));

        return response()->json($this->paginationPayload($categories));
    }

    public function show(string $slug, ShowCategoryRequest $request, CategoryFilterService $service): JsonResponse
    {
        $category = Category::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();
        $data = $service->dataForCategory($category, $request->validated());

        return response()->json([
            'category' => CategoryResource::make($category)->resolve(),
            'filters' => $data['filters'],
            'brands' => $data['brands'],
            'sorting_options' => $data['sorting_options'],
            'products' => $data['products'],
        ]);
    }

    public function breadcrumb(string $slug): JsonResponse
    {
        $category = Category::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();

        $chain = $this->breadcrumbChain($category);

        return response()->json([
            'data' => CategoryResource::collection($chain)->resolve(),
        ]);
    }

    /**
     * @return Collection<int, Category>
     */
    private function breadcrumbChain(Category $category): Collection
    {
        $path = $category->path;
        if (! $path) {
            return collect([$category]);
        }

        $ids = array_values(array_filter(array_map('intval', explode('/', (string) $path))));
        if ($ids === []) {
            return collect([$category]);
        }

        $categories = Category::query()
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        return collect($ids)
            ->map(fn (int $id): ?Category => $categories->get($id))
            ->filter()
            ->values();
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
