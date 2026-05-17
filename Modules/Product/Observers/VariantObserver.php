<?php

namespace Modules\Product\Observers;

use Illuminate\Support\Facades\Event;
use Modules\Product\Events\ProductStockLow;
use Modules\Product\Models\Variant;
use Modules\Product\Services\ProductCacheService;

class VariantObserver
{
    public function created(Variant $variant): void
    {
        $this->refreshCache($variant);
        $this->maybeDispatchStockLow($variant, null);
    }

    public function updated(Variant $variant): void
    {
        $this->refreshCache($variant);
        $this->maybeDispatchStockLow($variant, (int) $variant->getOriginal('stock'));
    }

    public function deleted(Variant $variant): void
    {
        $this->refreshCache($variant);
    }

    private function refreshCache(Variant $variant): void
    {
        $cache = app(ProductCacheService::class);
        $cache->refreshPriceRange((int) $variant->product_id);
        $cache->refreshAttributesSummary((int) $variant->product_id);
    }

    private function maybeDispatchStockLow(Variant $variant, ?int $previousStock): void
    {
        $threshold = (int) config('notification.low_stock_threshold', 5);

        if (! $variant->is_active) {
            return;
        }

        if ($variant->stock <= $threshold && ($previousStock === null || $previousStock > $threshold)) {
            Event::dispatch(new ProductStockLow($variant));
        }
    }
}
