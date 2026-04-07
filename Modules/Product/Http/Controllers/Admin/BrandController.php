<?php

namespace Modules\Product\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Auth\Http\Controllers\Admin\AdminController;
use Modules\Product\Http\Requests\Admin\StoreBrandRequest;
use Modules\Product\Http\Requests\Admin\UpdateBrandRequest;
use Modules\Product\Models\Brand;

class BrandController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:admin.products.view')->only(['index']);
        $this->middleware('permission:admin.products.edit')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(): View
    {
        $brands = Brand::query()->latest('id')->paginate(20);

        return view('product::admin.brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('product::admin.brands.create');
    }

    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $baseSlug = $data['slug'] ?? $data['name'];
        $data['slug'] = $this->makeUniqueSlug($baseSlug);
        unset($data['logo']);
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('catalog/brands', 'public');
        }

        Brand::query()->create($data);

        return redirect()->route('admin.brands.index')->with('status', __('Brand created successfully.'));
    }

    public function edit(Brand $brand): View
    {
        return view('product::admin.brands.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        $data = $request->validated();
        if (array_key_exists('slug', $data)) {
            $data['slug'] = $this->makeUniqueSlug($data['slug'] ?: $data['name'], $brand->id);
        }
        unset($data['logo']);
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('catalog/brands', 'public');
        }

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('status', __('Brand updated successfully.'));
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('status', __('Brand deleted successfully.'));
    }

    private function makeUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        $slug = $base ?: 'brand';
        $index = 1;

        while (Brand::query()
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = "{$base}-{$index}";
            $index++;
        }

        return $slug;
    }
}
