<?php

namespace Modules\Product\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreCategoryAttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin.products.edit') ?? false;
    }

    public function rules(): array
    {
        $attributeId = $this->route('attribute')?->id;

        return [
            'value' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('category_attribute_values', 'slug')->where(fn ($q) => $q->where('attribute_id', $attributeId)),
            ],
            'extra' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $slugSource = (string) ($this->input('slug') ?: $this->input('value'));
        $this->merge([
            'slug' => Str::slug($slugSource),
        ]);
    }
}
