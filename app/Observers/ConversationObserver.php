<?php

namespace App\Observers;

use App\Models\Conversation;
use App\Models\UserStats;

class ConversationObserver
{
    public function created(Conversation $conversation): void
    {
        $stats = UserStats::firstOrCreate(['user_id' => $conversation->user_id]);
        $stats->increment('total_conversations');
    }

    public function deleted(Conversation $conversation): void
    {
        $stats = UserStats::where('user_id', $conversation->user_id)->first();
        if ($stats) {
            $stats->decrement('total_conversations');
        }
    }

    public function forceDeleted(Conversation $conversation): void
    {
        $stats = UserStats::where('user_id', $conversation->user_id)->first();
        if ($stats) {
            $stats->decrement('total_conversations');
            $messages = $conversation->messages()->get();
            foreach ($messages as $msg) {
                $stats->decrement('total_messages');
                $stats->decrement('total_tokens', $msg->tokens_used ?? 0);

                $now = now();
                $firstDayOfMonth = $now->copy()->startOfMonth();
                if ($msg->created_at >= $firstDayOfMonth) {
                    $stats->decrement('monthly_messages');
                    $stats->decrement('monthly_cost', $msg->cost_usd ?? 0);
                }
            }
        }
    }
}
