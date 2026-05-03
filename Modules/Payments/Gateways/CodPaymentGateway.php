<?php

namespace Modules\Payments\Gateways;

use App\Models\Order;
use Modules\Payments\Contracts\PaymentGatewayContract;
use Modules\Payments\DTOs\PaymentResponseDTO;

class CodPaymentGateway implements PaymentGatewayContract
{
    public function initiate(Order $order, array $context = []): PaymentResponseDTO
    {
        // COD doesn't need actual payment initiation
        // Just return success with order reference
        return PaymentResponseDTO::success(
            transactionId: 'cod_' . $order->id . '_' . time(),
            metadata: [
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'payment_method' => 'cod',
                'status' => 'cod_pending'
            ]
        );
    }

    public function verify(array $payload): PaymentResponseDTO
    {
        // COD verification happens when order is delivered
        // This method can be used to mark COD as paid upon delivery
        $orderId = $payload['order_id'] ?? null;
        
        if (!$orderId) {
            return PaymentResponseDTO::failure('Order ID is required for COD verification');
        }

        return PaymentResponseDTO::success(
            transactionId: 'cod_verified_' . $orderId . '_' . time(),
            metadata: [
                'order_id' => $orderId,
                'verified_at' => now(),
                'payment_method' => 'cod',
                'status' => 'paid_on_delivery'
            ]
        );
    }

    public function supportsWebhook(): bool
    {
        return false; // COD doesn't use webhooks
    }

    public function getName(): string
    {
        return 'cod';
    }
}
