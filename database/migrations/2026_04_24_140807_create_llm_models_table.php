<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table llm_models.
     *
     * Référentiel des modèles LLM disponibles dans l'application (OpenAI, Anthropic, Google, etc.).
     * Peuplée via LlmModelSeeder (php artisan db:seed --class=LlmModelSeeder).
     *
     * Colonnes clés :
     *   - name     : libellé lisible unique (ex: "GPT-4o mini")
     *   - model_id : identifiant technique unique envoyé à l'API (ex: "openai/gpt-4o-mini")
     *   - enabled  : permet de désactiver un modèle sans le supprimer
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
    }

    /**
     * Supprime la table llm_models.
     */
    public function down(): void
    {
        Schema::dropIfExists('llm_models');
    }
};
