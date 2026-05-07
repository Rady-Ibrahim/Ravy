<?php

namespace Modules\Orders\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Orders\Http\Requests\Api\ValidatePromoCodeRequest;
use Modules\Orders\Services\Api\PromoCodeService;

class PromoCodeController extends Controller
{
    public function __construct(
        private PromoCodeService $promoCodeService
    ) {}

    public function validate(ValidatePromoCodeRequest $request): JsonResponse
    {
        try {
            $subtotal = (float) $request->validated('subtotal');
            $result = $this->promoCodeService->validateAndApply(
                $request->validated('promo_code'),
                $subtotal
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'promo_code' => $result['promo_code']->code,
                    'discount_amount' => $result['discount_amount'],
                    'discount_type' => $result['promo_code']->discount_type,
                    'discount_value' => $result['promo_code']->discount_value,
                ],
                'message' => $result['message']
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid promo code',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function details(Request $request): JsonResponse
    {
        $request->validate([
            'promo_code' => ['required', 'string', 'max:50']
        ]);

        $promoCode = $this->promoCodeService->getPromoCodeDetails($request->promo_code);

        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code not found or expired'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'code' => $promoCode->code,
                'description' => $promoCode->description,
                'discount_type' => $promoCode->discount_type,
                'discount_value' => $promoCode->discount_value,
                'min_amount' => $promoCode->min_amount,
                'max_discount_amount' => $promoCode->max_discount_amount,
                'expires_at' => $promoCode->expires_at,
                'max_uses' => $promoCode->max_uses,
                'used_count' => $promoCode->used_count,
            ]
        ]);
    }
}
