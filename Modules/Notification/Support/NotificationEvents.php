<?php

namespace Modules\Notification\Support;

class NotificationEvents
{
    public const ORDER_PLACED = 'order.placed';

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::ORDER_PLACED => __('New order from website'),
        ];
    }
}
