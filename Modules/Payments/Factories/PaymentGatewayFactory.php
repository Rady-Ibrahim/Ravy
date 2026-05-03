<?php

namespace Modules\Payments\Factories;

use InvalidArgumentException;
use Modules\Payments\Contracts\PaymentGatewayContract;
use Modules\Payments\Gateways\CodPaymentGateway;
use Modules\Payments\Gateways\PaymobPaymentGateway;

class PaymentGatewayFactory
{
    /**
     * Create payment gateway instance based on method
     */
    public static function make(string $method): PaymentGatewayContract
    {
        return match (strtolower($method)) {
            'cod' => new CodPaymentGateway(),
            'paymob' => new PaymobPaymentGateway(),
            default => throw new InvalidArgumentException("Unsupported payment method: {$method}")
        };
    }

    /**
     * Get list of supported payment methods
     */
    public static function getSupportedMethods(): array
    {
        return [
            'cod' => [
                'name' => 'Cash on Delivery',
                'supports_webhook' => false,
                'description' => 'Pay when you receive your order'
            ],
            'paymob' => [
                'name' => 'Paymob',
                'supports_webhook' => true,
                'description' => 'Pay with card, wallet, or other payment methods'
            ]
        ];
    }

    /**
     * Check if payment method is supported
     */
    public static function isSupported(string $method): bool
    {
        return array_key_exists(strtolower($method), self::getSupportedMethods());
    }
}
