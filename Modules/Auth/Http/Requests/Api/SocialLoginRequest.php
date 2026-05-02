<?php

namespace Modules\Auth\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SocialLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'access_token' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'access_token.required' => 'Access token is required.',
            'access_token.string' => 'Access token must be a string.',
        ];
    }
}
