<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait CalculateCosts
{
    /**
     * Calculer le coût basé sur le nombre total de tokens
     * Formule: (tokens_used / 1M) * price_per_1M_tokens
     */
    public function calculateCostByTokens(string $model, int $tokensUsed): float
    {
        // Coût moyen par million de tokens (prix approximatif)
        $avgCostPer1M = [
            'openai/gpt-4o-mini' => 0.75,          // ~$0,75 pour 1M tokens (moyenne entrée+sortie)
            'google/gemini-2.5-flash' => 0.20,     // ~$0,20 pour 1M tokens
            'anthropic/claude-3.5-haiku' => 2.40,  // ~$2,40 pour 1M tokens
            'anthropic/claude-3.5-sonnet' => 3.00, // ~$3,00 pour 1M tokens
        ];

        $costPer1M = $avgCostPer1M[$model] ?? 1.50; // Fallback si modèle inconnu

        return round(($tokensUsed / 1_000_000) * $costPer1M, 6);
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
