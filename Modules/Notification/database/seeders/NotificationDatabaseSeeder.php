<?php

namespace Modules\Notification\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Notification\Models\NotificationRecipient;
use Modules\Notification\Support\NotificationEvents;

class NotificationDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('NOTIFICATION_ORDER_ADMIN_EMAIL');

        if (! is_string($email) || $email === '') {
            return;
        }

        $source = config('notification.default_order_source', 'website');

        NotificationRecipient::query()->firstOrCreate(
            [
                'channel' => 'email',
                'event' => NotificationEvents::ORDER_PLACED,
                'address' => strtolower($email),
            ],
            [
                'label' => __('Orders inbox'),
                'is_active' => true,
                'filters' => ['source' => $source],
            ]
        );
    }
}
