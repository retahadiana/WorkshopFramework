<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSessionUser
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Allow if user is authenticated via auth() or session('user') exists
        if ($request->user() || session('user')) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
