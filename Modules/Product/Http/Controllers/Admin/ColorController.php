<?php

namespace Modules\Product\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Auth\Http\Controllers\Admin\AdminController;
use Modules\Product\Http\Requests\Admin\StoreColorRequest;
use Modules\Product\Http\Requests\Admin\UpdateColorRequest;
use Modules\Product\Models\Color;

class ColorController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:admin.products.view')->only(['index']);
        $this->middleware('permission:admin.products.edit')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(): View
    {
        $colors = Color::query()->latest()->paginate(20);

        return view('product::admin.colors.index', compact('colors'));
    }

    public function create(): View
    {
        return view('product::admin.colors.create');
    }

    public function store(StoreColorRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('colors', 'public');
        }
        
        Color::query()->create($data);

        return redirect()->route('admin.colors.index')->with('status', __('Color created successfully.'));
    }

    public function edit(Color $color): View
    {
        return view('product::admin.colors.edit', compact('color'));
    }

    public function update(UpdateColorRequest $request, Color $color): RedirectResponse
    {
        $data = $request->validated();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($color->image) {
                $oldImagePath = public_path('storage/' . $color->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $data['image'] = $request->file('image')->store('colors', 'public');
        }
        
        $color->update($data);

        return redirect()->route('admin.colors.index')->with('status', __('Color updated successfully.'));
    }

    public function destroy(Color $color): RedirectResponse
    {
        // Delete image if exists
        if ($color->image) {
            $imagePath = public_path('storage/' . $color->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        $color->delete();

        return redirect()->route('admin.colors.index')->with('status', __('Color deleted successfully.'));
    }
}
