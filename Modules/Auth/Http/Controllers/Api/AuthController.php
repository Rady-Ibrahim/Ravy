<?php

namespace Modules\Auth\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Http\Requests\Api\ForgotPasswordRequest;
use Modules\Auth\Http\Requests\Api\LoginRequest;
use Modules\Auth\Http\Requests\Api\ResendVerificationCodeRequest;
use Modules\Auth\Http\Requests\Api\RegisterRequest;
use Modules\Auth\Http\Requests\Api\ResetPasswordRequest;
use Modules\Auth\Http\Requests\Api\UpdateProfileRequest;
use Modules\Auth\Http\Requests\Api\VerifyRequest;
use Modules\Auth\Http\Resources\UserResource;
use Modules\Auth\Services\Api\LoginService;
use Modules\Auth\Services\Api\LogoutService;
use Modules\Auth\Services\Api\PasswordResetService;
use Modules\Auth\Services\Api\RegisterService;

class AuthController extends Controller
{
    public function login(LoginRequest $request, LoginService $loginService): JsonResponse
    {
        $payload = $loginService->login($request);
        $payload['user'] = UserResource::make($payload['user']);

        return response()->json($payload);
    }

    public function register(RegisterRequest $request, RegisterService $registerService): JsonResponse
    {
        $payload = $registerService->register($request);
        $payload['user'] = UserResource::make($payload['user']);

        return response()->json($payload, 201);
    }

    public function verify(VerifyRequest $request, RegisterService $registerService): JsonResponse
    {
        $user = $registerService->verifyEmail($request);

        return response()->json([
            'message' => __('Email verified successfully.'),
            'user' => UserResource::make($user),
        ]);
    }

    public function resendVerificationCode(ResendVerificationCodeRequest $request, RegisterService $registerService): JsonResponse
    {
        return response()->json($registerService->resendVerificationCode($request));
    }

    public function logout(Request $request, LogoutService $logoutService): JsonResponse
    {
        $logoutService->logout($request);

        return response()->json(['message' => __('Logged out successfully.')]);
    }

    public function forgotPassword(ForgotPasswordRequest $request, PasswordResetService $passwordResetService): JsonResponse
    {
        return response()->json($passwordResetService->requestCode($request));
    }

    public function resetPassword(ResetPasswordRequest $request, PasswordResetService $passwordResetService): JsonResponse
    {
        return response()->json($passwordResetService->reset($request));
    }

    public function profile(Request $request): JsonResponse
    {
        return response()->json(UserResource::make($request->user()));
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if (array_key_exists('first_name', $validated)) {
            $user->first_name = $validated['first_name'];
        }

        if (array_key_exists('last_name', $validated)) {
            $user->last_name = $validated['last_name'];
        }

        if (array_key_exists('phone', $validated)) {
            $user->phone = $validated['phone'];
        }

        if (array_key_exists('first_name', $validated) || array_key_exists('last_name', $validated)) {
            $firstName = (string) ($user->first_name ?? '');
            $lastName = (string) ($user->last_name ?? '');
            $user->name = trim($firstName.' '.$lastName);
        }

        $user->save();

        return response()->json([
            'message' => __('Profile updated successfully.'),
            'user' => UserResource::make($user->fresh()),
        ]);
    }
}
