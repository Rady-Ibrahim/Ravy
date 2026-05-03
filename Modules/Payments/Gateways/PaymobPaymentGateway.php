<?php

namespace Modules\Payments\Gateways;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Payments\Contracts\PaymentGatewayContract;
use Modules\Payments\DTOs\PaymentResponseDTO;

class PaymobPaymentGateway implements PaymentGatewayContract
{
    private string $apiKey;
    private string $integrationId;
    private string $hmacSecret;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.paymob.api_key');
        $this->integrationId = config('services.paymob.integration_id');
        $this->hmacSecret = config('services.paymob.hmac_secret');
        $this->baseUrl = config('services.paymob.base_url', 'https://accept.paymob.com/api');
    }

    public function initiate(Order $order, array $context = []): PaymentResponseDTO
    {
        try {
            // Step 1: Authentication
            $authToken = $this->authenticate();
            
            // Step 2: Order Registration
            $paymobOrder = $this->registerOrder($authToken, $order);
            
            // Step 3: Payment Key Generation
            $paymentKey = $this->generatePaymentKey($authToken, $paymobOrder['id'], $order, $context);
            
            // Step 4: Build payment URL
            $paymentUrl = $this->buildPaymentUrl($paymentKey['token']);

            return PaymentResponseDTO::success(
                transactionId: $paymobOrder['id'],
                redirectUrl: $paymentUrl,
                providerOrderId: $paymobOrder['id'],
                metadata: [
                    'order_id' => $order->id,
                    'paymob_order_id' => $paymobOrder['id'],
                    'payment_key_token' => $paymentKey['token'],
                    'amount' => $order->total_amount,
                    'currency' => $order->currency ?? 'EGP'
                ]
            );

        } catch (\Exception $e) {
            Log::error('Paymob payment initiation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return PaymentResponseDTO::failure(
                'Payment initiation failed: ' . $e->getMessage(),
                ['order_id' => $order->id]
            );
        }
    }

    public function verify(array $payload): PaymentResponseDTO
    {
        try {
            // Verify webhook signature
            if (!$this->verifyHmac($payload)) {
                return PaymentResponseDTO::failure('Invalid webhook signature');
            }

            $obj = json_decode($payload['obj'], true);
            
            if ($obj['success'] === false) {
                return PaymentResponseDTO::failure('Payment failed', $obj);
            }

            $transactionId = $obj['id'] ?? null;
            $orderId = $obj['order']['id'] ?? null;

            if (!$transactionId || !$orderId) {
                return PaymentResponseDTO::failure('Missing transaction data');
            }

            // Retrieve transaction details from Paymob
            $transaction = $this->retrieveTransaction($transactionId);

            return PaymentResponseDTO::success(
                transactionId: $transactionId,
                providerOrderId: $orderId,
                metadata: [
                    'paymob_transaction_id' => $transactionId,
                    'paymob_order_id' => $orderId,
                    'amount' => $transaction['amount'] ?? null,
                    'currency' => $transaction['currency'] ?? null,
                    'payment_method' => $transaction['source_data']['type'] ?? null
                ]
            );

        } catch (\Exception $e) {
            Log::error('Paymob payment verification failed', [
                'payload' => $payload,
                'error' => $e->getMessage()
            ]);

            return PaymentResponseDTO::failure(
                'Payment verification failed: ' . $e->getMessage()
            );
        }
    }

    public function supportsWebhook(): bool
    {
        return true;
    }

    public function getName(): string
    {
        return 'paymob';
    }

    private function authenticate(): string
    {
        $response = Http::post("{$this->baseUrl}/auth/tokens", [
            'api_key' => $this->apiKey
        ]);

        if (!$response->successful()) {
            throw new \Exception('Paymob authentication failed');
        }

        return $response->json()['token'];
    }

    private function registerOrder(string $authToken, Order $order): array
    {
        $response = Http::withToken($authToken)
            ->post("{$this->baseUrl}/ecommerce/orders", [
                'amount_cents' => (int) ($order->total_amount * 100),
                'currency' => $order->currency ?? 'EGP',
                'merchant_order_id' => $order->order_number,
                'shipping_data' => $this->getShippingData($order),
                'items' => $this->getOrderItems($order)
            ]);

        if (!$response->successful()) {
            throw new \Exception('Paymob order registration failed');
        }

        return $response->json();
    }

    private function generatePaymentKey(string $authToken, int $paymobOrderId, Order $order, array $context): array
    {
        $billingData = $context['billing_data'] ?? $this->getDefaultBillingData($order);

        $response = Http::withToken($authToken)
            ->post("{$this->baseUrl}/acceptance/payment_keys", [
                'amount_cents' => (int) ($order->total_amount * 100),
                'currency' => $order->currency ?? 'EGP',
                'order_id' => $paymobOrderId,
                'integration_id' => $this->integrationId,
                'billing_data' => $billingData,
                'expiration' => 3600, // 1 hour
                'lock_order_when_paid' => true
            ]);

        if (!$response->successful()) {
            throw new \Exception('Paymob payment key generation failed');
        }

        return $response->json();
    }

    private function buildPaymentUrl(string $paymentKeyToken): string
    {
        return "{$this->baseUrl}/acceptance/iframes/{$this->integrationId}?payment_token={$paymentKeyToken}";
    }

    private function verifyHmac(array $payload): bool
    {
        if (!isset($payload['hmac']) || !isset($payload['obj'])) {
            return false;
        }

        $data = json_decode($payload['obj'], true);
        $concatenatedString = $this->buildHmacString($data);
        $calculatedHmac = hash_hmac('sha512', $concatenatedString, $this->hmacSecret);

        return hash_equals($calculatedHmac, $payload['hmac']);
    }

    private function buildHmacString(array $data): string
    {
        $fields = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_refunded',
            'is_standalone_payment',
            'is_voided',
            'order',
            'owner',
            'pending',
            'source_data_pan',
            'source_data_type',
            'success'
        ];

        $values = [];
        foreach ($fields as $field) {
            $value = $this->getNestedValue($data, $field);
            if ($value !== null) {
                $values[] = $value;
            }
        }

        return implode('', $values);
    }

    private function getNestedValue(array $data, string $field)
    {
        $keys = explode('.', $field);
        $value = $data;

        foreach ($keys as $key) {
            if (!is_array($value) || !isset($value[$key])) {
                return null;
            }
            $value = $value[$key];
        }

        return $value;
    }

    private function retrieveTransaction(string $transactionId): array
    {
        $authToken = $this->authenticate();
        
        $response = Http::withToken($authToken)
            ->get("{$this->baseUrl}/acceptance/transactions/{$transactionId}");

        if (!$response->successful()) {
            throw new \Exception('Failed to retrieve transaction details');
        }

        return $response->json();
    }

    private function getShippingData(Order $order): array
    {
        // This should be adapted based on your order structure
        return [
            'apartment' => 'NA',
            'building' => 'NA',
            'city' => 'Cairo',
            'country' => 'EG',
            'email' => $order->user->email,
            'first_name' => $order->user->first_name ?? 'Test',
            'floor' => 'NA',
            'last_name' => $order->user->last_name ?? 'User',
            'phone_number' => $order->user->phone ?? '01000000000',
            'postal_code' => 'NA',
            'state' => 'Cairo',
            'street' => 'NA'
        ];
    }

    private function getOrderItems(Order $order): array
    {
        // This should be adapted based on your order items structure
        return [];
    }

    private function getDefaultBillingData(Order $order): array
    {
        return [
            'apartment' => 'NA',
            'building' => 'NA',
            'city' => 'Cairo',
            'country' => 'EG',
            'email' => $order->user->email,
            'first_name' => $order->user->first_name ?? 'Test',
            'floor' => 'NA',
            'last_name' => $order->user->last_name ?? 'User',
            'phone_number' => $order->user->phone ?? '01000000000',
            'postal_code' => 'NA',
            'state' => 'Cairo',
            'street' => 'NA'
        ];
    }
}
