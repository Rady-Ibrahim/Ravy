<?php

namespace Modules\Payments\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Payments\Services\PaymentService;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Initiate payment for an order
     */
    public function initiate(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:cod,paymob',
            'context' => 'array'
        ]);

        try {
            $order = Order::findOrFail($request->order_id);
            
            // Validate order belongs to authenticated user
            if ($order->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Validate order status
            if ($order->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order already paid'
                ], 400);
            }

            $response = $this->paymentService->initiatePayment(
                $order,
                $request->payment_method,
                $request->context ?? []
            );

            return response()->json([
                'success' => $response->success,
                'data' => $response->toArray(),
                'message' => $response->message
            ], $response->success ? 200 : 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment initiation failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get supported payment methods
     */
    public function methods(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->paymentService->getSupportedPaymentMethods()
        ]);
    }

    /**
     * Get payment status for an order
     */
    public function status(Request $request, Order $order): JsonResponse
    {
        // Validate order belongs to authenticated user
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_method' => $order->payment_method,
                'payment_reference' => $order->payment_reference,
                'payment_status' => $order->payment_status,
                'status' => $order->status
            ]
        ]);
    }
}
