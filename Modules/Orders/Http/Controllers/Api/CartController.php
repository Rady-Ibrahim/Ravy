<?php

namespace Modules\Orders\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Orders\Http\Requests\Api\AddCartItemRequest;
use Modules\Orders\Http\Requests\Api\UpdateCartItemRequest;
use Modules\Orders\Http\Resources\Api\CartItemResource;
use Modules\Orders\Services\Api\CartService;

class CartController extends Controller
{
    public function show(Request $request, CartService $service): JsonResponse
    {
        $cart = $service->getActiveCartWithRelations($request->user());

        return response()->json($this->payload($cart, $service));
    }

    public function addItem(AddCartItemRequest $request, CartService $service): JsonResponse
    {
        $cart = $service->addItem($request->user(), $request->validated());

        return response()->json([
            'message' => 'Item added to cart successfully.',
            ...$this->payload($cart, $service),
        ], 201);
    }

    public function updateItem(UpdateCartItemRequest $request, int $itemId, CartService $service): JsonResponse
    {
        $cart = $service->updateItemQty($request->user(), $itemId, (int) $request->validated('qty'));

        return response()->json([
            'message' => 'Cart item updated successfully.',
            ...$this->payload($cart, $service),
        ]);
    }

    public function removeItem(Request $request, int $itemId, CartService $service): JsonResponse
    {
        $cart = $service->removeItem($request->user(), $itemId);

        return response()->json([
            'message' => 'Cart item removed successfully.',
            ...$this->payload($cart, $service),
        ]);
    }

    public function clear(Request $request, CartService $service): JsonResponse
    {
        $cart = $service->clear($request->user());

        return response()->json([
            'message' => 'Cart cleared successfully.',
            ...$this->payload($cart, $service),
        ]);
    }

    private function payload($cart, CartService $service): array
    {
        return [
            'data' => [
                'id' => $cart->id,
                'status' => $cart->status,
                'items' => CartItemResource::collection($cart->items)->resolve(),
                'totals' => $service->totals($cart),
            ],
        ];
    }
}
