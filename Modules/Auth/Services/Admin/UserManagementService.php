<?php

namespace Modules\Auth\Services\Admin;

use Modules\Auth\Http\Requests\Admin\StoreUserRequest;
use Modules\Auth\Http\Requests\Admin\UpdateUserRequest;
use Modules\Auth\Models\User;

class UserManagementService
{
    public function store(StoreUserRequest $request): User
    {
        $data = $request->validated();
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $user = User::query()->create($data);
        $this->syncRolesForUser($user, $roles);

        return $user->fresh();
    }

    public function update(UpdateUserRequest $request, User $user): User
    {
        $data = $request->validated();
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        $this->syncRolesForUser($user, $roles);

        return $user->fresh();
    }

    public function delete(User $user, User $actor): void
    {
        if ($user->id === $actor->id) {
            abort(403, __('You cannot delete your own account.'));
        }

        $user->delete();
    }

    /**
     * @param  list<string>  $roleNames
     */
    private function syncRolesForUser(User $user, array $roleNames): void
    {
        if ($user->type === 'admin') {
            $user->syncRoles($roleNames);
        } else {
            $user->syncRoles([]);
        }
    }
}
