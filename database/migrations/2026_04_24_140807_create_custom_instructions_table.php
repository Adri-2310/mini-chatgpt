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
     * une instruction système envoyée en tête de chaque conversation IA.
     *
     * Colonnes clés :
     *   - user_id      : UNIQUE — garantit la contrainte 1-to-1 au niveau base de données
     *   - instructions : nullable — un utilisateur peut ne pas avoir d'instruction définie
     *   - enabled      : permet de désactiver temporairement l'instruction sans la supprimer
     *
     * La suppression de l'utilisateur entraine la suppression en cascade de son instruction.
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
