<?php

return [
    'channels' => [
        'email' => Modules\Notification\Channels\EmailChannelDriver::class,
    ],

    'default_order_source' => env('NOTIFICATION_DEFAULT_ORDER_SOURCE', 'website'),
];
