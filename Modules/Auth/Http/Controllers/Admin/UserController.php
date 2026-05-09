<?php

namespace Modules\Auth\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Auth\Http\Requests\Admin\StoreUserRequest;
use Modules\Auth\Http\Requests\Admin\UpdateUserRequest;
use Modules\Auth\Models\User;
use Modules\Auth\Services\Admin\UserManagementService;
use Spatie\Permission\Models\Role;

class UserController extends AdminController
{
    public function __construct(
        private UserManagementService $users
    ) {
        parent::__construct();
        $this->middleware('permission:admin.users.view')->only(['index']);
        $this->middleware('permission:admin.users.create')->only(['create', 'store']);
        $this->middleware('permission:admin.users.edit')->only(['edit', 'update']);
        $this->middleware('permission:admin.users.delete')->only(['destroy']);
    }

    public function index(): View
    {
        $users = User::query()
            ->isAdmin()
            ->with('roles')
            ->latest()
            ->paginate(15);

        return view('auth::admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::query()->where('guard_name', 'web')->orderBy('name')->get();

        return view('auth::admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->users->store($request);

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('User created successfully.'));
    }

    public function edit(User $user): View
    {
        $roles = Role::query()->where('guard_name', 'web')->orderBy('name')->get();

        return view('auth::admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->users->update($request, $user);

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('User updated successfully.'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->users->delete($user, $request->user());

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('User deleted successfully.'));
    }
}
