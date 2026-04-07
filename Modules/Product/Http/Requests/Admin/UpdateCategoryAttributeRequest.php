<?php

namespace Modules\Product\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin.products.edit') ?? false;
    }

    public function rules(): array
    {
        $attributeId = $this->route('attribute')?->id;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255', Rule::unique('category_attributes', 'code')->ignore($attributeId)],
            'type' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_filterable' => ['sometimes', 'boolean'],
        ];
    }
}
