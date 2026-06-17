<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute la relation entre messages et llm_models.
     *
     * Permet de tracer exactement quel modèle LLM a généré chaque message assistant.
     * La FK est nullable (nullOnDelete) : si un modèle est supprimé du référentiel,
     * les messages associés sont conservés sans référence plutôt que supprimés.
     *
     * Note : foreignId()->constrained() crée automatiquement l'index sur llm_model_id.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('llm_model_id')
                ->nullable()
                ->after('conversation_id')
                ->constrained('llm_models')
                ->nullOnDelete();
        });
    }

    /**
     * Supprime la colonne llm_model_id et sa contrainte de clé étrangère.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['llm_model_id']);
            $table->dropColumn('llm_model_id');
        });
    }
};
