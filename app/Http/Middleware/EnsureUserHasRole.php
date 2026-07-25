<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Allow the request only when the authenticated user holds one of the
     * given roles. Usage on a route: `->middleware('role:lecturer,admin')`.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null || ! in_array($user->role, array_map(UserRole::from(...), $roles), true)) {
            abort(403);
        }

        return $next($request);
    }
}
