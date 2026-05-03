<?php

namespace Modules\Payments\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Payments\Services\PaymentService;

class WebhookController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Handle Paymob webhook
     */
    public function paymob(Request $request): JsonResponse
    {
        try {
            Log::info('Paymob webhook received', [
                'payload' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            $response = $this->paymentService->verifyWebhook('paymob', $request->all());

            if ($response->success) {
                Log::info('Paymob webhook processed successfully', [
                    'transaction_id' => $response->transactionId,
                    'provider_order_id' => $response->providerOrderId
                ]);
            } else {
                Log::warning('Paymob webhook processing failed', [
                    'message' => $response->message,
                    'metadata' => $response->metadata
                ]);
            }

            return response()->json([
                'success' => $response->success,
                'message' => $response->message ?? 'Webhook processed'
            ], $response->success ? 200 : 400);

        } catch (\Exception $e) {
            Log::error('Paymob webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Webhook processing failed'
            ], 500);
        }
    }

    /**
     * Handle generic webhook (for future payment methods)
     */
    public function handle(Request $request, string $paymentMethod): JsonResponse
    {
        try {
            Log::info("Webhook received for {$paymentMethod}", [
                'payload' => $request->all()
            ]);

            $response = $this->paymentService->verifyWebhook($paymentMethod, $request->all());

            return response()->json([
                'success' => $response->success,
                'message' => $response->message ?? 'Webhook processed'
            ], $response->success ? 200 : 400);

        } catch (\Exception $e) {
            Log::error("Webhook processing error for {$paymentMethod}", [
                'error' => $e->getMessage(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Webhook processing failed'
            ], 500);
        }
    }
}
