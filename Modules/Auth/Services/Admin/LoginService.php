<?php

namespace Modules\Auth\Services\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Modules\Auth\Http\Requests\Admin\AdminLoginRequest;
use Modules\Auth\Models\User;

class LoginService
{
    public function login(AdminLoginRequest $request): User
    {
        $user = User::query()->where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        if ($user->type !== 'admin') {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => [__('Account is not active.')],
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        return $user;
    }
}
