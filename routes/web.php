<?php

use App\Http\Controllers\AskController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SettingsController;
use App\Http\Requests\VerifyEmailRequest;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::middleware(['auth:sanctum', config('jetstream.auth_session')])->group(function () {
    Route::get('/email/verify', function () {
        return Inertia::render('Auth/VerifyEmail');
    })->name('verification.notice');
});

Route::get('/email/verify/{id}/{hash}', function (VerifyEmailRequest $request) {
    $request->fulfill();
    return redirect(route('dashboard', absolute: false).'?verified=1');
})->middleware(['throttle:6,1', 'signed'])->name('verification.verify');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/chat', [ConversationController::class, 'chat'])->name('chat');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings/instructions', [SettingsController::class, 'update'])->name('settings.instructions.update');

    Route::get('/ask', [AskController::class, 'index'])->name('ask');
    Route::post('/ask', [AskController::class, 'store'])->name('ask.store');
    Route::post('/ask/stream', [AskController::class, 'stream'])->name('ask.stream');
});
