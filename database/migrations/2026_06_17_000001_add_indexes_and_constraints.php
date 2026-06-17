<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Migration consolidée — vide intentionnellement.
 *
 * Historique : cette migration ajoutait un index sur messages.role et une contrainte
 * UNIQUE sur llm_models.model_id.
 *
 * Corrections apportées :
 *   - llm_models.model_id UNIQUE : déplacé dans create_llm_models_table (emplacement correct).
 *   - messages.role INDEX : supprimé. Un index sur une colonne ENUM à deux valeurs
 *     ('user', 'assistant') est contre-productif : le moteur choisit un full scan,
 *     car l'index n'est sélectif qu'à 50 %. L'optimiseur MySQL/PostgreSQL l'ignorera.
 *
 * Ce fichier est conservé pour ne pas briser l'historique des migrations déjà
 * appliquées en production. Ne pas supprimer ce fichier.
 */
return new class extends Migration
{
    public function up(): void
    {
        // No-op : contraintes consolidées dans les migrations de création respectives.
    }

    public function down(): void
    {
        // No-op.
    }
};
