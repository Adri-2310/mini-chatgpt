<?php

use App\Http\Controllers\AskController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SettingsController;
use Illuminate\Foundation\Application;
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

    Route::apiResource('conversations', ConversationController::class);
    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store'])->name('messages.store');
});
