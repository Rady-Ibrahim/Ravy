<?php

namespace Modules\Product\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Auth\Http\Controllers\Admin\AdminController;
use Modules\Category\Models\Category;
use Modules\Product\Models\Brand;
use Modules\Product\Http\Requests\Admin\StoreProductRequest;
use Modules\Product\Http\Requests\Admin\UpdateProductRequest;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductImage;
use Modules\Product\Services\Admin\ProductService;

class ProductController extends AdminController
{
    public function __construct(
        private readonly ProductService $service
    ) {
        parent::__construct();
        $this->middleware('permission:admin.products.view')->only(['index', 'create', 'edit']);
        $this->middleware('permission:admin.products.create')->only(['store', 'create']);
        $this->middleware('permission:admin.products.edit')->only(['update', 'edit']);
        $this->middleware('permission:admin.products.delete')->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $query = Product::query()
            ->with(['brand', 'primaryCategory', 'categories', 'images'])
            ->withCount(['variants', 'images']);

        // Search by name/slug
        if ($search = $request->string('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->integer('is_active'));
        }

        // Filter by featured
        if ($request->filled('is_featured')) {
            $query->where('is_featured', (bool) $request->integer('is_featured'));
        }

        // Filter by new
        if ($request->filled('is_new')) {
            $query->where('is_new', (bool) $request->integer('is_new'));
        }

        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->integer('brand_id'));
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) {
                $q->where('category_id', request()->integer('category_id'));
            });
        }

        $products = $query
            ->latest('id')
            ->paginate((int) $request->integer('per_page', 20));

        return view('product::admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();
        $brands = Brand::query()->orderBy('name')->get();

        return view('product::admin.products.create', compact('categories', 'brands'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $coverImage = $request->file('cover_image');
        $categoryIds = $data['category_ids'] ?? [];
        unset($data['category_ids'], $data['cover_image']);
        $data['slug'] = $this->makeUniqueSlug($data['slug'] ?? $data['name']);
        $product = $this->service->store($data);

        if (! empty($categoryIds)) {
            $product->categories()->sync($categoryIds);
        }
        $this->syncCoverImage($product, $coverImage);

        return redirect()->route('admin.products.index')->with('status', __('Product created successfully.'));
    }

    public function edit(Product $product): View
    {
        $product->load(['categories', 'images']);
        $categories = Category::query()->orderBy('name')->get();
        $brands = Brand::query()->orderBy('name')->get();

        return view('product::admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $coverImage = $request->file('cover_image');
        $categoryIds = $data['category_ids'] ?? null;
        unset($data['category_ids'], $data['cover_image']);
        if (array_key_exists('slug', $data)) {
            $data['slug'] = $this->makeUniqueSlug($data['slug'] ?: $data['name'], $product->id);
        }
        $product = $this->service->update($product, $data);

        if (is_array($categoryIds)) {
            $product->categories()->sync($categoryIds);
        }
        $this->syncCoverImage($product, $coverImage);

        return redirect()->route('admin.products.index')->with('status', __('Product updated successfully.'));
    }

    private function syncCoverImage(Product $product, ?UploadedFile $image): void
    {
        if (! $image) {
            return;
        }

        $path = $image->store('catalog/products', 'public');
        $product->images()->where('is_primary', true)->update(['is_primary' => false]);
        ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => $path,
            'disk' => 'public',
            'type' => 'image',
            'is_primary' => true,
        ]);
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', __('Product deleted.'));
    }

    private function makeUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        $slug = $base ?: 'product';
        $index = 1;

        while (Product::query()
            ->when($ignoreId, fn($q) => $q->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = "{$base}-{$index}";
            $index++;
        }

        return $slug;
    }
}
