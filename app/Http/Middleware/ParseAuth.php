<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Back4AppService;

class ParseAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('parse_user') || !session()->has('parse_session_token')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        // Validate session token is still valid
        $back4AppService = app(Back4AppService::class);
        $sessionValidation = $back4AppService->validateSession(session('parse_session_token'));
        
        if (!$sessionValidation['success']) {
            // Clear invalid session
            session()->forget(['parse_user', 'parse_session_token']);
            
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Session expired.'], 401);
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
}
