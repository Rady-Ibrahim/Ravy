<?php

namespace Modules\Auth\Services\Admin;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionMatrixService
{
    public function __construct(
        private PermissionMatrixConfigRepository $configRepository
    ) {}

    /**
     * @return array<int, array{key: string, label: string, groups: array<int, mixed>}>
     */
    public function modulesForView(): array
    {
        $modules = $this->configRepository->modules();
        $defined = $this->flatPermissionNames($modules);

        $dbNames = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $orphans = array_values(array_diff($dbNames, $defined));

        if ($orphans !== []) {
            $modules[] = [
                'key' => 'other',
                'label' => 'Other',
                'groups' => [
                    [
                        'key' => 'other',
                        'label' => 'Not grouped in matrix config',
                        'permissions' => array_map(
                            fn (string $name) => ['name' => $name, 'label' => $name],
                            $orphans
                        ),
                    ],
                ],
            ];
        }

        return $modules;
    }

    /**
     * @param  array<int|string, array<int, string>>  $rolesPermissions  role id => permission names
     */
    public function sync(array $rolesPermissions): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'web';

        foreach ($rolesPermissions as $roleId => $permissions) {
            $role = Role::query()
                ->where('guard_name', $guard)
                ->whereKey($roleId)
                ->first();

            if (! $role || $role->name === 'super-admin') {
                continue;
            }

            $role->syncPermissions($permissions);
        }

        $superAdmin = Role::query()
            ->where('guard_name', $guard)
            ->where('name', 'super-admin')
            ->first();

        if ($superAdmin) {
            $superAdmin->syncPermissions(
                Permission::query()->where('guard_name', $guard)->get()
            );
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $modules
     * @return array<int, string>
     */
    public function flatPermissionNames(array $modules): array
    {
        $names = [];

        foreach ($modules as $module) {
            foreach ($module['groups'] ?? [] as $group) {
                foreach ($group['permissions'] ?? [] as $perm) {
                    if (! empty($perm['name'])) {
                        $names[] = $perm['name'];
                    }
                }
            }
        }

        return array_values(array_unique($names));
    }
}
