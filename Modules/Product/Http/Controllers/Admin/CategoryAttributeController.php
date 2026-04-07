<?php

namespace Modules\Product\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Auth\Http\Controllers\Admin\AdminController;
use Modules\Category\Models\Category;
use Modules\Product\Http\Requests\Admin\StoreCategoryAttributeRequest;
use Modules\Product\Http\Requests\Admin\UpdateCategoryAttributeRequest;
use Modules\Product\Models\CategoryAttribute;

class CategoryAttributeController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:admin.products.view')->only(['index', 'colors', 'sizes']);
        $this->middleware('permission:admin.products.edit')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(): View
    {
        $attributes = CategoryAttribute::query()->with('category')->withCount('values')->orderBy('id')->paginate(20);

        return view('product::admin.attributes.index', compact('attributes'));
    }

    public function colors(): View
    {
        $attributes = CategoryAttribute::query()
            ->where('code', 'color')
            ->with('category')
            ->withCount('values')
            ->orderBy('id')
            ->paginate(20);

        $pageTitle = __('Colors');

        return view('product::admin.attributes.index', compact('attributes', 'pageTitle'));
    }

    public function sizes(): View
    {
        $attributes = CategoryAttribute::query()
            ->where('code', 'size')
            ->with('category')
            ->withCount('values')
            ->orderBy('id')
            ->paginate(20);

        $pageTitle = __('Sizes');

        return view('product::admin.attributes.index', compact('attributes', 'pageTitle'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('product::admin.attributes.create', compact('categories'));
    }

    public function store(StoreCategoryAttributeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = Str::slug($data['code'] ?: $data['name'], '_');
        $data['type'] = $data['type'] ?? 'select';
        CategoryAttribute::query()->create($data);

        return redirect()->route('admin.attributes.index')->with('status', __('Attribute created successfully.'));
    }

    public function edit(CategoryAttribute $attribute): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('product::admin.attributes.edit', compact('attribute', 'categories'));
    }

    public function update(UpdateCategoryAttributeRequest $request, CategoryAttribute $attribute): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = Str::slug($data['code'] ?: $data['name'], '_');
        $data['type'] = $data['type'] ?? 'select';
        $attribute->update($data);

        return redirect()->route('admin.attributes.index')->with('status', __('Attribute updated successfully.'));
    }

    public function destroy(CategoryAttribute $attribute): RedirectResponse
    {
        $attribute->delete();

        return redirect()->route('admin.attributes.index')->with('status', __('Attribute deleted successfully.'));
    }
}
