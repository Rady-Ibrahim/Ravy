<?php

namespace Modules\Product\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin.products.edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:255', 'unique:variants,sku'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'attribute_value_ids' => ['required', 'array', 'min:1'],
            'attribute_value_ids.*' => ['integer', 'exists:category_attribute_values,id'],
        ];
    }
}
