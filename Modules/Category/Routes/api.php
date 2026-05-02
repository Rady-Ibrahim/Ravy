<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\Api\CategoryController;

Route::middleware('api')->prefix('api/v1')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}/breadcrumb', [CategoryController::class, 'breadcrumb']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);
});
