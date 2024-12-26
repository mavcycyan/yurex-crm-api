<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!in_array($request->user()->role->name, $roles)) {
            return response()->json(['message' => 'Access Denied'], 403);
        }

        return $next($request);
    }
}
