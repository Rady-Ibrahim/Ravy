<?php

namespace Modules\Auth\Services\Api;

use Illuminate\Validation\ValidationException;
use Modules\Auth\Http\Requests\Api\ForgotPasswordRequest;
use Modules\Auth\Http\Requests\Api\ResetPasswordRequest;
use Modules\Auth\Models\User;

class PasswordResetService
{
    public function __construct(
        private OtpService $otpService
    ) {}

    public function requestCode(ForgotPasswordRequest $request): array
    {
        $email = strtolower((string) $request->validated('email'));

        $user = User::query()
            ->where('email', $email)
            ->where('type', '!=', 'admin')
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => [__('User not found.')],
            ]);
        }

        $this->otpService->issueForEmail($email, 'password_reset');

        return [
            'message' => __('Password reset code sent successfully.'),
        ];
    }

    public function reset(ResetPasswordRequest $request): array
    {
        $email = strtolower((string) $request->validated('email'));
        $code = $request->validated('code');

        if (! $this->otpService->verifyEmailCode($email, $code, 'password_reset')) {
            throw ValidationException::withMessages([
                'code' => [__('Invalid or expired verification code.')],
            ]);
        }

        $user = User::query()
            ->where('email', $email)
            ->where('type', '!=', 'admin')
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => [__('User not found.')],
            ]);
        }

        $user->forceFill([
            'password' => $request->validated('password'),
        ])->save();

        return [
            'message' => __('Password reset successfully.'),
        ];
    }
}
