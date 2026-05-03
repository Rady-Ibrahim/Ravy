<?php

namespace Modules\Payments\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Payments\Contracts\PaymentGatewayContract;
use Modules\Payments\DTOs\PaymentResponseDTO;
use Modules\Payments\Factories\PaymentGatewayFactory;

class PaymentService
{
    public function initiatePayment(Order $order, string $paymentMethod, array $context = []): PaymentResponseDTO
    {
        try {
            // Validate payment method
            if (!PaymentGatewayFactory::isSupported($paymentMethod)) {
                return PaymentResponseDTO::failure("Unsupported payment method: {$paymentMethod}");
            }

            // Get gateway instance
            $gateway = PaymentGatewayFactory::make($paymentMethod);

            // Generate idempotency key
            $idempotencyKey = $this->generateIdempotencyKey($order, $paymentMethod);

            // Check for existing transaction with same idempotency key
            $existingTransaction = $this->findTransactionByIdempotencyKey($idempotencyKey);
            if ($existingTransaction) {
                return $this->buildResponseFromTransaction($existingTransaction);
            }

            // Create payment transaction record
            $transaction = $this->createTransaction($order, $paymentMethod, $idempotencyKey, $context);

            // Initiate payment with gateway
            $response = $gateway->initiate($order, $context);

            // Update transaction based on response
            $this->updateTransactionAfterInitiation($transaction, $response);

            // Update order payment method and reference
            $this->updateOrderPaymentInfo($order, $paymentMethod, $response->transactionId);

            return $response;

        } catch (\Exception $e) {
            Log::error('Payment initiation failed', [
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return PaymentResponseDTO::failure(
                'Payment initiation failed: ' . $e->getMessage(),
                ['order_id' => $order->id, 'payment_method' => $paymentMethod]
            );
        }
    }

    public function verifyWebhook(string $paymentMethod, array $payload): PaymentResponseDTO
    {
        try {
            $gateway = PaymentGatewayFactory::make($paymentMethod);
            
            if (!$gateway->supportsWebhook()) {
                return PaymentResponseDTO::failure("Payment method {$paymentMethod} does not support webhooks");
            }

            // Verify webhook with gateway
            $response = $gateway->verify($payload);

            if ($response->success) {
                // Process the successful verification
                $this->processSuccessfulWebhook($paymentMethod, $response, $payload);
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('Webhook verification failed', [
                'payment_method' => $paymentMethod,
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return PaymentResponseDTO::failure(
                'Webhook verification failed: ' . $e->getMessage()
            );
        }
    }

    private function generateIdempotencyKey(Order $order, string $paymentMethod): string
    {
        return md5("{$order->id}-{$paymentMethod}-{$order->total_amount}-" . $order->created_at->timestamp);
    }

    private function findTransactionByIdempotencyKey(string $idempotencyKey): ?\Modules\Payments\Models\PaymentTransaction
    {
        // Note: You'll need to create the PaymentTransaction model
        return \Modules\Payments\Models\PaymentTransaction::where('idempotency_key', $idempotencyKey)->first();
    }

    private function createTransaction(Order $order, string $paymentMethod, string $idempotencyKey, array $context): \Modules\Payments\Models\PaymentTransaction
    {
        return \Modules\Payments\Models\PaymentTransaction::create([
            'order_id' => $order->id,
            'provider' => $paymentMethod,
            'method' => $this->getPaymentMethodType($paymentMethod),
            'status' => 'pending',
            'amount' => $order->total_amount,
            'currency' => $order->currency ?? 'EGP',
            'idempotency_key' => $idempotencyKey,
            'request_payload' => $context
        ]);
    }

    private function updateTransactionAfterInitiation(\Modules\Payments\Models\PaymentTransaction $transaction, PaymentResponseDTO $response): void
    {
        $transaction->update([
            'status' => $response->success ? 'pending' : 'failed',
            'provider_transaction_id' => $response->transactionId,
            'provider_order_id' => $response->providerOrderId,
            'response_payload' => $response->toArray()
        ]);
    }

    private function updateOrderPaymentInfo(Order $order, string $paymentMethod, ?string $transactionId): void
    {
        $order->update([
            'payment_method' => $paymentMethod,
            'payment_reference' => $transactionId
        ]);
    }

    private function buildResponseFromTransaction(\Modules\Payments\Models\PaymentTransaction $transaction): PaymentResponseDTO
    {
        $responsePayload = $transaction->response_payload ?? [];

        return new PaymentResponseDTO(
            success: $transaction->status === 'completed',
            transactionId: $transaction->provider_transaction_id,
            redirectUrl: $responsePayload['redirect_url'] ?? null,
            message: $responsePayload['message'] ?? null,
            metadata: $responsePayload['metadata'] ?? [],
            providerOrderId: $transaction->provider_order_id
        );
    }

    private function processSuccessfulWebhook(string $paymentMethod, PaymentResponseDTO $response, array $payload): void
    {
        DB::transaction(function () use ($paymentMethod, $response, $payload) {
            // Find the transaction
            $transaction = \Modules\Payments\Models\PaymentTransaction::where('provider_transaction_id', $response->transactionId)
                ->orWhere('provider_order_id', $response->providerOrderId)
                ->first();

            if (!$transaction) {
                Log::warning('Transaction not found for webhook', [
                    'payment_method' => $paymentMethod,
                    'transaction_id' => $response->transactionId,
                    'provider_order_id' => $response->providerOrderId
                ]);
                return;
            }

            // Update transaction
            $transaction->update([
                'status' => 'completed',
                'response_payload' => array_merge($transaction->response_payload ?? [], $payload)
            ]);

            // Update order
            $order = $transaction->order;
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing' // or whatever business logic you need
            ]);

            // Additional business logic can go here
            // e.g., send confirmation email, update inventory, etc.
        });
    }

    private function getPaymentMethodType(string $paymentMethod): string
    {
        return match ($paymentMethod) {
            'cod' => 'cod',
            'paymob' => 'online',
            default => 'unknown'
        };
    }

    /**
     * Get supported payment methods
     */
    public function getSupportedPaymentMethods(): array
    {
        return PaymentGatewayFactory::getSupportedMethods();
    }

    /**
     * Check if payment method is supported
     */
    public function isPaymentMethodSupported(string $method): bool
    {
        return PaymentGatewayFactory::isSupported($method);
    }
}
