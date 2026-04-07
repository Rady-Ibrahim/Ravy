<?php

namespace Modules\Product\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Auth\Http\Controllers\Admin\AdminController;
use Modules\Product\Http\Requests\Admin\StoreCategoryAttributeValueRequest;
use Modules\Product\Http\Requests\Admin\UpdateCategoryAttributeValueRequest;
use Modules\Product\Models\CategoryAttribute;
use Modules\Product\Models\CategoryAttributeValue;

class CategoryAttributeValueController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:admin.products.view')->only(['index']);
        $this->middleware('permission:admin.products.edit')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(CategoryAttribute $attribute): View
    {
        $values = $attribute->values()->latest('id')->paginate(30);

        return view('product::admin.attribute-values.index', compact('attribute', 'values'));
    }

    public function create(CategoryAttribute $attribute): View
    {
        return view('product::admin.attribute-values.create', compact('attribute'));
    }

    public function store(StoreCategoryAttributeValueRequest $request, CategoryAttribute $attribute): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug'] ?: $data['value']);
        $data['extra'] = $data['extra'] ? ['value' => $data['extra']] : null;

        $attribute->values()->create($data);

        return redirect()->route('admin.attributes.values.index', $attribute)->with('status', __('Value created successfully.'));
    }

    public function edit(CategoryAttribute $attribute, CategoryAttributeValue $value): View
    {
        abort_unless((int) $value->attribute_id === (int) $attribute->id, 404);

        return view('product::admin.attribute-values.edit', compact('attribute', 'value'));
    }

    public function update(UpdateCategoryAttributeValueRequest $request, CategoryAttribute $attribute, CategoryAttributeValue $value): RedirectResponse
    {
        abort_unless((int) $value->attribute_id === (int) $attribute->id, 404);
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug'] ?: $data['value']);
        $data['extra'] = $data['extra'] ? ['value' => $data['extra']] : null;

        $value->update($data);

        return redirect()->route('admin.attributes.values.index', $attribute)->with('status', __('Value updated successfully.'));
    }

    public function destroy(CategoryAttribute $attribute, CategoryAttributeValue $value): RedirectResponse
    {
        abort_unless((int) $value->attribute_id === (int) $attribute->id, 404);
        $value->delete();

        return redirect()->route('admin.attributes.values.index', $attribute)->with('status', __('Value deleted successfully.'));
    }
}
