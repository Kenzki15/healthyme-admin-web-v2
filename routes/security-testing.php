<?php

/*
|--------------------------------------------------------------------------
| Security Testing Routes
|--------------------------------------------------------------------------
|
| These routes are ONLY for security testing with tools like ZAP.
| They temporarily remove authentication and other security measures.
| DO NOT USE IN PRODUCTION!
|
*/

use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TodolistController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Unprotected routes for security testing
Route::prefix('test')->name('test.')->group(function () {
    
    // Vulnerable SQL injection endpoint for testing
    Route::get('/users-vulnerable/{id}', function ($id) {
        // This is intentionally vulnerable for testing
        $user = \DB::select("SELECT * FROM users WHERE id = " . $id);
        return response()->json($user);
    })->name('users.vulnerable');
    
    // Unprotected user endpoints
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // Unprotected todo endpoints
    Route::get('/todolist', [TodolistController::class, 'index'])->name('todolist.index');
    Route::post('/todolist', [TodolistController::class, 'store'])->name('todolist.store');
    Route::patch('/todolist/{id}', [TodolistController::class, 'update'])->name('todolist.update');
    Route::delete('/todolist/{id}', [TodolistController::class, 'destroy'])->name('todolist.destroy');
    
    // File upload vulnerability test
    Route::post('/upload', function (Request $request) {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Intentionally unsafe file upload
            $path = $file->move(public_path('uploads'), $file->getClientOriginalName());
            return response()->json(['path' => $path]);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    })->name('upload.vulnerable');
    
    // XSS vulnerability test
    Route::get('/xss/{input}', function ($input) {
        // Intentionally vulnerable to XSS
        return response("<html><body><h1>Hello " . $input . "</h1></body></html>")
            ->header('Content-Type', 'text/html');
    })->name('xss.vulnerable');
    
    // LDAP injection test endpoint
    Route::post('/ldap-search', function (Request $request) {
        $filter = $request->input('filter', '');
        // Simulate LDAP injection vulnerability
        $ldapFilter = "(cn=" . $filter . ")";
        return response()->json(['ldap_filter' => $ldapFilter]);
    })->name('ldap.vulnerable');
    
    // Command injection test
    Route::get('/ping/{host}', function ($host) {
        // Intentionally vulnerable to command injection
        $output = shell_exec("ping -c 1 " . $host);
        return response()->json(['output' => $output]);
    })->name('ping.vulnerable');
    
    // Information disclosure
    Route::get('/debug-info', function () {
        return response()->json([
            'environment' => app()->environment(),
            'php_version' => PHP_VERSION,
            'server_info' => $_SERVER,
            'database_config' => config('database'),
        ]);
    })->name('debug.info');
    
    // Session manipulation test
    Route::get('/session/{key}/{value}', function ($key, $value) {
        session([$key => $value]);
        return response()->json(['message' => 'Session set', 'key' => $key, 'value' => $value]);
    })->name('session.set');
    
    Route::get('/session/{key}', function ($key) {
        return response()->json(['key' => $key, 'value' => session($key)]);
    })->name('session.get');
});

// Override some existing routes without authentication
Route::get('/dashboard-test', [DashboardController::class, 'index'])->name('dashboard.test');
Route::get('/profile-test', [ProfileController::class, 'edit'])->name('profile.test');
