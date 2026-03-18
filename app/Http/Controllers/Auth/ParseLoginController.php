<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Back4AppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ParseLoginController extends Controller
{
    protected $back4AppService;

    public function __construct(Back4AppService $back4AppService)
    {
        $this->back4AppService = $back4AppService;
    }

    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to login with Back4App
        $loginResult = $this->back4AppService->login($request->email, $request->password);

        if (!$loginResult['success']) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Store user data and session token in session
        session([
            'parse_user' => $loginResult['user'],
            'parse_session_token' => $loginResult['sessionToken']
        ]);

        $user = $loginResult['user'];
        
        $request->session()->regenerate();
        
        // Debug logging
        \Log::info('Login successful, checking 2FA status', [
            'user_id' => $user['objectId'] ?? 'unknown'
        ]);
        
        // Fetch fresh user data from Back4App to check 2FA status
        $objectId = $user['objectId'];
        $freshUserData = $this->back4AppService->fetchUserById($objectId);
        
        if ($freshUserData) {
            // Update session with fresh user data
            session(['parse_user' => $freshUserData]);
            $user = $freshUserData;
        } else {
            \Log::error('Failed to fetch fresh user data from Back4App', [
                'user_id' => $objectId
            ]);
        }
        
        // Check if user has a google2fa_secret in Back4App
        $has2FASecret = !empty($user['google2fa_secret']);
        
        \Log::info('2FA secret check results', [
            'user_id' => $objectId,
            'has_2fa_secret' => $has2FASecret
        ]);
        
        if ($has2FASecret) {
            // If user has a secret, go to verify
            \Log::info('User has 2FA secret in Back4App, redirecting to verify');
            return redirect()->route('twofactor.verify.form');
        } else {
            // If no secret exists, redirect to setup
            \Log::info('User does not have 2FA secret in Back4App, redirecting to setup');
            return redirect()->route('twofactor.setup');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Clear Parse session data
        $request->session()->forget(['parse_user', 'parse_session_token']);

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
