<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute le suivi des coûts API à la table messages.
     *
     * Seule la colonne cost_usd est conservée : elle cumule le coût total
     * du message (input + output) calculé côté application avant insertion.
     * Les colonnes granulaires input_tokens/output_tokens ont été supprimées
     * car redondantes avec tokens_used et inutilisées en pratique.
     *
     * Précision : DECIMAL(8,6) supporte jusqu'à 99.999999 USD par message.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->decimal('cost_usd', 8, 6)->nullable()->default(0)->after('tokens_used');
        });
    }

    /**
     * Supprime la colonne cost_usd de la table messages.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('cost_usd');
        });
    }
};
