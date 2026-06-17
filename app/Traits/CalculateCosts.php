<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait CalculateCosts
{
    /**
     * Calculer le coût basé sur le nombre total de tokens
     * Utilise la tarification depuis config/ai_models.php
     * Formule: (tokens / 1M) * ((input_price + output_price) / 2)
     */
    public function calculateCostByTokens(string $model, int $tokensUsed): float
    {
        $pricing = config('ai_models.pricing')[$model] ?? null;

        if (!$pricing) {
            return 0;
        }

        // Calcul du coût moyen (input + output) / 2
        $avgCostPer1M = ($pricing['input'] + $pricing['output']) / 2;

        return round(($tokensUsed / 1_000_000) * $avgCostPer1M, 6);
    }

    /**
     * Obtenir les stats simples d'une conversation
     * Total: messages, tokens, coût
     */
    public function getConversationStats($conversation)
    {
        $allMessages = $conversation->messages()->orderBy('created_at')->get();
        $assistantMessages = $conversation->messages()->where('role', 'assistant')->get();

        $totalTokens = $allMessages->sum('tokens_used') ?? 0;
        $totalCost = 0;

        // Calculer le coût pour chaque message assistant
        foreach ($assistantMessages as $msg) {
            if ($msg->tokens_used) {
                $cost = $this->calculateCostByTokens($msg->model ?? $conversation->model_used, $msg->tokens_used);
                $totalCost += $cost;
            }
        }

        return [
            'total_messages' => $allMessages->count(),
            'assistant_messages' => $assistantMessages->count(),
            'total_tokens' => $totalTokens,
            'total_cost_usd' => round($totalCost, 6),
        ];
    }
}
