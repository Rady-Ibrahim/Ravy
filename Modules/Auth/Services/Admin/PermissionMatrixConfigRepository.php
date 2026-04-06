<?php

namespace Modules\Auth\Services\Admin;

class PermissionMatrixConfigRepository
{
    /**
     * @return array<int, array{key: string, label: string, groups: array<int, mixed>}>
     */
    public function modules(): array
    {
        $paths = glob(base_path('Modules/*/config/admin_permission_matrix.php')) ?: [];
        $byKey = [];

        foreach ($paths as $path) {
            $data = require $path;
            foreach ($data['modules'] ?? [] as $module) {
                $key = $module['key'] ?? null;
                if (is_string($key) && $key !== '' && ! isset($byKey[$key])) {
                    $byKey[$key] = $module;
                }
            }
        }

        if ($byKey === []) {
            return config('admin_permission_matrix.modules', []);
        }

        return array_values($byKey);
    }
}
