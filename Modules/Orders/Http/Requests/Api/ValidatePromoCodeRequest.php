<?php

namespace Modules\Orders\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ValidatePromoCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'promo_code' => ['required', 'string', 'max:50'],
            'subtotal' => ['required', 'numeric', 'min:0'],
        ];
    }
}
