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
    private function getGuestId(Request $request, ?string $guestIdFromBody = null): string
    {
        // First check if guest_id is provided in request body
        if ($guestIdFromBody) {
            return $guestIdFromBody;
        }
        
        // Otherwise use cookie
        $guestId = $request->cookie('guest_cart_id');
        
        if (!$guestId) {
            $guestId = str()->uuid()->toString();
            cookie()->queue('guest_cart_id', $guestId, 43200); // 30 days
        }
        
        return $guestId;
    }

    public function summary(Request $request, CartService $service): JsonResponse
    {
        $user = $request->user();
        $guestId = $user ? null : $this->getGuestId($request);
        
        $cart = $service->getActiveCartWithRelations($user, $guestId);

        return response()->json([
            'data' => [
                'cart_id' => $cart->id,
                'guest_id' => $guestId,
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
        $user = $request->user();
        $guestId = $user ? null : $this->getGuestId($request, $request->input('guest_id'));
        
        $order = $service->checkout($user, $request->validated(), $guestId);

        return response()->json([
            'message' => 'Order created and waiting for payment.',
            'data' => OrderResource::make($order)->resolve(),
        ], 201);
    }
}
