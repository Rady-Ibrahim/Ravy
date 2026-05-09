<?php

namespace Modules\Product\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Auth\Http\Controllers\Admin\AdminController;
use Modules\Product\Http\Requests\Admin\StoreSizeRequest;
use Modules\Product\Http\Requests\Admin\UpdateSizeRequest;
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
        
        Size::query()->create($data);

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
}
