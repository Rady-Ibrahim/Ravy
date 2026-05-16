<?php

namespace Modules\Notification\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Notification\Support\NotificationEvents;

class StoreNotificationRecipientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin.notifications.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'channel' => ['required', 'string', Rule::in(['email'])],
            'event' => ['required', 'string', Rule::in(array_keys(NotificationEvents::labels()))],
            'address' => ['required', 'email', 'max:255'],
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
