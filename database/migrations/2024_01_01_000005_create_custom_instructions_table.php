<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table custom_instructions.
     *
     * Relation 1-to-1 avec users : chaque utilisateur peut définir au maximum
     * une instruction système, envoyée en tête de chaque conversation IA.
     *
     * Colonnes clés :
     *   - user_id      : UNIQUE — garantit le 1-to-1 au niveau base de données
     *                    (cascadeOnDelete : supprimée avec l'utilisateur).
     *   - instructions : nullable — un utilisateur peut n'avoir aucune instruction.
     *   - enabled      : désactivation temporaire sans suppression.
     */
    public function up(): void
    {
        Schema::create('custom_instructions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->longText('instructions')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Supprime la table custom_instructions.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_instructions');
    }
};
