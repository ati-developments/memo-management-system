<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAssignedApprover
{
    public function handle(Request $request, Closure $next): Response
    {
        $approval = $request->route('approval');

        if ((int) $approval->approver_id !== (int) $request->user()->id) {
            return redirect()->guest(route('login'))
                ->withErrors(['username' => 'Please sign in with the account assigned to review this memo.']);
        }

        return $next($request);
    }
}
