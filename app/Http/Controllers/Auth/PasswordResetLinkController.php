<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use GuzzleHttp\Client;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $client = new Client();

            $response = $client->post(env('PARSE_SERVER_URL') . '/parse/requestPasswordReset', [
                'headers' => [
                    'X-Parse-Application-Id' => env('PARSE_APP_ID'),
                    'X-Parse-REST-API-Key' => env('PARSE_REST_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => ['email' => $request->input('email')],
                'verify' => false, // ✅ Disables SSL cert verification (use with caution)
            ]);

            return back()->with('status', 'Password reset link sent! Please check your email.');
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'email' => ['Password reset failed: ' . $e->getMessage()],
            ]);
        }
    }
}
