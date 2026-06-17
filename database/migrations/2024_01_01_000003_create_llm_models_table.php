<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée et PEUPLE la table llm_models.
     *
     * Référentiel des modèles LLM disponibles (OpenAI, Google, Anthropic).
     * Aucune dépendance : créée avant messages (qui la référence en FK nullable).
     *
     * Colonnes clés :
     *   - name     UNIQUE : libellé lisible (ex: "GPT-4o mini")
     *   - model_id UNIQUE : identifiant technique envoyé à l'API
     *                       (ex: "openai/gpt-4o-mini"), doit correspondre
     *                       à conversations.model_used
     *   - provider INDEX  : filtrage par fournisseur dans l'interface
     *   - enabled         : masquage d'un modèle sans suppression
     *   - config (JSON)   : paramètres spécifiques au modèle (température, etc.)
     *
     * PEUPLEMENT INITIAL : contrairement à l'usage habituel (seeder), les 3 modèles
     * de référence sont insérés directement ici via DB::table()->insert(). Raison :
     * ce sont des DONNÉES DE RÉFÉRENCE indispensables au fonctionnement de l'app
     * (messages.llm_model_id et conversations.model_used en dépendent). Elles doivent
     * exister dès la migration, y compris en production sans `--seed`.
     */
    public function up(): void
    {
        Schema::create('llm_models', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('provider')->index();
            $table->string('model_id')->unique();
            $table->text('description')->nullable();
            $table->boolean('enabled')->default(true);
            $table->integer('max_tokens')->default(4096);
            $table->json('config')->nullable();
            $table->timestamps();
        });

        // Peuplement des 3 modèles de référence directement dans la migration.
        DB::table('llm_models')->insert([
            [
                'name'        => 'GPT-4o mini',
                'provider'    => 'OpenAI',
                'model_id'    => 'openai/gpt-4o-mini',
                'description' => 'Modèle GPT-4o mini d\'OpenAI, optimisé pour les réponses rapides et économiques.',
                'enabled'     => true,
                'max_tokens'  => 4096,
                'config'      => null,
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
                'config'      => null,
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
                'config'      => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }

    /**
     * Supprime la table llm_models.
     */
    public function down(): void
    {
        Schema::dropIfExists('llm_models');
    }
};
