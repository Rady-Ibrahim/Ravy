<?php

namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;
use Modules\Product\Http\Resources\Api\BrandResource;
use Modules\Product\Models\Brand;

class BrandController extends Controller
{
    /**
     * Get all active brands
     */
    public function index(): ResourceCollection
    {
        $brands = Brand::query()
            ->where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return BrandResource::collection($brands);
    }

    /**
     * Get single brand by slug with products
     */
    public function show(string $slug): JsonResponse
    {
        $brand = Brand::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'products' => fn($q) => $q->where('is_active', true)
                    ->with(['images', 'primaryCategory', 'brand'])
                    ->withCount('variants')
                    ->orderBy('name')
            ])
            ->firstOrFail();

        return response()->json([
            'data' => [
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug,
                'logo' => $brand->logo,
                'products_count' => $brand->products_count,
                'products' => $brand->products->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'image' => $p->images->first()?->path,
                    'price' => $p->price,
                    'is_featured' => $p->is_featured,
                    'variants_count' => $p->variants_count,
                ]),
            ]
        ]);
    }
}
