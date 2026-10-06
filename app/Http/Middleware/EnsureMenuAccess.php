<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMenuAccess
{
    public function handle(Request $request, Closure $next, string $menu): Response
    {
        $role = $request->user()?->role;
        abort_unless($role, 403);

        if (in_array(strtolower($role->role_name), ['admin', 'administrator'], true)) {
            return $next($request);
        }

        $defaultMenus = ['dashboard', 'templates', 'new_memo', 'memos', 'approvals'];
        $allowedMenus = $role->menu_access ?? $request->user()->menu_access ?? $defaultMenus;
        abort_unless(in_array($menu, $allowedMenus, true), 403);

        return $next($request);
    }
}
