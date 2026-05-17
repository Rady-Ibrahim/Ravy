<?php

namespace Modules\Product\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Product\Models\Variant;

class ProductStockLow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Variant $variant,
    ) {}
}
