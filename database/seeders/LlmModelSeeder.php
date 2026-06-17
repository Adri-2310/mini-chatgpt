<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * LlmModelSeeder
 *
 * Peuple la table `llm_models` avec le référentiel des modèles LLM disponibles.
 *
 * Ce seeder est le SEUL endroit où les données de référence des modèles sont gérées.
 * La migration 2026_06_11_000001_populate_llm_models_table est intentionnellement vide
 * (no-op) — les données métier n'ont pas leur place dans les migrations.
 *
 * Idempotence : `insertOrIgnore` s'appuie sur la contrainte UNIQUE de `model_id`
 * pour éviter tout doublon en cas de ré-exécution.
 *
 * Exécution standalone : php artisan db:seed --class=LlmModelSeeder
 *
 * Champs :
 *   - name      : libellé affiché à l'utilisateur
 *   - provider  : fournisseur du modèle (OpenAI, Anthropic, Google…)
 *   - model_id  : identifiant technique envoyé à l'API (doit correspondre
 *                 à la valeur stockée dans conversations.model_used)
 *   - enabled   : false = modèle masqué dans l'interface sans suppression
 *   - max_tokens: limite de génération par défaut recommandée par le fournisseur
 */
class LlmModelSeeder extends Seeder
{
    /**
     * Insère les modèles LLM de référence.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('llm_models')->insertOrIgnore([
            [
                'name'        => 'GPT-4o mini',
                'provider'    => 'OpenAI',
                'model_id'    => 'openai/gpt-4o-mini',
                'description' => 'Modèle GPT-4o mini d\'OpenAI, optimisé pour les réponses rapides et économiques.',
                'enabled'     => true,
                'max_tokens'  => 4096,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Gemini 2.5 Flash',
                'provider'    => 'Google',
                'model_id'    => 'google/gemini-2.5-flash',
                'description' => 'Modèle Gemini 2.5 Flash de Google, optimisé pour la vitesse et le volume.',
                'enabled'     => true,
                'max_tokens'  => 8000,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Claude 3.5 Haiku',
                'provider'    => 'Anthropic',
                'model_id'    => 'anthropic/claude-3.5-haiku',
                'description' => 'Modèle Claude 3.5 Haiku d\'Anthropic, compact et efficace pour les tâches courantes.',
                'enabled'     => true,
                'max_tokens'  => 8192,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
