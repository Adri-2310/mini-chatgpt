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
     * La suppression de l'utilisateur entraine la suppression en cascade de ses conversations.
     * Note : foreignId()->constrained() crée automatiquement l'index sur user_id.
     */
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('model_used')->default('gpt-4o');
            $table->timestamps();
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
