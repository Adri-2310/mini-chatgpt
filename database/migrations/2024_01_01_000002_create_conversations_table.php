<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table conversations.
     *
     * Une conversation appartient à un utilisateur et contient plusieurs messages.
     * La suppression de l'utilisateur supprime ses conversations en cascade.
     *
     * Soft deletes : SEULE cette table utilise softDeletes(). Une conversation
     * "supprimée" par l'utilisateur conserve deleted_at (corbeille restaurable,
     * historique des messages préservé, protection contre suppression accidentelle).
     * Le modèle Conversation doit utiliser le trait SoftDeletes.
     *
     * Note : foreignId()->constrained() crée automatiquement l'index sur user_id.
     */
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('model_used')->default('openai/gpt-4o-mini');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Supprime la table conversations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
