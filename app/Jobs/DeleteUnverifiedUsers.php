<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeleteUnverifiedUsers implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $hoursThreshold = 24;

        User::where('email_verified_at', null)
            ->where('created_at', '<=', now()->subHours($hoursThreshold))
            ->delete();
    }
}
