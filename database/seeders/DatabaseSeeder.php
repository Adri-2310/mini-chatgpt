<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder
 *
 * Point d'entrée unique pour le seeding de la base de données.
 * Appelle les seeders dans l'ordre de dépendance strict :
 *
 *   1. LlmModelSeeder   — référentiel des modèles LLM (aucune dépendance)
 *   2. UserSeeder        — utilisateurs de test (dépend de rien)
 *   3. ConversationSeeder — conversations de test (dépend de users + llm_models)
 *   4. MessageSeeder     — messages de test (dépend de conversations)
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
