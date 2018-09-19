<?php

namespace App\Http\Middleware;

use App\Enums\RoleType;
use Closure;

class CheckAdmin
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
        if (\Auth::user()->hasRole(RoleType::ADMIN)) {
            return $next($request);
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }
}
