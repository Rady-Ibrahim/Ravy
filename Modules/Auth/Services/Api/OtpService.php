<?php

namespace Modules\Auth\Services\Api;

use Illuminate\Support\Facades\Cache;

class OtpService
{
    private const TTL_MINUTES = 15;

    public function issueForEmail(string $email): void
    {
        $code = (string) random_int(100000, 999999);
        Cache::put($this->cacheKey($email), $code, now()->addMinutes(self::TTL_MINUTES));
    }

    public function verifyEmailCode(string $email, string $code): bool
    {
        $key = $this->cacheKey($email);
        $stored = Cache::get($key);

        if ($stored === null || ! hash_equals((string) $stored, $code)) {
            return false;
        }

        Cache::forget($key);

        return true;
    }

    private function cacheKey(string $email): string
    {
        return 'auth:email_otp:'.hash('sha256', strtolower($email));
    }
}
