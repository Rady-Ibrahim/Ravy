<?php

namespace Modules\Notification\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Auth\Http\Controllers\Admin\AdminController;
use Modules\Notification\Http\Requests\Admin\StoreNotificationRecipientRequest;
use Modules\Notification\Http\Requests\Admin\UpdateNotificationRecipientRequest;
use Modules\Notification\Models\NotificationRecipient;
use Modules\Notification\Services\Admin\NotificationRecipientService;
use Modules\Notification\Support\NotificationEvents;

class NotificationRecipientController extends AdminController
{
    public function __construct(
        private NotificationRecipientService $service
    ) {
        parent::__construct();

        $this->middleware('permission:admin.notifications.view')->only(['index']);
        $this->middleware('permission:admin.notifications.create')->only(['create', 'store']);
        $this->middleware('permission:admin.notifications.edit')->only(['edit', 'update', 'toggleStatus']);
        $this->middleware('permission:admin.notifications.delete')->only(['destroy']);
    }

    public function index(): View
    {
        $recipients = NotificationRecipient::query()
            ->orderBy('event')
            ->orderBy('address')
            ->get();

        return view('notification::admin.recipients.index', compact('recipients'));
    }

    public function create(): View
    {
        $events = NotificationEvents::labels();

        return view('notification::admin.recipients.create', compact('events'));
    }

    public function store(StoreNotificationRecipientRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('admin.notification-recipients.index')
            ->with('status', __('Notification recipient created successfully.'));
    }

    public function edit(NotificationRecipient $recipient): View
    {
        $events = NotificationEvents::labels();

        return view('notification::admin.recipients.edit', compact('recipient', 'events'));
    }

    public function update(UpdateNotificationRecipientRequest $request, NotificationRecipient $recipient): RedirectResponse
    {
        $this->service->update($recipient, $request->validated());

        return redirect()
            ->route('admin.notification-recipients.index')
            ->with('status', __('Notification recipient updated successfully.'));
    }

    public function destroy(NotificationRecipient $recipient): RedirectResponse
    {
        $this->service->delete($recipient);

        return redirect()
            ->route('admin.notification-recipients.index')
            ->with('status', __('Notification recipient deleted successfully.'));
    }

    public function toggleStatus(NotificationRecipient $recipient): RedirectResponse
    {
        $this->service->toggleActive($recipient);

        return redirect()
            ->route('admin.notification-recipients.index')
            ->with('status', __('Notification recipient status updated.'));
    }
}
