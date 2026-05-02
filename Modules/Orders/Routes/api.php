<?php

use Illuminate\Support\Facades\Route;
use Modules\Orders\Http\Controllers\Api\CartController;
use Modules\Orders\Http\Controllers\Api\CheckoutController;
use Modules\Orders\Http\Controllers\Api\OrderController;

Route::middleware(['api', 'auth:sanctum'])->prefix('api/v1')->group(function () {
    Route::get('/cart', [CartController::class, 'show']);
    Route::post('/cart/items', [CartController::class, 'addItem'])->middleware('throttle:20,1');
    Route::patch('/cart/items/{itemId}', [CartController::class, 'updateItem'])->middleware('throttle:30,1');
    Route::delete('/cart/items/{itemId}', [CartController::class, 'removeItem'])->middleware('throttle:30,1');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->middleware('throttle:10,1');

    Route::get('/checkout/summary', [CheckoutController::class, 'summary']);
    Route::post('/orders/checkout', [CheckoutController::class, 'placeOrder'])->middleware('throttle:10,1');

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{orderNumber}', [OrderController::class, 'show']);
    Route::post('/orders/{orderNumber}/cancel', [OrderController::class, 'cancel'])->middleware('throttle:10,1');
});
