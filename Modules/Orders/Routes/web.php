<?php

use Illuminate\Support\Facades\Route;
use Modules\Orders\Http\Controllers\Admin\OrderController;

use Modules\Orders\Http\Controllers\Admin\GovernorateController;

// Admin routes
Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    // Orders routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderNumber}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{orderNumber}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{orderNumber}', [OrderController::class, 'update'])->name('orders.update');
    Route::post('/orders/{orderNumber}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::delete('/orders/{orderNumber}', [OrderController::class, 'destroy'])->name('orders.destroy');
    
    // Governorates routes
    Route::get('/governorates', [GovernorateController::class, 'index'])->name('governorates.index');
    Route::get('/governorates/create', [GovernorateController::class, 'create'])->name('governorates.create');
    Route::post('/governorates', [GovernorateController::class, 'store'])->name('governorates.store');
    Route::get('/governorates/{governorate}/edit', [GovernorateController::class, 'edit'])->name('governorates.edit');
    Route::put('/governorates/{governorate}', [GovernorateController::class, 'update'])->name('governorates.update');
    Route::delete('/governorates/{governorate}', [GovernorateController::class, 'destroy'])->name('governorates.destroy');
    Route::post('/governorates/{governorate}/toggle', [GovernorateController::class, 'toggleStatus'])->name('governorates.toggle-status');
});

