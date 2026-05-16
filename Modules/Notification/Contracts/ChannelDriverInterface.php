<?php

namespace Modules\Notification\Contracts;

use Modules\Notification\Models\NotificationRecipient;

interface ChannelDriverInterface
{
    public function send(NotificationRecipient $recipient, string $event, array $payload): void;
}
