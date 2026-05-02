<?php

namespace Modules\Category\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Category\Models\Category;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin.categories.edit') ?? false;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'parent_id' => ['nullable', 'exists:categories,id', Rule::notIn([$categoryId])],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'banner' => ['nullable', 'image', 'max:4096'],
            'icon' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
            'show_in_sidebar' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],
            'menu_order' => ['sometimes', 'integer'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $category = $this->route('category');
            $parentId = $this->input('parent_id');

            if (! $category || ! $parentId) {
                return;
            }

            $descendantIds = Category::query()
                ->where('path', 'like', $category->path.'/%')
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            if (in_array((int) $parentId, $descendantIds, true)) {
                $validator->errors()->add('parent_id', __('Invalid parent category selection.'));
            }
        });
    }
}
