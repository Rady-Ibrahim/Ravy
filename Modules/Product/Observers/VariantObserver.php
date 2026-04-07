<?php

namespace Modules\Product\Observers;

use Modules\Product\Models\Variant;
use Modules\Product\Services\ProductCacheService;

class VariantObserver
{
    public function deleted(Variant $variant): void
    {
        $cache = app(ProductCacheService::class);
        $cache->refreshPriceRange((int) $variant->product_id);
        $cache->refreshAttributesSummary((int) $variant->product_id);
    }
}
