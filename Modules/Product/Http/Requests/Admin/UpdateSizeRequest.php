<?php

namespace Modules\Product\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSizeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sizeId = $this->route('size')?->id;

        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:sizes,code,' . $sizeId,
            'code_from' => 'nullable|string|max:50',
            'code_to' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }
}
