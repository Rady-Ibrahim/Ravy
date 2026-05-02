<?php

namespace Modules\Auth\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Http\Requests\Api\SocialLoginRequest;
use Modules\Auth\Http\Resources\UserResource;
use Modules\Auth\Services\Api\SocialAuthService;

class SocialAuthController extends Controller
{
    public function login(SocialLoginRequest $request, SocialAuthService $socialAuthService, string $provider): JsonResponse
    {
        $payload = $socialAuthService->authenticate($request, $provider);
        $payload['user'] = UserResource::make($payload['user']);

        return response()->json($payload);
    }
}
