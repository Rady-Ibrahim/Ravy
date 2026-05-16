<?php

namespace Modules\Notification\Services\Admin;

use Modules\Notification\Models\NotificationRecipient;
use Modules\Notification\Support\NotificationEvents;

class NotificationSettingsService
{
    public function getOrderNotificationEmail(): ?string
    {
        $recipient = NotificationRecipient::query()
            ->active()
            ->forEvent(NotificationEvents::ORDER_PLACED)
            ->forChannel('email')
            ->orderBy('id')
            ->first();

        return $recipient?->address;
    }

    public function saveOrderNotificationEmail(?string $email): void
    {
        $source = config('notification.default_order_source', 'website');

        $query = NotificationRecipient::query()
            ->forEvent(NotificationEvents::ORDER_PLACED)
            ->forChannel('email');

        if ($email === null || $email === '') {
            $query->update(['is_active' => false]);

            return;
        }

        $email = strtolower($email);

        $query->clone()->where('address', '!=', $email)->update(['is_active' => false]);

        NotificationRecipient::query()->updateOrCreate(
            [
                'channel' => 'email',
                'event' => NotificationEvents::ORDER_PLACED,
                'address' => $email,
            ],
            [
                'label' => __('Orders inbox'),
                'is_active' => true,
                'filters' => ['source' => $source],
            ]
        );
    }
}
