<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute la suppression douce (soft delete) à la table conversations.
     *
     * Avec softDeletes(), une conversation "supprimée" par l'utilisateur n'est pas
     * effacée physiquement : deleted_at est horodaté. Cela permet :
     *   - une corbeille restaurable,
     *   - la conservation de l'historique des messages associés,
     *   - une protection contre les suppressions accidentelles.
     *
     * Le modèle Conversation doit utiliser le trait SoftDeletes.
     */
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Supprime la colonne deleted_at de la table conversations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
