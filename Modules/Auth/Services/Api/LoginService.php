<?php

namespace Modules\Auth\Services\Api;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Modules\Auth\Http\Requests\Api\LoginRequest;
use Modules\Auth\Models\User;

class LoginService
{
    /**
     * @return array{token: string, token_type: string, user: User}
     */
    public function login(LoginRequest $request): array
    {
        $user = User::query()->where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        if ($user->type === 'admin') {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => [__('Account is not active.')],
            ]);
        }

        $token = $user->createToken('api')->plainTextToken;

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ];
    }
}
