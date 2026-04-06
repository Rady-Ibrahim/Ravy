<?php

namespace Modules\Auth\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Auth\Http\Requests\Admin\StorePermissionRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:admin.permissions.view')->only(['index']);
        $this->middleware('permission:admin.permissions.create')->only(['create', 'store']);
    }

    public function index(): View|RedirectResponse
    {
        if (auth()->user()?->can('admin.matrix.manage')) {
            return redirect()->route('admin.roles.matrix', ['show' => 'catalog']);
        }

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->paginate(30);

        return view('admin.permissions.index', compact('permissions'));
    }

    public function create(): View|RedirectResponse
    {
        if (auth()->user()?->can('admin.matrix.manage')) {
            return redirect()->route('admin.roles.matrix', array_filter([
                'show' => 'catalog',
                'new' => '1',
            ]));
        }

        return view('admin.permissions.create');
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $permission = Permission::query()->firstOrCreate(
            [
                'name' => $request->validated('name'),
                'guard_name' => 'web',
            ]
        );

        $superAdmin = Role::query()->where('name', 'super-admin')->where('guard_name', 'web')->first();
        if ($superAdmin && $permission->wasRecentlyCreated) {
            $superAdmin->givePermissionTo($permission);
        }

        if ($request->user()?->can('admin.matrix.manage')) {
            return redirect()
                ->route('admin.roles.matrix', ['show' => 'catalog'])
                ->with('status', __('Permission registered successfully.'));
        }

        return redirect()
            ->route('admin.permissions.index')
            ->with('status', __('Permission registered successfully.'));
    }
}
