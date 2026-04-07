<?php

namespace Modules\Product\Observers;

use Modules\Product\Models\ProductAttribute;
use Modules\Product\Services\ProductCacheService;

class ProductAttributeObserver
{
    public function saved(ProductAttribute $attribute): void
    {
        app(ProductCacheService::class)->refreshAttributesSummary((int) $attribute->product_id);
    }

    public function deleted(ProductAttribute $attribute): void
    {
        app(ProductCacheService::class)->refreshAttributesSummary((int) $attribute->product_id);
    }
}
