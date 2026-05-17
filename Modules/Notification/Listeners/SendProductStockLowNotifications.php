<?php

namespace Modules\Notification\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Notification\Services\NotificationDispatcher;
use Modules\Notification\Support\NotificationEvents;
use Modules\Product\Events\ProductStockLow;

class SendProductStockLowNotifications implements ShouldQueue
{
    public function __construct(
        private readonly NotificationDispatcher $dispatcher
    ) {}

    public function handle(ProductStockLow $event): void
    {
        $this->dispatcher->dispatch(
            NotificationEvents::PRODUCT_STOCK_LOW,
            ['variant' => $event->variant],
        );
    }
}
