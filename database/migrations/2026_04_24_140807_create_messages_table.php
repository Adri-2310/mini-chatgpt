<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table messages.
     *
     * Un message appartient à une conversation et est émis par l'utilisateur ou l'assistant.
     * La suppression de la conversation entraine la suppression en cascade de ses messages.
     *
     * Colonnes clés :
     *   - role        : 'user' ou 'assistant' — détermine l'émetteur du message
     *   - content     : corps du message (longText pour supporter les longues réponses IA)
     *   - model       : identifiant brut du modèle envoyé à l'API (ex: "openai/gpt-4o-mini")
     *                   Distinct de llm_model_id (FK), ajouté ultérieurement, qui lie au référentiel.
     *   - tokens_used : nombre total de tokens consommés (comptabilisation des coûts)
     *
     * Note : foreignId()->constrained() crée automatiquement l'index sur conversation_id.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['user', 'assistant']);
            $table->longText('content');
            $table->string('model')->nullable()->comment('Identifiant brut du modèle envoyé à l\'API');
            $table->unsignedInteger('tokens_used')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Supprime la table messages.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
