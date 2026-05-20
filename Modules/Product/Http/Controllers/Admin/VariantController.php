<?php

namespace Modules\Product\Http\Controllers\Admin;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Auth\Http\Controllers\Admin\AdminController;
use Modules\Product\Http\Requests\Admin\StoreVariantRequest;
use Modules\Product\Http\Requests\Admin\UpdateVariantRequest;
use Modules\Product\Models\CategoryAttributeValue;
use Modules\Product\Models\Product;
use Modules\Product\Models\Variant;
use Modules\Product\Services\Admin\VariantService;

class VariantController extends AdminController
{
    public function __construct(
        private readonly VariantService $service
    ) {
        parent::__construct();
        $this->middleware('permission:admin.products.view')->only(['index']);
        $this->middleware('permission:admin.products.edit')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Product $product): View
    {
        $variants = $product->variants()->with('attributeValues.attribute')->latest('id')->paginate(20);

        return view('product::admin.variants.index', compact('product', 'variants'));
    }

    public function create(Product $product): View
    {
        $attributeValues = $this->getAttributeValuesForProduct($product);

        return view('product::admin.variants.create', compact('product', 'attributeValues'));
    }

    public function store(StoreVariantRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $attributeValueIds = $data['attribute_value_ids'];
        unset($data['attribute_value_ids']);
        $data['product_id'] = $product->id;

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('variants', 'public');
        }

        try {
            $this->service->create($data, $attributeValueIds);
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['attribute_value_ids' => __('This variant combination already exists for this product.')]);
        }

        return redirect()->route('admin.products.variants.index', $product)->with('status', __('Variant created successfully.'));
    }

    public function edit(Product $product, Variant $variant): View
    {
        abort_unless((int) $variant->product_id === (int) $product->id, 404);
        $variant->load('attributeValues');
        $attributeValues = $this->getAttributeValuesForProduct($product, $variant);

        return view('product::admin.variants.edit', compact('product', 'variant', 'attributeValues'));
    }

    public function update(UpdateVariantRequest $request, Product $product, Variant $variant): RedirectResponse
    {
        abort_unless((int) $variant->product_id === (int) $product->id, 404);

        $data = $request->validated();
        $attributeValueIds = $data['attribute_value_ids'];
        unset($data['attribute_value_ids']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($variant->image) {
                $oldImagePath = public_path('storage/' . $variant->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $data['image'] = $request->file('image')->store('variants', 'public');
        }

        try {
            $this->service->update($variant, $data, $attributeValueIds);
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['attribute_value_ids' => __('This variant combination already exists for this product.')]);
        }

        return redirect()->route('admin.products.variants.index', $product)->with('status', __('Variant updated successfully.'));
    }

    public function destroy(Product $product, Variant $variant): RedirectResponse
    {
        abort_unless((int) $variant->product_id === (int) $product->id, 404);
        $variant->delete();

        return redirect()->route('admin.products.variants.index', $product)->with('status', __('Variant deleted successfully.'));
    }

    private function getAttributeValuesForProduct(Product $product, ?Variant $variant = null)
    {
        $selectedIds = $variant?->attributeValues?->pluck('id')->all() ?? [];
        // Determine relevant category IDs for the product (primary + assigned categories)
        $categoryIds = collect()
            ->when($product->primary_category_id, fn($c) => $c->push($product->primary_category_id))
            ->merge($product->categories()->pluck('categories.id'))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $query = CategoryAttributeValue::query()->with('attribute');

        if (! empty($categoryIds)) {
            $query->whereHas('attribute', function ($q) use ($categoryIds) {
                $q->whereIn('category_id', $categoryIds);
            });
        }

        if (! empty($selectedIds)) {
            // ensure existing selected values are included even if they belong to other categories
            $query->orWhereIn('id', $selectedIds);
        }

        return $query->orderBy('attribute_id')->orderBy('value')->get();
    }
}
