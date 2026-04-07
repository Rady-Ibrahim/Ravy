<?php

namespace Modules\Auth\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Auth\Http\Requests\Admin\StoreRoleRequest;
use Modules\Auth\Http\Requests\Admin\UpdateRoleRequest;
use Modules\Auth\Services\Admin\RoleManagementService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends AdminController
{
    public function __construct(
        private RoleManagementService $roles
    ) {
        parent::__construct();
        $this->middleware('permission:admin.roles.view')->only(['index']);
        $this->middleware('permission:admin.roles.create')->only(['create', 'store']);
        $this->middleware('permission:admin.roles.edit')->only(['edit', 'update']);
        $this->middleware('permission:admin.roles.delete')->only(['destroy']);
    }

    public function index(): View
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->withCount('users')
            ->with('permissions')
            ->orderBy('name')
            ->get();

        return view('auth::admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::query()->where('guard_name', 'web')->orderBy('name')->get();

        return view('auth::admin.roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->roles->store($request);

        return redirect()
            ->route('admin.roles.index')
            ->with('status', __('Role created successfully.'));
    }

    public function edit(Role $role): View
    {
        abort_unless($role->guard_name === 'web', 404);

        $permissions = Permission::query()->where('guard_name', 'web')->orderBy('name')->get();
        $role->load('permissions');

        return view('auth::admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        abort_unless($role->guard_name === 'web', 404);

        $this->roles->update($request, $role);

        return redirect()
            ->route('admin.roles.index')
            ->with('status', __('Role updated successfully.'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_unless($role->guard_name === 'web', 404);

        $this->roles->delete($role);

        return redirect()
            ->route('admin.roles.index')
            ->with('status', __('Role deleted successfully.'));
    }
}
