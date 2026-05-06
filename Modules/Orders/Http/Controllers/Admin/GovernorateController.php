<?php

namespace Modules\Orders\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Orders\Models\Governorate;

class GovernorateController extends Controller
{
    public function index(): View
    {
        $governorates = Governorate::ordered()->get();

        return view('orders::admin.governorates.index', compact('governorates'));
    }

    public function create(): View
    {
        return view('orders::admin.governorates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:governorates,name',
            'name_en' => 'required|string|max:255|unique:governorates,name_en',
            'shipping_cost' => 'required|numeric|min:0|max:999999.99',
            'delivery_days' => 'required|integer|min:1|max:30',
            'is_active' => 'boolean',
        ]);

        Governorate::create([
            'name' => $request->name,
            'name_en' => $request->name_en,
            'shipping_cost' => $request->shipping_cost,
            'delivery_days' => $request->delivery_days,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.governorates.index')
            ->with('status', 'Governorate created successfully.');
    }

    public function edit(Governorate $governorate): View
    {
        return view('orders::admin.governorates.edit', compact('governorate'));
    }

    public function update(Request $request, Governorate $governorate)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:governorates,name,' . $governorate->id,
            'name_en' => 'required|string|max:255|unique:governorates,name_en,' . $governorate->id,
            'shipping_cost' => 'required|numeric|min:0|max:999999.99',
            'delivery_days' => 'required|integer|min:1|max:30',
            'is_active' => 'boolean',
        ]);

        $governorate->update([
            'name' => $request->name,
            'name_en' => $request->name_en,
            'shipping_cost' => $request->shipping_cost,
            'delivery_days' => $request->delivery_days,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.governorates.index')
            ->with('status', 'Governorate updated successfully.');
    }

    public function destroy(Governorate $governorate)
    {
        // Check if there are orders using this governorate
        if ($governorate->orders()->count() > 0) {
            return redirect()->route('admin.governorates.index')
                ->with('error', 'Cannot delete governorate. It has associated orders.');
        }

        $governorate->delete();

        return redirect()->route('admin.governorates.index')
            ->with('status', 'Governorate deleted successfully.');
    }

    public function toggleStatus(Governorate $governorate)
    {
        $governorate->update([
            'is_active' => !$governorate->is_active
        ]);

        return redirect()->back()
            ->with('status', 'Governorate status updated successfully.');
    }
}
