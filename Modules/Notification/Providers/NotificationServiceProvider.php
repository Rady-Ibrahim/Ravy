<?php

namespace Modules\Notification\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\Notification\Listeners\SendOrderPlacedNotifications;
use Modules\Notification\Listeners\SendProductStockLowNotifications;
use Modules\Orders\Events\OrderPlaced;
use Modules\Product\Events\ProductStockLow;

class NotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/admin_permission_matrix.php',
            'admin_permission_matrix'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../config/notification.php',
            'notification'
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'notification');

        Event::listen(
            OrderPlaced::class,
            SendOrderPlacedNotifications::class,
        );

        Event::listen(
            ProductStockLow::class,
            SendProductStockLowNotifications::class,
        );
    }
}
