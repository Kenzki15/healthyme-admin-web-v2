<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthenticationController extends Controller
{
    public function show()
    {
        return Inertia::render('TwoFactorAuthentication/Show', [
            'qrSvg' => $this->generateQrCode(),
        ]);
    }

    public function setup(Request $request)
    {
        $user = session('parse_user');
        if (!$user) {
            \Log::info('Setup called but no user in session, redirecting to login');
            return redirect()->route('login');
        }
        
        \Log::info('2FA setup called', [
            'user_id' => $user['objectId'] ?? 'unknown'
        ]);
        
        // Fetch fresh user data from Back4App to check 2FA status
        $objectId = $user['objectId'];
        $freshUserData = app(\App\Services\Back4AppService::class)->fetchUserById($objectId);
        
        if (!$freshUserData) {
            \Log::error('Failed to fetch user data from Back4App', ['objectId' => $objectId]);
            return redirect()->route('login');
        }
        
        // Check if user already has a google2fa_secret (note: using google2fa_secret as field name per Back4App)
        if (!empty($freshUserData['google2fa_secret'])) {
            \Log::info('User already has 2FA secret in Back4App, redirecting to verify');
            // Update session with fresh user data
            session(['parse_user' => $freshUserData]);
            return redirect()->route('twofactor.verify.form');
        }

        \Log::info('User does not have 2FA secret, generating new one');
        
        // Generate Google2FA secret if not present
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        // Save secret to Back4App
        $objectId = $user['objectId'];
        app(\App\Services\Back4AppService::class)->updateUser($objectId, [
            'google2fa_secret' => $secret
        ]);

        // Generate QR code
        $company = 'HealthyMe';
        $email = $user['email'] ?? $user['username'];
        $qrData = $google2fa->getQRCodeUrl($company, $email, $secret);

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrSvg = $writer->writeString($qrData);

        // Update session user with secret
        $user['google2fa_secret'] = $secret;
        session(['parse_user' => $user]);

        \Log::info('2FA setup complete, showing QR code');

        return Inertia::render('TwoFactorAuthentication/Show', [
            'qrSvg' => $qrSvg,
            'secret' => $secret
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);
        
        $user = session('parse_user');
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Fetch fresh user data from Back4App to check 2FA status
        $objectId = $user['objectId'];
        $freshUserData = app(\App\Services\Back4AppService::class)->fetchUserById($objectId);
        
        if (!$freshUserData) {
            \Log::error('Failed to fetch user data from Back4App', ['objectId' => $objectId]);
            return redirect()->route('login');
        }
        
        // Update session with fresh user data
        session(['parse_user' => $freshUserData]);
        
        // If user doesn't have a 2FA secret in Back4App, redirect to setup
        if (empty($freshUserData['google2fa_secret'])) {
            \Log::info('User does not have 2FA secret in Back4App, redirecting to setup');
            return redirect()->route('twofactor.setup');
        }
        
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($freshUserData['google2fa_secret'], $request->input('code'));
        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid authentication code.']);
        }
        
        // Set a session flag to mark 2FA as passed
        session(['2fa_passed' => true]);

        // Remember device logic
        if ($request->boolean('remember_device')) {
            // Set a cookie for 30 days to remember the device
            return redirect()->intended('/dashboard')
                ->withCookie(cookie('2fa_remember', true, 60 * 24 * 30)); // 30 days
        }

        return redirect()->intended('/dashboard');
    }

    public function showVerifyForm(Request $request)
    {
        $user = session('parse_user');
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Fetch fresh user data from Back4App to check 2FA status
        $objectId = $user['objectId'];
        $freshUserData = app(\App\Services\Back4AppService::class)->fetchUserById($objectId);
        
        if (!$freshUserData) {
            \Log::error('Failed to fetch user data from Back4App', ['objectId' => $objectId]);
            return redirect()->route('login');
        }
        
        // Update session with fresh user data
        session(['parse_user' => $freshUserData]);
        
        // If user doesn't have a 2FA secret in Back4App, redirect to setup
        if (empty($freshUserData['google2fa_secret'])) {
            \Log::info('User does not have 2FA secret in Back4App, redirecting to setup');
            return redirect()->route('twofactor.setup');
        }
        
        // If the remember device cookie is set, skip 2FA prompt
        if ($request->cookie('2fa_remember')) {
            session(['2fa_passed' => true]);
            return redirect()->intended('/dashboard');
        }
        
        return Inertia::render('Auth/TwoFactorVerify');
    }

    // Add this method to return the QR code as JSON for redisplay
    public function qr(Request $request)
    {
        $user = session('parse_user');
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        // Fetch fresh user data from Back4App to check 2FA status
        $objectId = $user['objectId'];
        $freshUserData = app(\App\Services\Back4AppService::class)->fetchUserById($objectId);
        
        if (!$freshUserData || empty($freshUserData['google2fa_secret'])) {
            return response()->json(['error' => '2FA not set up'], 400);
        }
        
        // Update session with fresh user data
        session(['parse_user' => $freshUserData]);
        
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $company = 'HealthyMe';
        $email = $freshUserData['email'] ?? $freshUserData['username'];
        $qrData = $google2fa->getQRCodeUrl($company, $email, $freshUserData['google2fa_secret']);

        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $qrSvg = $writer->writeString($qrData);

        return response()->json([
            'qrSvg' => $qrSvg,
            'secret' => $freshUserData['google2fa_secret']
        ]);
    }
}
