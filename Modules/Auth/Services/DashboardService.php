<?php

namespace Modules\Auth\Services;

use Modules\Auth\Models\User;
use Modules\Category\Models\Category;
use Modules\Orders\Models\Order;
use Modules\Product\Models\Product;

class DashboardService
{
    /**
     * Get dashboard KPI data
     */
    public function getKpiData(): array
    {
        return [
            'customers' => User::count(),
            'products' => $this->getTotalProducts(),
            'orders' => Order::count(),
            'revenue' => Order::where('status', 'completed')->sum('grand_total'),
        ];
    }

    /**
     * Get orders overview data
     */
    public function getOrdersOverview(): array
    {
        return [
            'total' => Order::count(),
            // some orders use 'pending_payment' as default status, include both
            'pending' => Order::whereIn('status', ['pending', 'pending_payment'])->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'processed' => Order::where('status', 'processed')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
        ];
    }

    /**
     * Get top categories by products count
     */
    public function getTopCategories(int $limit = 5): array
    {
        return Category::query()
            ->withCount('products')
            ->where('is_active', true)
            ->orderByDesc('products_count')
            ->limit($limit)
            ->get()
            ->map(fn(Category $cat) => [
                'label' => $cat->name,
                'value' => $cat->products_count,
                'pct' => 0, // Will be calculated if we have revenue data
            ])
            ->toArray();
    }

    /**
     * Get products statistics
     */
    public function getProductsStats(): array
    {
        return [
            'total' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'inactive' => Product::where('is_active', false)->count(),
            'featured' => Product::where('is_featured', true)->count(),
            'new' => Product::where('is_new', true)->count(),
            'total_views' => Product::sum('views_count') ?? 0,
            'avg_score' => Product::average('score') ?? 0,
        ];
    }

    /**
     * Get total products count
     */
    private function getTotalProducts(): int
    {
        return Product::count();
    }

    /**
     * Get category distribution data
     */
    public function getCategoryDistribution(): array
    {
        return Category::query()
            ->withCount('products')
            ->where('is_active', true)
            ->orderByDesc('products_count')
            ->get()
            ->map(fn(Category $cat) => [
                'name' => $cat->name,
                'count' => $cat->products_count,
            ])
            ->toArray();
    }

    /**
     * Get recently added products
     */
    public function getRecentProducts(int $limit = 10): array
    {
        return Product::query()
            ->with(['brand', 'primaryCategory'])
            ->where('is_active', true)
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(fn(Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'category' => $product->primaryCategory?->name,
                'created_at' => $product->created_at,
            ])
            ->toArray();
    }

    /**
     * Get in-house (boutique/pos) channel statistics
     */
    public function getInhouseStats(): array
    {
        $sources = ['pos', 'boutique', 'inhouse'];

        $inhouseOrdersQuery = Order::query()->whereIn('source', $sources);

        $productIds = \Modules\Orders\Models\OrderItem::query()
            ->whereIn('order_id', $inhouseOrdersQuery->pluck('id')->toArray())
            ->distinct()
            ->pluck('product_id')
            ->toArray();

        $inhouseRevenue = (float) $inhouseOrdersQuery->where('status', 'completed')->sum('grand_total');
        $totalRevenue = (float) Order::where('status', 'completed')->sum('grand_total');

        return [
            'share_pct' => $totalRevenue > 0 ? (int) round($inhouseRevenue / $totalRevenue * 100) : 0,
            'products' => count(array_filter($productIds)),
            'avg_score' => Product::whereIn('id', $productIds)->avg('score') ?? 0,
            'orders_30d' => $inhouseOrdersQuery->where('created_at', '>=', now()->subDays(30))->count(),
            'orders_total' => $inhouseOrdersQuery->count(),
            'revenue' => $inhouseRevenue,
        ];
    }
}
