<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table messages avec TOUS ses champs définitifs.
     *
     * Un message appartient à une conversation et est émis par l'utilisateur ou
     * l'assistant. La suppression de la conversation supprime ses messages en cascade.
     *
     * Relations :
     *   - conversation_id : FK -> conversations, cascadeOnDelete (index auto)
     *   - llm_model_id    : FK nullable -> llm_models, nullOnDelete (index auto).
     *                       Si un modèle est retiré du référentiel, les messages
     *                       sont CONSERVÉS sans référence plutôt que supprimés.
     *
     * Colonnes clés :
     *   - role (ENUM)     : 'user' ou 'assistant'. ENUM plutôt que string :
     *                       contrainte d'intégrité au niveau base, valeurs fermées.
     *                       Pas d'index dessus : 2 valeurs => non sélectif (50%),
     *                       l'optimiseur l'ignorerait au profit d'un full scan.
     *   - content         : longText pour les longues réponses IA
     *   - model           : identifiant brut du modèle envoyé à l'API. Distinct de
     *                       llm_model_id (FK) : trace la chaîne exacte même si le
     *                       référentiel évolue.
     *   - tokens_used     : total de tokens consommés (comptabilité)
     *   - cost_usd        : coût total du message (input + output) calculé côté app.
     *                       DECIMAL(8,6) : jusqu'à 99.999999 USD par message.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('llm_model_id')
                ->nullable()
                ->constrained('llm_models')
                ->nullOnDelete();
            $table->enum('role', ['user', 'assistant']);
            $table->longText('content');
            $table->string('model')->nullable()->comment('Identifiant brut du modèle envoyé à l\'API');
            $table->unsignedInteger('tokens_used')->nullable();
            $table->decimal('cost_usd', 8, 6)->nullable()->default(0);
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
