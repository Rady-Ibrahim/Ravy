<?php

namespace Modules\Auth\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UpdatePermissionMatrixRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin.matrix.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $editableRoleIds = Role::query()
            ->where('guard_name', 'web')
            ->where('name', '!=', 'super-admin')
            ->pluck('id')
            ->map(fn ($id) => (string) $id);

        $incoming = $this->input('roles_permissions', []);
        $normalized = [];

        foreach ($editableRoleIds as $id) {
            $list = [];

            if (array_key_exists($id, $incoming) && is_array($incoming[$id])) {
                $list = $incoming[$id];
            } elseif (array_key_exists((int) $id, $incoming) && is_array($incoming[(int) $id])) {
                $list = $incoming[(int) $id];
            }

            $normalized[$id] = array_values(array_filter($list, fn ($v) => is_string($v) && $v !== ''));
        }

        $this->merge(['roles_permissions' => $normalized]);
    }

    public function rules(): array
    {
        return [
            'roles_permissions' => ['present', 'array'],
            'roles_permissions.*' => ['array'],
            'roles_permissions.*.*' => [
                'string',
                Rule::exists('permissions', 'name')->where('guard_name', 'web'),
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $payload = $this->input('roles_permissions', []);

            foreach (array_keys($payload) as $roleId) {
                $role = Role::query()->where('guard_name', 'web')->whereKey($roleId)->first();
                if (! $role) {
                    $validator->errors()->add('roles_permissions', __('Invalid role selected.'));

                    return;
                }
                if ($role->name === 'super-admin') {
                    $validator->errors()->add('roles_permissions', __('The super-admin role cannot be edited from the matrix form.'));

                    return;
                }
            }
        });
    }
}
