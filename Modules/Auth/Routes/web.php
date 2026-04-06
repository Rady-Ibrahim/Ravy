<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Admin\AuthController;
use Modules\Auth\Http\Controllers\Admin\DashboardController;
use Modules\Auth\Http\Controllers\Admin\PermissionController;
use Modules\Auth\Http\Controllers\Admin\PermissionMatrixController;
use Modules\Auth\Http\Controllers\Admin\RoleController;
use Modules\Auth\Http\Controllers\Admin\UserController;

Route::middleware('web')->get('/', function () {
    return auth()->check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('admin.auth.login');
});

Route::middleware('web')->prefix('admin/auth')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.auth.login');
        Route::post('/login', [AuthController::class, 'login'])->name('admin.auth.login.submit');
    });

    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth')
        ->name('admin.auth.logout');
});

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('roles/matrix', [PermissionMatrixController::class, 'index'])->name('roles.matrix');
    Route::put('roles/matrix', [PermissionMatrixController::class, 'update'])->name('roles.matrix.update');

    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class)->only(['index', 'create', 'store']);
});
