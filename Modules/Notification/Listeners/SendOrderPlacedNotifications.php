<?php

namespace Modules\Notification\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Notification\Services\NotificationDispatcher;
use Modules\Notification\Support\NotificationEvents;
use Modules\Orders\Events\OrderPlaced;

class SendOrderPlacedNotifications implements ShouldQueue
{
    public function __construct(
        private NotificationDispatcher $dispatcher
    ) {}

    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->loadMissing(['items', 'governorate', 'user']);

        $this->dispatcher->dispatch(
            NotificationEvents::ORDER_PLACED,
            ['order' => $order],
            $this->dispatcher->contextFromOrder($order)
        );
    }
}
