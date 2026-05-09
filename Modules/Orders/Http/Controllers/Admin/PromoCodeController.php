<?php

namespace Modules\Orders\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Orders\Models\PromoCode;

class PromoCodeController extends Controller
{
    public function index()
    {
        $promoCodes = PromoCode::query()
            ->latest('id')
            ->paginate(10);

        return view('orders::admin.promo-codes.index', compact('promoCodes'));
    }

    public function create()
    {
        return view('orders::admin.promo-codes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:promo_codes,code'],
            'description' => ['nullable', 'string', 'max:255'],
            'discount_type' => ['required', 'in:fixed,percentage'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        PromoCode::create([
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'min_amount' => $validated['min_amount'] ?? 0,
            'max_discount_amount' => $validated['max_discount_amount'] ?? null,
            'max_uses' => $validated['max_uses'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Promo code created successfully.');
    }

    public function edit(PromoCode $promoCode)
    {
        return view('orders::admin.promo-codes.edit', compact('promoCode'));
    }

    public function update(Request $request, PromoCode $promoCode)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:promo_codes,code,' . $promoCode->id],
            'description' => ['nullable', 'string', 'max:255'],
            'discount_type' => ['required', 'in:fixed,percentage'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $promoCode->update([
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'min_amount' => $validated['min_amount'] ?? 0,
            'max_discount_amount' => $validated['max_discount_amount'] ?? null,
            'max_uses' => $validated['max_uses'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Promo code updated successfully.');
    }

    public function destroy(PromoCode $promoCode)
    {
        $promoCode->delete();

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Promo code deleted successfully.');
    }

    public function toggleStatus(PromoCode $promoCode)
    {
        $promoCode->update([
            'is_active' => !$promoCode->is_active,
        ]);

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Promo code status updated successfully.');
    }
}
