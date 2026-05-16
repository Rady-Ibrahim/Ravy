<?php

namespace Modules\Notification\Services;

use Modules\Notification\Contracts\ChannelDriverInterface;
use Modules\Notification\Models\NotificationRecipient;
use Modules\Orders\Models\Order;

class NotificationDispatcher
{
    public function dispatch(string $event, array $payload, array $context = []): void
    {
        $recipients = NotificationRecipient::query()
            ->active()
            ->forEvent($event)
            ->get();

        foreach ($recipients as $recipient) {
            if (! $recipient->matchesContext($context)) {
                continue;
            }

            $driver = $this->resolveDriver($recipient->channel);

            if ($driver === null) {
                continue;
            }

            $driver->send($recipient, $event, $payload);
        }
    }

    public function contextFromOrder(Order $order): array
    {
        return [
            'source' => $order->source,
        ];
    }

    private function resolveDriver(string $channel): ?ChannelDriverInterface
    {
        $class = config("notification.channels.{$channel}");

        if (! is_string($class) || ! class_exists($class)) {
            return null;
        }

        $driver = app($class);

        return $driver instanceof ChannelDriverInterface ? $driver : null;
    }
}
