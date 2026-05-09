<?php

namespace Modules\Orders\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class OptionalAuthSanctum
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->bearerToken()) {
            // If token is present, authenticate via Sanctum
            auth()->shouldUse('sanctum');
            
            try {
                $user = auth('sanctum')->user();
                if (!$user) {
                    // Token invalid, but continue as guest
                    auth()->guard('sanctum')->forgetUser();
                }
            } catch (\Exception $e) {
                // Token validation failed, continue as guest
                auth()->guard('sanctum')->forgetUser();
            }
        }
        
        return $next($request);
    }
}
