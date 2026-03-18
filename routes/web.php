<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\TodolistController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['parse.auth', '2fa'])
    ->name('dashboard');

Route::middleware(['parse.auth', '2fa'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // User management routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{id}/send-notification', [UserController::class, 'sendNotification'])->name('users.sendNotification');
    
    // Reports route
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    
    // Todo List routes
    Route::get('/todolist', [TodolistController::class, 'index'])->name('todolist.index');
    Route::post('/todolist', [TodolistController::class, 'store'])->name('todolist.store');
    Route::patch('/todolist/{id}', [TodolistController::class, 'update'])->name('todolist.update');
    Route::delete('/todolist/{id}', [TodolistController::class, 'destroy'])->name('todolist.destroy');
    

    // Step Tracker route
    Route::get('/steptracker', [\App\Http\Controllers\StepTrackerController::class, 'index'])->name('steptracker');
    
    // Messages route
    Route::get('/messages', [\App\Http\Controllers\MessagesController::class, 'index'])->name('messages.index');
    Route::patch('/messages/{id}/mark-as-read', [\App\Http\Controllers\MessagesController::class, 'markAsRead'])->name('messages.markAsRead');
    Route::delete('/messages/{id}', [\App\Http\Controllers\MessagesController::class, 'destroy'])->name('messages.destroy');

    // Food Nutrition route
    Route::get('/foodnutrition', [\App\Http\Controllers\FoodNutritionController::class, 'index'])->name('foodnutrition.index');
    Route::post('/foodnutrition', [\App\Http\Controllers\FoodNutritionController::class, 'store'])->name('foodnutrition.store');
    Route::delete('/foodnutrition/{id}', [\App\Http\Controllers\FoodNutritionController::class, 'destroy'])->name('foodnutrition.destroy');
    Route::patch('/foodnutrition/{id}', [\App\Http\Controllers\FoodNutritionController::class, 'update'])->name('foodnutrition.update');
    // API Testing route
    Route::get('/apitesting', [\App\Http\Controllers\ApiTestingController::class, 'index'])->name('apitesting.index');
    Route::post('/apitesting/predict', [\App\Http\Controllers\ApiTestingController::class, 'predict'])->name('apitesting.predict');

    // Two Factor Authentication route
    Route::get('/two-factor-verify', [\App\Http\Controllers\TwoFactorAuthenticationController::class, 'showVerifyForm'])->name('twofactor.verify.form');
    Route::get('/two-factor-setup', [\App\Http\Controllers\TwoFactorAuthenticationController::class, 'setup'])->name('twofactor.setup');
    Route::post('/two-factor-verify', [\App\Http\Controllers\TwoFactorAuthenticationController::class, 'verify'])->name('twofactor.verify');

    // Settings route
    Route::get('/settings', function () {
        return Inertia::render('Settings');
    })->name('settings');
});

Route::middleware(['parse.auth'])->group(function () {
    // Add route for redisplaying QR code for 2FA (must not require 2fa middleware)
    Route::get('/two-factor-qr', [\App\Http\Controllers\TwoFactorAuthenticationController::class, 'qr'])->name('twofactor.qr');
});

// Temporary route to clear the 2FA secret
Route::get('/clear-2fa-secret', function () {
    $user = session('parse_user');
    if (!$user) {
        return redirect()->route('login')->with('error', 'Please login first');
    }
    
    $objectId = $user['objectId'];
    $back4AppService = app(\App\Services\Back4AppService::class);
    
    // Clear the 2FA secret by setting it to null
    $result = $back4AppService->updateUser($objectId, [
        'google2fa_secret' => null
    ]);
    
    // Clear the session as well
    session()->forget('parse_user');
    
    \Log::info('2FA secret cleared for user', [
        'user_id' => $objectId,
        'result' => $result
    ]);
    
    return redirect()->route('login')->with('success', '2FA secret cleared successfully. Please login again to set up a new 2FA secret.');
})->name('clear.2fa.secret');

require __DIR__.'/auth.php';
