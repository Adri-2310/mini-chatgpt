<?php

namespace App\Observers;

use App\Models\Message;
use App\Models\UserStats;
use Illuminate\Support\Facades\DB;

class MessageObserver
{
    public function created(Message $message): void
    {
        $userId = $message->conversation->user_id;
        $stats = UserStats::firstOrCreate(['user_id' => $userId]);

        $tokensUsed = $message->tokens_used ?? 0;
        $costUsd = $message->cost_usd ?? 0;
        $now = now();
        $firstDayOfMonth = $now->copy()->startOfMonth();

        DB::transaction(function () use ($stats, $tokensUsed, $costUsd, $now, $firstDayOfMonth, $message) {
            $stats->increment('total_messages');
            $stats->increment('total_tokens', $tokensUsed);

            if ($message->created_at >= $firstDayOfMonth) {
                $stats->increment('monthly_messages');
                $stats->increment('monthly_cost', $costUsd);
            }

            $stats->update(['last_activity_at' => $now]);
        });
    }

    public function deleted(Message $message): void
    {
        $userId = $message->conversation->user_id;
        $stats = UserStats::where('user_id', $userId)->first();

        if (!$stats) return;

        $tokensUsed = $message->tokens_used ?? 0;
        $costUsd = $message->cost_usd ?? 0;
        $now = now();
        $firstDayOfMonth = $now->copy()->startOfMonth();

        DB::transaction(function () use ($stats, $tokensUsed, $costUsd, $message, $firstDayOfMonth) {
            $stats->decrement('total_messages');
            $stats->decrement('total_tokens', $tokensUsed);

            if ($message->created_at >= $firstDayOfMonth) {
                $stats->decrement('monthly_messages');
                $stats->decrement('monthly_cost', $costUsd);
            }
        });
    }
}
