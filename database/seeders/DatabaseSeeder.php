<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder
 *
 * Point d'entrée unique pour le seeding de la base de données.
 * Appelle les seeders dans l'ordre de dépendance strict :
 *
 *   1. UserSeeder        — utilisateurs de test (dépend de rien)
 *   2. ConversationSeeder — conversations de test (dépend de users + llm_models)
 *   3. MessageSeeder     — messages de test (dépend de conversations)
 *   4. CustomInstructionSeeder — instructions personnalisées (dépend de users)
 *
 * IMPORTANT : les modèles LLM (llm_models) sont peuplés DIRECTEMENT par la migration
 * create_llm_models_table, pas par un seeder. Raison : ce sont des données de référence
 * indispensables au fonctionnement de l'app et doivent exister dès la migration.
 *
 * Idempotence globale : chaque seeder gère sa propre idempotence.
 * `php artisan migrate:fresh --seed` peut être relancé sans erreur.
 *
 * ATTENTION : ces données sont réservées aux environnements de développement
 * et de test. Ne pas exécuter en production.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Exécute tous les seeders dans l'ordre de dépendance.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ConversationSeeder::class,
            MessageSeeder::class,
            CustomInstructionSeeder::class,
        ]);
    }
}
