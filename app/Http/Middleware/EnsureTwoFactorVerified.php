<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class EnsureTwoFactorVerified
{
    public function handle(Request $request, Closure $next)
    {
        // If user is logged in but 2FA not passed, redirect appropriately
        if (session()->has('parse_user') && !session('2fa_passed')) {
            $user = session('parse_user');
            
            // Check if user has a 2FA secret
            if (empty($user['google2fa_secret'])) {
                // No secret, redirect to setup (but prevent loop)
                if (!$request->is('two-factor-setup')) {
                    return redirect()->route('twofactor.setup');
                }
            } else {
                // Has secret, redirect to verify (but prevent loop)
                if (!$request->is('two-factor-verify')) {
                    return redirect()->route('twofactor.verify.form');
                }
            }
        }
        return $next($request);
    }
}
