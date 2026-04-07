<?php

namespace Modules\Product\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Product\Models\ProductAttribute;
use Modules\Product\Models\Variant;
use Modules\Product\Observers\ProductAttributeObserver;
use Modules\Product\Observers\VariantObserver;

class ProductServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'product');

        Variant::observe(VariantObserver::class);
        ProductAttribute::observe(ProductAttributeObserver::class);
    }
}