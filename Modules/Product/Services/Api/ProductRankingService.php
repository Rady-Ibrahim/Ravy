<?php

namespace Modules\Product\Services\Api;

use Illuminate\Support\Facades\DB;
use Modules\Product\Models\Product;

class ProductRankingService
{
    public function recalculateScores(): void
    {
        $weights = config('ranking.weights', [
            'total_sales' => 0.5,
            'views_count' => 0.2,
            'is_featured' => 0.2,
            'is_new' => 0.1,
        ]);

        Product::query()->select(['id', 'total_sales', 'views_count', 'is_featured', 'is_new', 'sort_order'])
            ->chunkById(200, function ($products) use ($weights): void {
                foreach ($products as $product) {
                    $score = ((float) $product->total_sales * (float) $weights['total_sales'])
                        + ((float) $product->views_count * (float) $weights['views_count'])
                        + ((float) ($product->is_featured ? 1 : 0) * (float) $weights['is_featured'])
                        + ((float) ($product->is_new ? 1 : 0) * (float) $weights['is_new'])
                        + ((float) $product->sort_order * 0.01);

                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['score' => round($score, 4)]);
                }
            });
    }
}
