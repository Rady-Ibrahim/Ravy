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
    private function getGuestId(Request $request): string
    {
        $guestId = $request->cookie('guest_cart_id');
        
        if (!$guestId) {
            $guestId = str()->uuid()->toString();
            cookie()->queue('guest_cart_id', $guestId, 43200); // 30 days
        }
        
        return $guestId;
    }

    public function show(Request $request, CartService $service): JsonResponse
    {
        $user = $request->user();
        $guestId = $user ? null : $this->getGuestId($request);
        
        $cart = $service->getActiveCartWithRelations($user, $guestId);

        return response()->json($this->payload($cart, $service, $guestId));
    }

    public function addItem(AddCartItemRequest $request, CartService $service): JsonResponse
    {
        $user = $request->user();
        $guestId = $user ? null : $this->getGuestId($request);
        
        $cart = $service->addItem($user, $request->validated(), $guestId);

        return response()->json([
            'message' => 'Item added to cart successfully.',
            ...$this->payload($cart, $service, $guestId),
        ], 201);
    }

    public function updateItem(UpdateCartItemRequest $request, int $itemId, CartService $service): JsonResponse
    {
        $user = $request->user();
        $guestId = $user ? null : $this->getGuestId($request);
        
        $cart = $service->updateItemQty($user, $itemId, (int) $request->validated('qty'), $guestId);

        return response()->json([
            'message' => 'Cart item updated successfully.',
            ...$this->payload($cart, $service, $guestId),
        ]);
    }

    public function removeItem(Request $request, int $itemId, CartService $service): JsonResponse
    {
        $user = $request->user();
        $guestId = $user ? null : $this->getGuestId($request);
        
        $cart = $service->removeItem($user, $itemId, $guestId);

        return response()->json([
            'message' => 'Cart item removed successfully.',
            ...$this->payload($cart, $service, $guestId),
        ]);
    }

    public function clear(Request $request, CartService $service): JsonResponse
    {
        $user = $request->user();
        $guestId = $user ? null : $this->getGuestId($request);
        
        $cart = $service->clear($user, $guestId);

        return response()->json([
            'message' => 'Cart cleared successfully.',
            ...$this->payload($cart, $service, $guestId),
        ]);
    }

    private function payload($cart, CartService $service, ?string $guestId = null): array
    {
        return [
            'data' => [
                'id' => $cart->id,
                'status' => $cart->status,
                'guest_id' => $guestId,
                'items' => CartItemResource::collection($cart->items)->resolve(),
                'totals' => $service->totals($cart),
            ],
        ];
    }
}
