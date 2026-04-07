<?php

namespace Modules\Product\Services\Admin;

use Modules\Product\Models\Product;

class ProductService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function store(array $data): Product
    {
        /** @var Product $product */
        $product = Product::query()->create($data);

        return $product->fresh(['categories', 'variants', 'images']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh(['categories', 'variants', 'images']);
    }
}
