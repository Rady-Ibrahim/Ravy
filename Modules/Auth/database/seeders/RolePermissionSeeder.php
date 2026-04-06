<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Admin dashboard RBAC (web guard). API / customers do not use these permissions.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'web';

        $permissions = [
            'admin.access',
            'admin.users.view',
            'admin.users.create',
            'admin.users.edit',
            'admin.users.delete',
            'admin.roles.view',
            'admin.roles.create',
            'admin.roles.edit',
            'admin.roles.delete',
            'admin.permissions.view',
            'admin.permissions.create',
            'admin.matrix.manage',
        ];

        foreach ($permissions as $name) {
            Permission::query()->firstOrCreate(
                ['name' => $name, 'guard_name' => $guard]
            );
        }

        $superAdmin = Role::query()->firstOrCreate(
            ['name' => 'super-admin', 'guard_name' => $guard]
        );

        $superAdmin->syncPermissions(Permission::query()->where('guard_name', $guard)->get());
    }
}
