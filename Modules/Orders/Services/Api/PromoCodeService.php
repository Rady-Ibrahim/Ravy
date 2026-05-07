<?php

namespace Modules\Orders\Services\Api;

use Illuminate\Validation\ValidationException;
use Modules\Orders\Models\PromoCode;

class PromoCodeService
{
    public function validateAndApply(string $code, float $subtotal): array
    {
        $promoCode = PromoCode::query()
            ->where('code', strtoupper($code))
            ->valid()
            ->notUsedUp()
            ->first();

        if (!$promoCode) {
            throw ValidationException::withMessages([
                'promo_code' => ['Invalid or expired promo code.'],
            ]);
        }

        if (!$promoCode->isValid()) {
            throw ValidationException::withMessages([
                'promo_code' => ['Promo code is no longer valid.'],
            ]);
        }

        if ($promoCode->min_amount && $subtotal < $promoCode->min_amount) {
            throw ValidationException::withMessages([
                'promo_code' => ["Minimum order amount is {$promoCode->min_amount} for this promo code."],
            ]);
        }

        $discountAmount = $promoCode->calculateDiscount($subtotal);

        if ($discountAmount <= 0) {
            throw ValidationException::withMessages([
                'promo_code' => ['Promo code cannot be applied to this order.'],
            ]);
        }

        return [
            'promo_code' => $promoCode,
            'discount_amount' => $discountAmount,
            'message' => $this->getSuccessMessage($promoCode, $discountAmount),
        ];
    }

    public function applyPromoCode(PromoCode $promoCode): void
    {
        $promoCode->incrementUsage();
    }

    private function getSuccessMessage(PromoCode $promoCode, float $discountAmount): string
    {
        $discountType = $promoCode->discount_type === 'percentage' ? '%' : ' fixed';
        $discountValue = $promoCode->discount_type === 'percentage' 
            ? $promoCode->discount_value 
            : number_format($promoCode->discount_value, 2);

        return "Promo code applied! {$discountValue}{$discountType} discount ({$discountAmount} saved)";
    }

    public function getPromoCodeDetails(string $code): ?PromoCode
    {
        return PromoCode::query()
            ->where('code', strtoupper($code))
            ->valid()
            ->notUsedUp()
            ->first();
    }
}
