<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(in_array(strtolower((string) $request->user()?->role?->role_name), ['admin', 'administrator'], true), 403);

        return $next($request);
    }
}
