<?php

namespace Modules\Orders\Providers;

use Illuminate\Support\ServiceProvider;

class OrdersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'orders');
        
        $router = $this->app['router'];
        $router->aliasMiddleware('optional.sanctum', \Modules\Orders\Http\Middleware\OptionalAuthSanctum::class);
    }
}