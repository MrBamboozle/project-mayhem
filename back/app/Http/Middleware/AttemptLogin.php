<?php

namespace App\Http\Middleware;

use App\Enums\TokenAbility;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AttemptLogin
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('sanctum')->user();

        if ($user !== null && $user->currentAccessToken()->can(TokenAbility::ACCESS_API->value)) {
            Auth::login($user);
        }

        return $next($request);
    }
}
