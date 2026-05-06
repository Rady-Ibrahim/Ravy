<?php

namespace Modules\Orders\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Orders\Models\Order;
use Modules\Payments\Models\PaymentTransaction;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'items.product', 'paymentTransactions'])
            ->orderBy('created_at', 'desc');

        // Search by order number or customer email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by order status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('orders::admin.orders.index', compact('orders'));
    }

    public function show(string $orderNumber): View
    {
        $order = Order::with(['user', 'items.product', 'items.variant.attributeValues', 'paymentTransactions'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('orders::admin.orders.show', compact('order'));
    }

    public function edit(string $orderNumber): View
    {
        $order = Order::with(['user', 'items.product', 'items.variant.attributeValues', 'paymentTransactions'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('orders::admin.orders.edit', compact('order'));
    }

    public function update(Request $request, string $orderNumber)
    {
        $request->validate([
            'status' => 'required|in:pending_payment,pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,unpaid,failed,refunded',
            'payment_method' => 'nullable|in:cod,paymob,stripe',
            'source' => 'nullable|string|max:50',
            'tracking_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1500',
        ]);

        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        
        $order->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'source' => $request->source,
            'tracking_number' => $request->tracking_number,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.orders.show', $order->order_number)
            ->with('status', 'Order updated successfully.');
    }

    public function updateStatus(Request $request, string $orderNumber)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $order->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('status', 'Order status updated successfully.');
    }

    public function destroy(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        
        // Delete order items first
        $order->items()->delete();
        
        // Delete payment transactions
        $order->paymentTransactions()->delete();
        
        // Delete the order
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('status', 'Order deleted successfully.');
    }
}
