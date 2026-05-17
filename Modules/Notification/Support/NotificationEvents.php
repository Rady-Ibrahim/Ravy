<?php

namespace Modules\Notification\Support;

class NotificationEvents
{
    public const ORDER_PLACED = 'order.placed';
    public const PRODUCT_STOCK_LOW = 'product.stock.low';

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::ORDER_PLACED => __('New order from website'),
            self::PRODUCT_STOCK_LOW => __('Low stock product'),
        ];
    }
}
