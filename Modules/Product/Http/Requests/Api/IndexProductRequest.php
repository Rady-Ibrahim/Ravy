<?php

namespace Modules\Product\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class IndexProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => ['nullable', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'is_new' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'featured' => ['nullable', 'boolean'],
            'color' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
            'material' => ['nullable', 'string', 'max:255'],
            'search' => ['nullable', 'string', 'max:255'],
            'q' => ['nullable', 'string', 'max:255'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', 'in:latest,price_asc,price_desc,best_seller,trending'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
