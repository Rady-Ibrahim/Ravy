<?php

namespace Modules\Auth\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Auth\Http\Requests\Admin\UpdatePermissionMatrixRequest;
use Modules\Auth\Services\Admin\PermissionMatrixService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionMatrixController extends AdminController
{
    public function __construct(
        private PermissionMatrixService $matrix
    ) {
        parent::__construct();
        $this->middleware('permission:admin.matrix.manage')->only(['index', 'update']);
    }

    public function index(Request $request): View
    {
        $modules = $this->matrix->modulesForView();

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->with('permissions')
            ->orderBy('name')
            ->get();

        $catalogPermissions = null;
        if ($request->user()?->can('admin.permissions.view')) {
            $catalogPermissions = Permission::query()
                ->where('guard_name', 'web')
                ->orderBy('name')
                ->paginate(30)
                ->withQueryString();
        }

        $catalogPanelOpen = $request->user()?->can('admin.permissions.view')
            && $request->query('show') === 'catalog';

        return view('admin.roles.matrix', [
            'modules' => $modules,
            'roles' => $roles,
            'catalogPermissions' => $catalogPermissions,
            'catalogPanelOpen' => $catalogPanelOpen,
        ]);
    }

    public function update(UpdatePermissionMatrixRequest $request): RedirectResponse
    {
        $this->matrix->sync($request->validated('roles_permissions'));

        return redirect()
            ->route('admin.roles.matrix')
            ->with('status', __('Permission matrix saved successfully.'));
    }
}
