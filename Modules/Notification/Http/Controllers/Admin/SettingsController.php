<?php

namespace Modules\Notification\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Auth\Http\Controllers\Admin\AdminController;
use Modules\Notification\Http\Requests\Admin\UpdateNotificationSettingsRequest;
use Modules\Notification\Services\Admin\NotificationSettingsService;

class SettingsController extends AdminController
{
    public function __construct(
        private NotificationSettingsService $settings
    ) {
        parent::__construct();

        $this->middleware('permission:admin.notifications.view')->only(['notifications']);
        $this->middleware('permission:admin.notifications.edit')->only(['updateNotifications']);
    }

    public function notifications(): View
    {
        $orderNotificationEmail = $this->settings->getOrderNotificationEmail();

        return view('notification::admin.settings.notifications', compact('orderNotificationEmail'));
    }

    public function updateNotifications(UpdateNotificationSettingsRequest $request): RedirectResponse
    {
        $email = $request->validated('order_notification_email');

        $this->settings->saveOrderNotificationEmail(
            is_string($email) && $email !== '' ? strtolower($email) : null
        );

        return redirect()
            ->route('admin.settings.notifications')
            ->with('status', __('Notification settings saved successfully.'));
    }
}
