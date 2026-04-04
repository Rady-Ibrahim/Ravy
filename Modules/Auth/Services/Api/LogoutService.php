<?php

namespace Modules\Auth\Services\Api;

use Illuminate\Http\Request;

class LogoutService
{
    public function logout(Request $request): void
    {
        $request->user()->currentAccessToken()->delete();
    }
}
