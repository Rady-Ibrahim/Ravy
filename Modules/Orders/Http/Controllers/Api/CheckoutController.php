<?php

namespace Modules\Orders\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Orders\Http\Requests\Api\CheckoutRequest;
use Modules\Orders\Http\Resources\Api\OrderResource;
use Modules\Orders\Services\Api\CartService;

class CheckoutController extends Controller
{
    public function summary(Request $request, CartService $service): JsonResponse
    {
        $cart = $service->getActiveCartWithRelations($request->user());

        return response()->json([
            'data' => [
                'cart_id' => $cart->id,
                'items_count' => $cart->items->count(),
                'totals' => $service->totals($cart),
                'packaging_options' => [
                    ['id' => 'eco', 'label' => 'Eco packaging'],
                ],
            ],
        ]);
    }

    public function placeOrder(CheckoutRequest $request, CartService $service): JsonResponse
    {
        $order = $service->checkout($request->user(), $request->validated());

        return response()->json([
            'message' => 'Order created and waiting for payment.',
            'data' => OrderResource::make($order)->resolve(),
        ], 201);
    }
}
