<?php

namespace Modules\Payments\DTOs;

class PaymentResponseDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $transactionId = null,
        public readonly ?string $redirectUrl = null,
        public readonly ?string $message = null,
        public readonly array $metadata = [],
        public readonly ?string $providerOrderId = null
    ) {}

    public static function success(
        ?string $transactionId = null,
        ?string $redirectUrl = null,
        array $metadata = [],
        ?string $providerOrderId = null
    ): self {
        return new self(
            success: true,
            transactionId: $transactionId,
            redirectUrl: $redirectUrl,
            metadata: $metadata,
            providerOrderId: $providerOrderId
        );
    }

    public static function failure(string $message, array $metadata = []): self
    {
        return new self(
            success: false,
            message: $message,
            metadata: $metadata
        );
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'transaction_id' => $this->transactionId,
            'redirect_url' => $this->redirectUrl,
            'message' => $this->message,
            'metadata' => $this->metadata,
            'provider_order_id' => $this->providerOrderId
        ];
    }
}
