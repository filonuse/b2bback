<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;

class LogoutIfBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (\Auth::user()->banned) {
            \JWTAuth::invalidate();

            return response()->json(['message' => 'User is blocked.', 'errors' => UserStatus::BANNED], 403);
        }

        return $next($request);
    }
}
