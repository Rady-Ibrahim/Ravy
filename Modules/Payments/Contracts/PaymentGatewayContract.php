<?php

namespace Modules\Payments\Contracts;

use App\Models\Order;
use Modules\Payments\DTOs\PaymentResponseDTO;

interface PaymentGatewayContract
{
    /**
     * Initiate payment for an order
     */
    public function initiate(Order $order, array $context = []): PaymentResponseDTO;

    /**
     * Verify payment response/webhook
     */
    public function verify(array $payload): PaymentResponseDTO;

    /**
     * Check if gateway supports webhooks
     */
    public function supportsWebhook(): bool;

    /**
     * Get gateway name
     */
    public function getName(): string;
}
