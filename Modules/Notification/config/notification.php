<?php

return [
    'channels' => [
        'email' => Modules\Notification\Channels\EmailChannelDriver::class,
    ],

    'default_order_source' => env('NOTIFICATION_DEFAULT_ORDER_SOURCE', 'website'),
    'dashboard_order_window_hours' => env('NOTIFICATION_DASHBOARD_ORDER_WINDOW_HOURS', 24),
    'low_stock_threshold' => env('NOTIFICATION_LOW_STOCK_THRESHOLD', 5),
];
