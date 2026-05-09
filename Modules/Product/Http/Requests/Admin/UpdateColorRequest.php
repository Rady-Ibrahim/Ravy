<?php

namespace Modules\Product\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateColorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $colorId = $this->route('color')?->id;
        
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:colors,code,' . $colorId,
            'hex' => 'nullable|string|max:7',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }
}
