<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CleanupPendingEmails implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        User::where('pending_email', '!=', null)
            ->where('pending_email_sent_at', '<', now()->subDays(7))
            ->chunk(500, function ($users) {
                $users->each(function ($user) {
                    $user->update([
                        'pending_email' => null,
                        'pending_email_sent_at' => null,
                        'pending_email_token' => null,
                    ]);
                });
            });
    }
}
