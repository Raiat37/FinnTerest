<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    /**
     * Handle an incoming request.
     * Expects the middleware parameter to be the required role, e.g. ensure.role:admin
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $user = $request->user();
        if (! $user || ($user->role ?? null) !== $role) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
