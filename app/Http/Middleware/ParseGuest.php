<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ParseGuest
{
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        if (session()->has('parse_user') && session()->has('parse_session_token')) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
