<?php

namespace Modules\Auth\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ResendVerificationCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
        ];
    }
}
