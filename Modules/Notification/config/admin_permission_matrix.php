<?php

return [
    'modules' => [
        [
            'key' => 'notifications',
            'label' => 'Notifications',
            'groups' => [
                [
                    'key' => 'notifications',
                    'label' => 'Recipients',
                    'permissions' => [
                        ['name' => 'admin.notifications.view', 'label' => 'View'],
                        ['name' => 'admin.notifications.create', 'label' => 'Create'],
                        ['name' => 'admin.notifications.edit', 'label' => 'Edit'],
                        ['name' => 'admin.notifications.delete', 'label' => 'Delete'],
                    ],
                ],
            ],
        ],
    ],
];
