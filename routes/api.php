<?php

use App\Http\Controllers\AskController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/ask', [AskController::class, 'store'])->name('ask.store');
    Route::post('/ask/stream', [AskController::class, 'stream'])->name('ask.stream');

    Route::apiResource('conversations', ConversationController::class);
    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/conversations/{conversation}/messages/stream', [MessageController::class, 'streamStore'])->name('messages.stream');
});
