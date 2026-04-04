<?php

namespace Modules\Auth\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Http\Requests\Api\LoginRequest;
use Modules\Auth\Http\Requests\Api\RegisterRequest;
use Modules\Auth\Http\Requests\Api\VerifyRequest;
use Modules\Auth\Services\Api\LoginService;
use Modules\Auth\Services\Api\LogoutService;
use Modules\Auth\Services\Api\RegisterService;

class AuthController extends Controller
{
    public function login(LoginRequest $request, LoginService $loginService): JsonResponse
    {
        return response()->json($loginService->login($request));
    }

    public function register(RegisterRequest $request, RegisterService $registerService): JsonResponse
    {
        return response()->json($registerService->register($request), 201);
    }

    public function verify(VerifyRequest $request, RegisterService $registerService): JsonResponse
    {
        $user = $registerService->verifyEmail($request);

        return response()->json([
            'message' => __('Email verified successfully.'),
            'user' => $user,
        ]);
    }

    public function logout(Request $request, LogoutService $logoutService): JsonResponse
    {
        $logoutService->logout($request);

        return response()->json(['message' => __('Logged out successfully.')]);
    }

    public function profile(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
