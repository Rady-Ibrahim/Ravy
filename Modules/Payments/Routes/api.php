<?php

// API routes — stateless (Sanctum). Do not add Spatie permission middleware here.

use Modules\Payments\Http\Controllers\Api\PaymentController;
use Modules\Payments\Http\Controllers\Api\WebhookController;

// Payment endpoints
Route::prefix('payments')->middleware('auth:sanctum')->group(function () {
    // Get supported payment methods
    Route::get('/methods', [PaymentController::class, 'methods']);
    
    // Initiate payment for an order
    Route::post('/initiate', [PaymentController::class, 'initiate']);
    
    // Get payment status for an order
    Route::get('/status/{order}', [PaymentController::class, 'status']);
});

// Webhook endpoints (no auth middleware - called by payment providers)
Route::prefix('webhooks')->group(function () {
    // Paymob webhook
    Route::post('/paymob', [WebhookController::class, 'paymob']);
    
    // Generic webhook handler for future payment methods
    Route::post('/{paymentMethod}', [WebhookController::class, 'handle']);
});

