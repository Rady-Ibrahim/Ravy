<?php

namespace Modules\Orders\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Orders\Models\Governorate;

class GovernorateController extends Controller
{
    /**
     * Get all active governorates
     */
    public function index(): JsonResponse
    {
        $governorates = Governorate::active()->ordered()->get(['id', 'name', 'name_en', 'shipping_cost', 'delivery_days']);
        
        return response()->json([
            'success' => true,
            'data' => $governorates
        ]);
    }

    /**
     * Get governorate by ID
     */
    public function show(Governorate $governorate): JsonResponse
    {
        if (!$governorate->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Governorate is not available for shipping'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $governorate
        ]);
    }

    /**
     * Calculate shipping cost for governorate
     */
    public function calculateShipping(Request $request): JsonResponse
    {
        $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            'subtotal' => 'required|numeric|min:0'
        ]);

        $governorate = Governorate::findOrFail($request->governorate_id);
        
        if (!$governorate->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Shipping is not available for this governorate'
            ], 400);
        }

        $shippingCost = $governorate->shipping_cost;
        
        // You can add logic for free shipping above certain amount
        $freeShippingThreshold = config('orders.free_shipping_threshold', 0);
        if ($freeShippingThreshold > 0 && $request->subtotal >= $freeShippingThreshold) {
            $shippingCost = 0;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'governorate' => [
                    'id' => $governorate->id,
                    'name' => $governorate->name,
                    'name_en' => $governorate->name_en,
                    'shipping_cost' => $shippingCost,
                    'delivery_days' => $governorate->delivery_days
                ],
                'shipping_cost' => $shippingCost,
                'free_shipping_applied' => $shippingCost == 0 && $governorate->shipping_cost > 0,
                'delivery_days' => $governorate->delivery_days
            ]
        ]);
    }
}
