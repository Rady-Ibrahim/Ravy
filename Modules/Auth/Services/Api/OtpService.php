<?php

namespace Modules\Auth\Services\Api;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Modules\Auth\Mail\OtpCodeMail;

class OtpService
{
    private const TTL_MINUTES = 15;

    public function issueForEmail(string $email, string $purpose = 'verify'): void
    {
        $code = (string) random_int(100000, 999999);
        Cache::put($this->cacheKey($email, $purpose), $code, now()->addMinutes(self::TTL_MINUTES));
        Mail::to($email)->send(new OtpCodeMail($code, $purpose));
    }

    public function verifyEmailCode(string $email, string $code, string $purpose = 'verify'): bool
    {
        $key = $this->cacheKey($email, $purpose);
        $stored = Cache::get($key);

        if ($stored === null || ! hash_equals((string) $stored, $code)) {
            return false;
        }

        Cache::forget($key);

        return true;
    }

    private function cacheKey(string $email, string $purpose): string
    {
        return 'auth:email_otp:'.$purpose.':'.hash('sha256', strtolower($email));
    }
}
