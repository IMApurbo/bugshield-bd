<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Usage: RoleMiddleware::class.':researcher' | ':admin' | ':organization' (any org) | ':org' (approved org only)
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();
        if (!$user) return redirect()->route('login');

        if ($user->status === 'suspended') {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'This account is suspended.']);
        }

        if ($role === 'org') {
            if ($user->role !== 'organization') abort(403);
            $org = $user->organization;
            if (!$org || $org->status !== 'approved') return redirect()->route('org.pending');
            return $next($request);
        }

        if ($user->role !== $role) abort(403);
        return $next($request);
    }
}
