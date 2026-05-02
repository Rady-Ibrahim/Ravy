<?php

namespace Modules\Auth\Services\Api;

use Illuminate\Validation\ValidationException;
use Modules\Auth\Http\Requests\Api\RegisterRequest;
use Modules\Auth\Http\Requests\Api\ResendVerificationCodeRequest;
use Modules\Auth\Http\Requests\Api\VerifyRequest;
use Modules\Auth\Models\User;

class RegisterService
{
    public function __construct(
        private OtpService $otpService
    ) {}

    /**
     * @return array{token: string, token_type: string, user: User, message: string}
     */
    public function register(RegisterRequest $request): array
    {
        $firstName = trim((string) $request->validated('first_name'));
        $lastName = trim((string) $request->validated('last_name'));
        $generatedFullName = trim($firstName.' '.$lastName);

        $user = User::query()->create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => $request->validated('name') ?: $generatedFullName,
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'phone' => $request->validated('phone'),
            'type' => 'customer',
            'status' => 'active',
        ]);

        $this->otpService->issueForEmail($user->email);

        $token = $user->createToken('api')->plainTextToken;

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->fresh(),
            'message' => __('Registration successful. Verify your email with the code sent to you.'),
        ];
    }

    /**
     * @throws ValidationException
     */
    public function verifyEmail(VerifyRequest $request): User
    {
        $email = $request->validated('email');
        $code = $request->validated('code');

        if (! $this->otpService->verifyEmailCode($email, $code)) {
            throw ValidationException::withMessages([
                'code' => [__('Invalid or expired verification code.')],
            ]);
        }

        $user = User::query()->where('email', $email)->firstOrFail();

        if ($user->email_verified_at !== null) {
            throw ValidationException::withMessages([
                'email' => [__('Email is already verified.')],
            ]);
        }

        $user->forceFill(['email_verified_at' => now()])->save();

        return $user->fresh();
    }

    public function resendVerificationCode(ResendVerificationCodeRequest $request): array
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

        if ($user->email_verified_at !== null) {
            throw ValidationException::withMessages([
                'email' => [__('Email is already verified.')],
            ]);
        }

        $this->otpService->issueForEmail($email);

        return [
            'message' => __('Verification code resent successfully.'),
        ];
    }
}
