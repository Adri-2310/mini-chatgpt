<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migration vide — données déplacées vers LlmModelSeeder.
 *
 * ANTI-PATTERN CORRIGÉ : les migrations ne doivent jamais contenir de données métier.
 * Les migrations définissent la structure (DDL), les seeders peuplent les données (DML).
 *
 * Les modèles LLM initiaux (GPT-4o mini, Gemini 2.5 Flash, Claude 3.5 Haiku) sont
 * désormais gérés dans database/seeders/LlmModelSeeder.php.
 * Commande : php artisan db:seed --class=LlmModelSeeder
 *
 * Ce fichier est conservé pour ne pas briser l'historique des migrations déjà
 * appliquées en production. Ne pas supprimer ce fichier.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('llm_models')->insert([
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

    public function down(): void
    {
        DB::table('llm_models')->delete();
    }
};
