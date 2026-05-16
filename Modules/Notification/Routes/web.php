<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\Http\Controllers\Admin\NotificationRecipientController;
use Modules\Notification\Http\Controllers\Admin\SettingsController;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/settings/notifications', [SettingsController::class, 'notifications'])
        ->name('settings.notifications');
    Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])
        ->name('settings.notifications.update');

    Route::get('/notification-recipients', [NotificationRecipientController::class, 'index'])
        ->name('notification-recipients.index');
    Route::get('/notification-recipients/create', [NotificationRecipientController::class, 'create'])
        ->name('notification-recipients.create');
    Route::post('/notification-recipients', [NotificationRecipientController::class, 'store'])
        ->name('notification-recipients.store');
    Route::get('/notification-recipients/{recipient}/edit', [NotificationRecipientController::class, 'edit'])
        ->name('notification-recipients.edit');
    Route::put('/notification-recipients/{recipient}', [NotificationRecipientController::class, 'update'])
        ->name('notification-recipients.update');
    Route::delete('/notification-recipients/{recipient}', [NotificationRecipientController::class, 'destroy'])
        ->name('notification-recipients.destroy');
    Route::post('/notification-recipients/{recipient}/toggle', [NotificationRecipientController::class, 'toggleStatus'])
        ->name('notification-recipients.toggle-status');
});
