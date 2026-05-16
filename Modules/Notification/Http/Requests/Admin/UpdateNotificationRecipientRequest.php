<?php

namespace Modules\Notification\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Notification\Models\NotificationRecipient;
use Modules\Notification\Support\NotificationEvents;

class UpdateNotificationRecipientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin.notifications.edit') ?? false;
    }

    public function rules(): array
    {
        /** @var NotificationRecipient|null $recipient */
        $recipient = $this->route('recipient');

        return [
            'channel' => ['required', 'string', Rule::in(['email'])],
            'event' => ['required', 'string', Rule::in(array_keys(NotificationEvents::labels()))],
            'address' => [
                'required',
                'email',
                'max:255',
                Rule::unique('notification_recipients', 'address')
                    ->where('channel', $this->input('channel'))
                    ->where('event', $this->input('event'))
                    ->ignore($recipient?->id),
            ],
            'label' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'filter_source' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated($key, $default);

        $filters = [];
        if (! empty($data['filter_source'])) {
            $filters['source'] = $data['filter_source'];
        }

        return [
            'channel' => $data['channel'],
            'event' => $data['event'],
            'address' => strtolower($data['address']),
            'label' => $data['label'] ?? null,
            'is_active' => $this->boolean('is_active', true),
            'filters' => $filters === [] ? null : $filters,
        ];
    }
}
