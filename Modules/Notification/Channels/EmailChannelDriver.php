<?php

namespace Modules\Notification\Channels;

use Illuminate\Support\Facades\Mail;
use Modules\Notification\Contracts\ChannelDriverInterface;
use Modules\Notification\Mail\NewOrderAdminMail;
use Modules\Notification\Models\NotificationRecipient;
use Modules\Notification\Support\NotificationEvents;
use Modules\Orders\Models\Order;

class EmailChannelDriver implements ChannelDriverInterface
{
    public function send(NotificationRecipient $recipient, string $event, array $payload): void
    {
        match ($event) {
            NotificationEvents::ORDER_PLACED => $this->sendOrderPlaced($recipient, $payload),
            default => null,
        };
    }

    private function sendOrderPlaced(NotificationRecipient $recipient, array $payload): void
    {
        $order = $payload['order'] ?? null;

        if (! $order instanceof Order) {
            return;
        }

        Mail::to($recipient->address)->queue(new NewOrderAdminMail($order));
    }
}
