<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Api\ProductController;
use Modules\Product\Http\Controllers\Api\BrandController;

Route::middleware('api')->prefix('api/v1')->group(function () {
    // Brands
    Route::get('/brands', [BrandController::class, 'index'])->name('api.brands.index');
    Route::get('/brands/{slug}', [BrandController::class, 'show'])->name('api.brands.show');

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products/{slug}/view', [ProductController::class, 'incrementViews'])
        ->middleware('throttle:60,1');
    Route::get('/products/{slug}', [ProductController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/products/{slug}/wishlist', [ProductController::class, 'toggleWishlist']);
        Route::get('/wishlist', [ProductController::class, 'wishlist']);
    });
});
