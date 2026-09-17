<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckRole Middleware — enforces role-based access control on routes.
 *
 * Registered in bootstrap/app.php as middleware alias 'role'.
 * Usage in routes: ->middleware('role:Avocat,Administrateur')
 *
 * Behaviour:
 *   - Unauthenticated users are redirected to the login page.
 *   - When no roles are specified, any authenticated user is allowed.
 *   - When roles are specified, the user must have at least one of them
 *     (delegates to User::hasRole()).
 *   - Users without the required role receive a 403 Forbidden response.
 *
 * Similar: User::hasRole() — this middleware calls that method to decide access.
 */
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    /**
     * Processes the incoming request and enforces role-based access.
     *
     * @param  Request                          $request  The current HTTP request.
     * @param  Closure(Request): (Response)     $next     The next middleware or controller.
     * @param  string                           ...$roles Zero or more role names passed from the route definition
     *                                                    e.g. middleware('role:Avocat,Administrateur').
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        if (empty($roles)) {
            return $next($request);
        }

        if (! $user->hasRole($roles)) {
            abort(403, 'Accès non autorisé : privilèges insuffisants.');
        }

        return $next($request);
    }
}
