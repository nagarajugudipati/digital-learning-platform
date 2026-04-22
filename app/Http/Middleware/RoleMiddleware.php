<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $user = auth()->user();

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated. Please contact admin.');
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'You do not have permission to access this page.');
        }

        // Extra check: teacher must be approved to access teacher panel
        if ($user->isTeacher() && !$user->isApprovedTeacher()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your teacher account is pending admin approval.');
        }

        return $next($request);
    }
}
