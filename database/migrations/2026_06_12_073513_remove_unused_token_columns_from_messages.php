<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Migration consolidée — vide intentionnellement.
 *
 * Historique : cette migration supprimait les colonnes input_tokens et output_tokens
 * qui avaient été ajoutées par erreur dans 2026_06_12_065315_add_cost_tracking_to_messages.
 * Ces colonnes n'ont jamais été créées dans la version consolidée de cette branche.
 * Ce fichier est conservé pour ne pas briser l'historique des migrations déjà appliquées
 * en production. Ne pas supprimer ce fichier.
 */
return new class extends Migration
{
    public function up(): void
    {
        // No-op : colonnes input_tokens et output_tokens non présentes dans ce schéma.
    }

    public function down(): void
    {
        // No-op.
    }
};
