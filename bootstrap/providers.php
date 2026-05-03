<?php

use App\Providers\AppServiceProvider;
use Modules\Auth\Providers\AuthServiceProvider;
use Modules\Category\Providers\CategoryServiceProvider;
use Modules\Orders\Providers\OrdersServiceProvider;
use Modules\Payments\Providers\PaymentsServiceProvider;
use Modules\Product\Providers\ProductServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    CategoryServiceProvider::class,
    OrdersServiceProvider::class,
    PaymentsServiceProvider::class,
    ProductServiceProvider::class,
];
