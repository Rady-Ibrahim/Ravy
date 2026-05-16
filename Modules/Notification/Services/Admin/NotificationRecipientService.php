<?php

namespace Modules\Notification\Services\Admin;

use Modules\Notification\Models\NotificationRecipient;

class NotificationRecipientService
{
    public function create(array $data): NotificationRecipient
    {
        return NotificationRecipient::query()->create($data);
    }

    public function update(NotificationRecipient $recipient, array $data): NotificationRecipient
    {
        $recipient->update($data);

        return $recipient->fresh();
    }

    public function delete(NotificationRecipient $recipient): void
    {
        $recipient->delete();
    }

    public function toggleActive(NotificationRecipient $recipient): NotificationRecipient
    {
        $recipient->update(['is_active' => ! $recipient->is_active]);

        return $recipient->fresh();
    }
}
