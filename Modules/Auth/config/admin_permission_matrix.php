<?php

/**
 * Permission matrix metadata (web guard). Other modules can add
 * Modules/{Name}/config/admin_permission_matrix.php with the same shape; entries are merged by module key.
 *
 * @see \Modules\Auth\Services\Admin\PermissionMatrixConfigRepository
 */
return [
    'modules' => [
        [
            'key' => 'core',
            'label' => 'Core',
            'groups' => [
                [
                    'key' => 'access',
                    'label' => 'Admin',
                    'permissions' => [
                        ['name' => 'admin.access', 'label' => 'Access dashboard'],
                    ],
                ],
            ],
        ],
        [
            'key' => 'users',
            'label' => 'Users',
            'groups' => [
                [
                    'key' => 'users',
                    'label' => 'Users',
                    'permissions' => [
                        ['name' => 'admin.users.view', 'label' => 'View'],
                        ['name' => 'admin.users.create', 'label' => 'Create'],
                        ['name' => 'admin.users.edit', 'label' => 'Edit'],
                        ['name' => 'admin.users.delete', 'label' => 'Delete'],
                    ],
                ],
            ],
        ],
        [
            'key' => 'roles',
            'label' => 'Roles',
            'groups' => [
                [
                    'key' => 'roles',
                    'label' => 'Roles',
                    'permissions' => [
                        ['name' => 'admin.roles.view', 'label' => 'View'],
                        ['name' => 'admin.roles.create', 'label' => 'Create'],
                        ['name' => 'admin.roles.edit', 'label' => 'Edit'],
                        ['name' => 'admin.roles.delete', 'label' => 'Delete'],
                    ],
                ],
            ],
        ],
        [
            'key' => 'permissions',
            'label' => 'Permissions',
            'groups' => [
                [
                    'key' => 'permissions',
                    'label' => 'Permissions',
                    'permissions' => [
                        ['name' => 'admin.permissions.view', 'label' => 'View'],
                        ['name' => 'admin.permissions.create', 'label' => 'Create'],
                    ],
                ],
            ],
        ],
        [
            'key' => 'matrix',
            'label' => 'Tools',
            'groups' => [
                [
                    'key' => 'matrix',
                    'label' => 'Matrix',
                    'permissions' => [
                        ['name' => 'admin.matrix.manage', 'label' => 'Manage matrix'],
                    ],
                ],
            ],
        ],
    ],
];
