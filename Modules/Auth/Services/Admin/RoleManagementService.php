<?php

namespace Modules\Auth\Services\Admin;

use Modules\Auth\Http\Requests\Admin\StoreRoleRequest;
use Modules\Auth\Http\Requests\Admin\UpdateRoleRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleManagementService
{
    public function store(StoreRoleRequest $request): Role
    {
        $data = $request->validated();
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $role = Role::query()->create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($permissions);

        return $role->load('permissions');
    }

    public function update(UpdateRoleRequest $request, Role $role): Role
    {
        $data = $request->validated();
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        if ($role->name === 'super-admin' && $data['name'] !== 'super-admin') {
            abort(403, __('The super-admin role cannot be renamed.'));
        }

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($permissions);

        if ($role->name === 'super-admin') {
            $role->syncPermissions(
                Permission::query()->where('guard_name', 'web')->get()
            );
        }

        return $role->fresh()->load('permissions');
    }

    public function delete(Role $role): void
    {
        if ($role->name === 'super-admin') {
            abort(403, __('The super-admin role cannot be deleted.'));
        }

        if ($role->users()->count() > 0) {
            abort(403, __('Cannot delete a role that is still assigned to users.'));
        }

        $role->delete();
    }
}
