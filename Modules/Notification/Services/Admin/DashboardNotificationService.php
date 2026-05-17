<?php

namespace Modules\Notification\Services\Admin;

use Modules\Orders\Models\Order;
use Modules\Product\Models\Variant;

class DashboardNotificationService
{
    public function getDashboardNotifications(): array
    {
        $ordersCount = $this->getNewOrderNotificationsCount();
        $lowStockCount = $this->getLowStockNotificationsCount();
        $items = [];

        if ($ordersCount > 0) {
            $items[] = [
                'title' => __('New orders'),
                'description' => trans_choice('{1}:count new order created in the last :hours hours.|[2,*]:count new orders created in the last :hours hours.', $ordersCount, [
                    'count' => $ordersCount,
                    'hours' => config('notification.dashboard_order_window_hours', 24),
                ]),
                'url' => route('admin.orders.index'),
            ];
        }

        if ($lowStockCount > 0) {
            $items[] = [
                'title' => __('Low stock products'),
                'description' => trans_choice('{1}:count variant is low on stock.|[2,*]:count variants are low on stock.', $lowStockCount, [
                    'count' => $lowStockCount,
                ]),
                'url' => route('admin.products.index'),
            ];
        }

        if (empty($items)) {
            $items[] = [
                'title' => __('No new notifications'),
                'description' => __('All systems are running smoothly.'),
                'url' => null,
            ];
        }

        return [
            'total' => $ordersCount + $lowStockCount,
            'items' => $items,
            'new_orders_count' => $ordersCount,
            'low_stock_count' => $lowStockCount,
        ];
    }

    public function getTotalNotifications(): int
    {
        return $this->getNewOrderNotificationsCount() + $this->getLowStockNotificationsCount();
    }

    public function getNewOrderNotificationsCount(): int
    {
        return Order::query()
            ->where('created_at', '>=', now()->subHours(config('notification.dashboard_order_window_hours', 24)))
            ->where('status', '!=', 'completed')
            ->count();
    }

    public function getLowStockNotificationsCount(): int
    {
        return Variant::query()
            ->where('stock', '<=', config('notification.low_stock_threshold', 5))
            ->where('is_active', true)
            ->count();
    }
}
