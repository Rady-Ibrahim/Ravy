<?php

namespace Modules\Category\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class IndexCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'tree' => ['nullable', 'boolean'],
            'sidebar' => ['nullable', 'boolean'],
            'max_depth' => ['nullable', 'integer', 'min:0', 'max:30'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'include' => ['nullable', 'in:children'],
        ];
    }
}
