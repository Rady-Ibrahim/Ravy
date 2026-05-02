<?php

namespace Modules\Auth\Services\Api;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Modules\Auth\Http\Requests\Api\SocialLoginRequest;
use Modules\Auth\Models\User;

class SocialAuthService
{
    public function authenticate(SocialLoginRequest $request, string $provider): array
    {
        $validated = $request->validated();
        $accessToken = $validated['access_token'];

        // Validate provider
        if (!in_array($provider, ['google', 'facebook'])) {
            throw new \InvalidArgumentException('Unsupported provider');
        }

        try {
            // Get user from social provider using access token
            $socialUser = $this->getSocialUser($provider, $accessToken);
            
            // Find or create user
            $user = $this->findOrCreateUser($socialUser, $provider);

            // Create Sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ];
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid access token or provider error: ' . $e->getMessage());
        }
    }

    private function getSocialUser(string $provider, string $accessToken)
    {
        $socialite = Socialite::driver($provider)->stateless();
        
        // For Google, we need to use the token from the frontend
        if ($provider === 'google') {
            return $socialite->userFromToken($accessToken);
        }
        
        // For Facebook
        if ($provider === 'facebook') {
            return $socialite->userFromToken($accessToken);
        }

        throw new \InvalidArgumentException('Unsupported provider');
    }

    private function findOrCreateUser($socialUser, string $provider): User
    {
        // First, check if user exists with this social ID
        $existingUser = User::where('social_id', $socialUser->getId())
            ->where('social_type', $provider)
            ->first();

        if ($existingUser) {
            return $existingUser;
        }

        // Check if user exists with same email
        $userByEmail = User::where('email', $socialUser->getEmail())->first();

        if ($userByEmail) {
            // Link social account to existing user
            $userByEmail->update([
                'social_id' => $socialUser->getId(),
                'social_type' => $provider,
            ]);
            return $userByEmail;
        }

        // Create new user
        $name = $socialUser->getName() ?? explode(' ', $socialUser->getEmail())[0];
        $firstName = $socialUser->user['given_name'] ?? explode(' ', $name)[0] ?? null;
        $lastName = $socialUser->user['family_name'] ?? explode(' ', $name)[1] ?? null;

        return User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => $name,
            'email' => $socialUser->getEmail(),
            'social_id' => $socialUser->getId(),
            'social_type' => $provider,
            'type' => 'customer',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
