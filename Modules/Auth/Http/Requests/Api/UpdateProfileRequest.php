<?php

namespace Modules\Auth\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => [
                'sometimes',
                'nullable',
                'string',
                'max:32',
                Rule::unique('users', 'phone')->ignore($user?->id),
            ],
        ];
    }
}
