<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticatedApi
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // Token based → check user via sanctum
        if ($request->user()) {
            return redirect('/dashboard');
        }
        return $next($request);
    }
}
