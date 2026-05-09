<?php

namespace Modules\Orders\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Orders\Http\Resources\Api\OrderResource;
use Modules\Orders\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->with('items')
            ->latest('id')
            ->paginate(10);

        return response()->json([
            'data' => OrderResource::collection(collect($orders->items()))->resolve(),
            'links' => [
                'first' => $orders->url(1),
                'last' => $orders->url($orders->lastPage()),
                'prev' => $orders->previousPageUrl(),
                'next' => $orders->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $orders->currentPage(),
                'from' => $orders->firstItem(),
                'last_page' => $orders->lastPage(),
                'path' => $orders->path(),
                'per_page' => $orders->perPage(),
                'to' => $orders->lastItem(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function show(Request $request, string $identifier): JsonResponse
    {
        $order = Order::query()
            ->where('user_id', $request->user()->id)
            ->where(function ($query) use ($identifier) {
                $query->where('id', $identifier)
                      ->orWhere('order_number', $identifier);
            })
            ->with('items')
            ->firstOrFail();

        return response()->json([
            'data' => OrderResource::make($order)->resolve(),
        ]);
    }

    public function cancel(Request $request, string $identifier): JsonResponse
    {
        $order = Order::query()
            ->where('user_id', $request->user()->id)
            ->where(function ($query) use ($identifier) {
                $query->where('id', $identifier)
                      ->orWhere('order_number', $identifier);
            })
            ->firstOrFail();

        if (! in_array($order->status, ['pending_payment', 'pending'], true)) {
            return response()->json([
                'message' => 'Order cannot be cancelled in its current status.',
            ], 422);
        }

        $order->update([
            'status' => 'cancelled',
            'payment_status' => 'void',
        ]);

        return response()->json([
            'message' => 'Order cancelled successfully.',
            'data' => OrderResource::make($order->fresh('items'))->resolve(),
        ]);
    }
}
