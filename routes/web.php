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
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

Route::middleware(['auth:sanctum', config('jetstream.auth_session')])->group(function () {
    Route::get('/email/verify', function () {
        return Inertia::render('Auth/VerifyEmail');
    })->name('verification.notice');

    Route::post('/email/pending-email-send', function (Request $request) {
        $user = $request->user();

        // Rate limiting: Minimum 5 minutes entre les demandes
        if ($user->pending_email_sent_at && $user->pending_email_sent_at->addMinutes(5) > now()) {
            return back()->with('error', 'Veuillez attendre 5 minutes avant de renvoyer le lien.');
        }

        if (!is_null($user->pending_email)) {
            $user->update(['pending_email_sent_at' => now()]);
            $user->notify(new \App\Notifications\VerifyEmailChangeNotification($user->pending_email));
        }
        return back()->with('status', 'pending-email-sent');
    })->middleware('throttle:6,1')->name('verification.pending-email-send');
});

Route::get('/email/verify/{id}/{hash}', function (VerifyEmailRequest $request) {
    $request->fulfill();
    return redirect(route('dashboard', absolute: false).'?verified=1');
})->middleware(['throttle:6,1', 'signed'])->name('verification.verify');

Route::get('/email/verify-change/{token}', function (Request $request) {
    $token = $request->route('token');
    $tokenHash = hash('sha256', $token);

    $user = \App\Models\User::where('pending_email_token', $tokenHash)->first();

    if (!$user) {
        if ($request->user()) {
            return redirect(route('dashboard'))->with('error', 'Lien invalide ou expiré.');
        }
        return redirect(route('login'))->with('error', 'Lien invalide ou expiré.');
    }

    // Sécurité: Si utilisateur authentifié, vérifier que c'est SON email
    if ($request->user() && $request->user()->id !== $user->id) {
        return redirect(route('dashboard'))->with('error', 'Ce lien ne correspond pas à votre compte.');
    }

    // Confirmer l'email
    $user->update([
        'email' => $user->pending_email,
        'pending_email' => null,
        'pending_email_sent_at' => null,
        'pending_email_token' => null,
        'email_verified_at' => now(),
    ]);

    // Envoyer notification de confirmation
    $user->notify(new \App\Notifications\VerifyEmailChangeConfirmedNotification());

    // Redirection selon authentification
    if ($request->user()) {
        return redirect(route('dashboard'))->with('status', 'email-change-verified');
    }

    return redirect(route('login'))->with('status', 'email-confirmed-please-login');
})->middleware('throttle:6,1')->name('verification.email-change');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'stats' => Auth::user()->getDashboardStats()
        ]);
    })->name('dashboard');

    Route::get('/chat', [ConversationController::class, 'chat'])->name('chat');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings/instructions', [SettingsController::class, 'update'])->name('settings.instructions.update');

    Route::get('/ask', [AskController::class, 'index'])->name('ask');
    Route::post('/ask', [AskController::class, 'store'])->name('ask.store');
    Route::post('/ask/stream', [AskController::class, 'stream'])->name('ask.stream');

    Route::apiResource('conversations', ConversationController::class);
    Route::get('/conversations/{conversation}/stats', [ConversationController::class, 'stats'])->name('conversations.stats');
    Route::get('/conversations/{conversation}/search', [ConversationController::class, 'search'])->name('conversations.search');
    Route::get('/conversations/{conversation}/export', [ConversationController::class, 'export'])->name('conversations.export');
    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/conversations/{conversation}/messages/stream', [MessageController::class, 'streamStore'])->name('messages.stream');
});
