<?php

namespace Modules\Product\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Auth\Http\Controllers\Admin\AdminController;
use Modules\Product\Http\Requests\Admin\StoreSizeRequest;
use Modules\Product\Http\Requests\Admin\UpdateSizeRequest;
use Modules\Product\Models\CategoryAttribute;
use Modules\Product\Models\CategoryAttributeValue;
use Modules\Product\Models\Size;

class SizeController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:admin.products.view')->only(['index']);
        $this->middleware('permission:admin.products.edit')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(): View
    {
        $sizes = Size::query()->latest()->paginate(20);

        return view('product::admin.sizes.index', compact('sizes'));
    }

    public function create(): View
    {
        return view('product::admin.sizes.create');
    }

    public function store(StoreSizeRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('sizes', 'public');
        }

        $size = Size::query()->create($data);
        $this->syncSizeToAttributeValues($size);

        return redirect()->route('admin.sizes.index')->with('status', __('Size created successfully.'));
    }

    public function edit(Size $size): View
    {
        return view('product::admin.sizes.edit', compact('size'));
    }

    public function update(UpdateSizeRequest $request, Size $size): RedirectResponse
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($size->image) {
                $oldImagePath = public_path('storage/' . $size->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $data['image'] = $request->file('image')->store('sizes', 'public');
        }

        $size->update($data);
        $this->syncSizeToAttributeValues($size);

        return redirect()->route('admin.sizes.index')->with('status', __('Size updated successfully.'));
    }

    public function destroy(Size $size): RedirectResponse
    {
        // Delete image if exists
        if ($size->image) {
            $imagePath = public_path('storage/' . $size->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $size->delete();

        return redirect()->route('admin.sizes.index')->with('status', __('Size deleted successfully.'));
    }

    private function syncSizeToAttributeValues(Size $size): void
    {
        $slug = Str::slug($size->code ?: $size->name, '_');

        $sizeAttributes = CategoryAttribute::query()
            ->where('code', 'size')
            ->get();

        foreach ($sizeAttributes as $attribute) {
            CategoryAttributeValue::query()->updateOrCreate(
                ['attribute_id' => $attribute->id, 'slug' => $slug],
                [
                    'value' => $size->name,
                    'extra' => [
                        'code' => $size->code,
                        'code_from' => $size->code_from,
                        'code_to' => $size->code_to,
                    ],
                ]
            );
        }
    }
}
