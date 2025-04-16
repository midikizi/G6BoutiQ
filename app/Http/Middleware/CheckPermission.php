<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!auth()->check()) {
            abort(401, 'Unauthorized');
        }

        $userPermissions = auth()->user()->permissions ?? [];

        foreach ($permissions as $permission) {
            if (!in_array($permission, $userPermissions)) {
                abort(403, 'Access Denied');
            }
        }

        return $next($request);
    }
} 