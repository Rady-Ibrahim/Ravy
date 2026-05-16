<?php

namespace Modules\Notification\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin.notifications.edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'order_notification_email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
