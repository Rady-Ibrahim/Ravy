<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Admin\BrandController;
use Modules\Product\Http\Controllers\Admin\CategoryAttributeController;
use Modules\Product\Http\Controllers\Admin\CategoryAttributeValueController;
use Modules\Product\Http\Controllers\Admin\ColorController;
use Modules\Product\Http\Controllers\Admin\ProductController;
use Modules\Product\Http\Controllers\Admin\SizeController;
use Modules\Product\Http\Controllers\Admin\VariantController;

Route::middleware(['web', 'auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('products.variants', VariantController::class)->except(['show']);
        Route::resource('brands', BrandController::class)->except(['show']);
        Route::resource('colors', ColorController::class)->except(['show']);
        Route::resource('sizes', SizeController::class)->except(['show']);
        Route::get('attributes/colors', [CategoryAttributeController::class, 'colors'])->name('attributes.colors');
        Route::get('attributes/sizes', [CategoryAttributeController::class, 'sizes'])->name('attributes.sizes');
        Route::resource('attributes', CategoryAttributeController::class)->except(['show']);
        Route::resource('attributes.values', CategoryAttributeValueController::class)->except(['show']);
    });

