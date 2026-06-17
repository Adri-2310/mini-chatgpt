<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table user_stats.
     *
     * Relation 1-to-1 avec users : agrège les statistiques d'usage et de coût
     * d'un utilisateur (cache de calculs lourds, recalculé périodiquement).
     *
     * Colonnes clés :
     *   - user_id             : UNIQUE — garantit le 1-to-1 (cascadeOnDelete).
     *   - total_messages / total_conversations / total_tokens : compteurs cumulés
     *     (total_tokens en BIG INTEGER : peut dépasser 4 milliards sur le long terme).
     *   - monthly_cost / monthly_messages : agrégats du mois courant (réinitialisés
     *     mensuellement), pour l'affichage de la consommation récente.
     *   - last_activity_at    : dernière activité de l'utilisateur.
     *   - stats_computed_at    : horodatage du dernier recalcul (useCurrent par défaut).
     *
     * NB : il n'y a volontairement PAS de colonne total_cost — le coût total se
     * dérive à la demande depuis messages.cost_usd ; seuls les agrégats mensuels
     * sont matérialisés ici.
     *
     * DECIMAL(10,6) pour monthly_cost : jusqu'à 9999.999999 USD.
     */
    public function up(): void
    {
        Schema::create('user_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            $table->unsignedInteger('total_messages')->default(0);
            $table->unsignedInteger('total_conversations')->default(0);
            $table->unsignedBigInteger('total_tokens')->default(0);

            $table->decimal('monthly_cost', 10, 6)->default(0);
            $table->unsignedInteger('monthly_messages')->default(0);

            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('stats_computed_at')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Supprime la table user_stats.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_stats');
    }
};
