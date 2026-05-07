<?php

namespace Modules\Orders\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Orders\Models\PromoCode;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        $promoCodes = [
            [
                'code' => 'WELCOME10',
                'description' => 'Welcome discount for new customers',
                'discount_type' => 'percentage',
                'discount_value' => 10.00,
                'min_amount' => 100.00,
                'max_discount_amount' => 50.00,
                'max_uses' => 100,
                'is_active' => true,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(6),
            ],
            [
                'code' => 'SUMMER20',
                'description' => 'Summer special discount',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'min_amount' => 200.00,
                'max_discount_amount' => 100.00,
                'max_uses' => 50,
                'is_active' => true,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
            ],
            [
                'code' => 'FLAT50',
                'description' => 'Flat discount for orders above 300',
                'discount_type' => 'fixed',
                'discount_value' => 50.00,
                'min_amount' => 300.00,
                'max_discount_amount' => null,
                'max_uses' => 25,
                'is_active' => true,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(2),
            ],
        ];

        foreach ($promoCodes as $promoCode) {
            PromoCode::create($promoCode);
        }
    }
}
