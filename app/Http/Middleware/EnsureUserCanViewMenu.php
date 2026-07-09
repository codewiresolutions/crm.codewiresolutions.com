<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanViewMenu
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $menuKey): Response
    {
        if (! $request->user() || ! $request->user()->canViewMenu($menuKey)) {
            abort(403, 'You are not authorized to access this page.');
        }

        return $next($request);
    }
}