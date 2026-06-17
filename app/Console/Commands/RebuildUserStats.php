<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\User;
use App\Models\UserStats;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RebuildUserStats extends Command
{
    protected $signature = 'stats:rebuild {--user=}';
    protected $description = 'Recalcule les stats de tous les utilisateurs depuis les messages.';

    public function handle()
    {
        $userId = $this->option('user');

        if ($userId) {
            $this->rebuildForUser($userId);
        } else {
            User::all()->each(fn($user) => $this->rebuildForUser($user->id));
            $this->info('✅ Stats recalculées pour tous les utilisateurs.');
        }
    }

    private function rebuildForUser($userId): void
    {
        $user = User::find($userId);
        if (!$user) {
            $this->error("Utilisateur $userId non trouvé.");
            return;
        }

        $now = now();
        $firstDayOfMonth = $now->copy()->startOfMonth();

        $totalCost = Message::whereHas('conversation', fn($q) => $q->where('user_id', $userId))
            ->sum(DB::raw('COALESCE(cost_usd, 0)'));

        $monthlyCost = Message::whereHas('conversation', fn($q) => $q->where('user_id', $userId))
            ->where('created_at', '>=', $firstDayOfMonth)
            ->sum(DB::raw('COALESCE(cost_usd, 0)'));

        $totalMessages = Message::whereHas('conversation', fn($q) => $q->where('user_id', $userId))
            ->count();

        $monthlyMessages = Message::whereHas('conversation', fn($q) => $q->where('user_id', $userId))
            ->where('created_at', '>=', $firstDayOfMonth)
            ->count();

        $totalTokens = Message::whereHas('conversation', fn($q) => $q->where('user_id', $userId))
            ->sum(DB::raw('COALESCE(tokens_used, 0)'));

        $totalConversations = $user->conversations()->count();

        UserStats::updateOrCreate(
            ['user_id' => $userId],
            [
                'total_cost' => $totalCost,
                'monthly_cost' => $monthlyCost,
                'total_messages' => $totalMessages,
                'monthly_messages' => $monthlyMessages,
                'total_tokens' => $totalTokens,
                'total_conversations' => $totalConversations,
                'stats_computed_at' => now(),
            ]
        );

        $this->info("✅ Stats recalculées pour l'utilisateur {$user->name}");
    }
}
