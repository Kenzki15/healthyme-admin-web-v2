# Security Modifications for ZAP Testing

This document outlines all the security measures that have been temporarily disabled or relaxed for security assessment with OWASP ZAP and other testing tools.

## ⚠️ IMPORTANT WARNING ⚠️
**These changes are ONLY for security testing purposes and should NEVER be deployed to production!**
**Remember to revert all changes after completing the security assessment.**

## Changes Made:

### 1. CSRF Token Verification Disabled
**File:** `app/Http/Middleware/VerifyCsrfToken.php`
- Added wildcard `*` to `$except` array
- Overridden `inExceptArray()` method to always return `true`
- **Impact:** All requests bypass CSRF validation

### 2. CORS Configuration Relaxed
**File:** `config/cors.php`
- Changed `paths` to `['*']` (all paths)
- Added `allowed_origins_patterns` as `['*']`
- Set `exposed_headers` to `['*']`
- Enabled `supports_credentials` to `true`
- **Impact:** Very permissive CORS policy for testing tools

### 3. Two-Factor Authentication Bypassed
**File:** `app/Http/Middleware/EnsureTwoFactorVerified.php`
- Auto-sets `2fa_passed` session variable to `true`
- Commented out original 2FA logic
- **Impact:** Users bypass 2FA requirement automatically

### 4. Session Security Relaxed
**File:** `config/session.php`
- Set `secure` to `false` (allows non-HTTPS)
- Set `http_only` to `false` (allows JavaScript access)
- Set `same_site` to `null` (disables SameSite protection)
- **Impact:** Sessions less secure, more accessible for testing

### 5. HTTP Kernel Middleware Modifications
**File:** `app/Http/Kernel.php`
- Commented out `TrustHosts` middleware
- Commented out `PreventRequestsDuringMaintenance` middleware
- Commented out `ValidatePostSize` middleware
- Commented out `EncryptCookies` in web group
- Commented out `throttle:api` in API group
- **Impact:** Removes various security layers and restrictions

### 6. Cookie Encryption Disabled
**File:** `app/Http/Middleware/EncryptCookies.php`
- Added wildcard `*` to `$except` array
- Overridden `isDisabled()` method to always return `true`
- **Impact:** All cookies are unencrypted and readable

### 7. Environment Configuration Template
**File:** `.env.testing`
- Created template with testing-friendly environment variables
- Includes debug mode, disabled secure cookies, permissive settings
- **Impact:** Easy configuration switching for testing

## Security Testing Routes Available
Your application already has security testing routes in `routes/security-testing.php` which include:
- Unprotected user endpoints
- Vulnerable SQL injection endpoint
- Unprotected todolist endpoints
- File upload vulnerability test
- XSS testing endpoints

## How to Use for ZAP Testing:

1. **Start your application** with the modified configuration
2. **Configure ZAP** to target your application URL
3. **Authenticate once** manually through the normal login process
4. **Export session cookies** from your browser to ZAP if needed
5. **Run ZAP scans** - the relaxed security should allow better coverage

## Post-Testing Cleanup:

After completing your security assessment, you must:

1. **Revert all middleware files** to their original state
2. **Restore original CORS configuration**
3. **Re-enable CSRF protection**
4. **Restore session security settings**
5. **Remove or comment out security testing routes**
6. **Verify all security measures are back in place**

## Files to Revert:
- `app/Http/Middleware/VerifyCsrfToken.php`
- `app/Http/Middleware/EnsureTwoFactorVerified.php`
- `app/Http/Middleware/EncryptCookies.php`
- `app/Http/Kernel.php`
- `config/cors.php`
- `config/session.php`
- `.env` (if you used `.env.testing` values)

## Testing Notes:
- The application now accepts requests without CSRF tokens
- 2FA is automatically bypassed for all users
- Cookies are unencrypted and accessible via JavaScript
- CORS allows requests from any origin
- Rate limiting is disabled
- Various security headers may be missing

Remember to test both authenticated and unauthenticated endpoints to get comprehensive coverage.