<?php

namespace Modules\Category\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Auth\Http\Controllers\Admin\AdminController;
use Modules\Category\Http\Requests\Admin\StoreCategoryRequest;
use Modules\Category\Http\Requests\Admin\UpdateCategoryRequest;
use Modules\Category\Models\Category;
use Modules\Category\Services\Admin\CategoryService;

class CategoryController extends AdminController
{
    public function __construct(
        private readonly CategoryService $service
    ) {
        parent::__construct();
        $this->middleware('permission:admin.categories.view')->only(['index', 'create', 'edit']);
        $this->middleware('permission:admin.categories.create')->only(['store', 'create']);
        $this->middleware('permission:admin.categories.edit')->only(['update', 'edit']);
        $this->middleware('permission:admin.categories.delete')->only(['destroy']);
    }

    public function index(): View
    {
        $categories = Category::query()
            ->with('parent')
            ->withCount('products')
            ->orderBy('menu_order')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('category::admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parents = $this->buildHierarchyOptions();

        return view('category::admin.categories.create', compact('parents'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->makeUniqueSlug($data['slug'] ?? $data['name']);
        $data = $this->handleUploads($request, $data);
        Category::query()->create($data);

        return redirect()->route('admin.categories.index')->with('status', __('Category created successfully.'));
    }

    public function edit(Category $category): View
    {
        $parents = $this->buildHierarchyOptions($category->id);

        return view('category::admin.categories.edit', compact('category', 'parents'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();
        if (array_key_exists('slug', $data)) {
            $data['slug'] = $this->makeUniqueSlug($data['slug'] ?: $data['name'], $category->id);
        }
        $data = $this->handleUploads($request, $data);
        $category->update($data);

        return redirect()->route('admin.categories.index')->with('status', __('Category updated successfully.'));
    }

    /**
     * @param  array<string,mixed>  $data
     * @return array<string,mixed>
     */
    private function handleUploads(StoreCategoryRequest|UpdateCategoryRequest $request, array $data): array
    {
        foreach (['image', 'banner', 'icon'] as $field) {
            if ($request->file($field) instanceof UploadedFile) {
                $data[$field] = $request->file($field)->store('catalog/categories', 'public');
            }
        }

        return $data;
    }

    public function destroy(Category $category): RedirectResponse
    {
        $result = $this->service->canBeDeleted($category);
        if (! $result['allowed']) {
            return redirect()->back()->withErrors(['category' => $result['reason']]);
        }

        $category->delete();

        return redirect()->back()->with('status', __('Category deleted.'));
    }

    private function makeUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        $slug = $base ?: 'category';
        $index = 1;

        while (Category::query()
            ->when($ignoreId, fn($q) => $q->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = "{$base}-{$index}";
            $index++;
        }

        return $slug;
    }

    /**
     * Build hierarchical options for parent category dropdown
     * with breadcrumb-style labels (e.g., "Women > Clothing > Dresses")
     */
    private function buildHierarchyOptions(?int $excludeId = null): array
    {
        $categories = Category::query()
            ->when($excludeId, fn($q) => $q->whereKeyNot($excludeId))
            ->with('parent')
            ->orderBy('menu_order')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return $categories->mapWithKeys(function (Category $category) {
            $path = $this->buildCategoryPath($category);
            return [$category->id => $path];
        })->toArray();
    }

    /**
     * Build breadcrumb path for a category (e.g., "Women > Clothing")
     */
    private function buildCategoryPath(Category $category): string
    {
        $breadcrumbs = [];
        $current = $category;

        while ($current) {
            array_unshift($breadcrumbs, $current->name);
            $current = $current->parent;
        }

        return implode(' > ', $breadcrumbs);
    }
}
