<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Permet aux model events (boot hooks) de s'exécuter pendant le seeding
    // Nécessaire pour que User::boot() crée automatiquement CustomInstruction

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Les modèles LLM sont maintenant insérés par migration (2026_06_11_000001_populate_llm_models_table)

        // Données de test et développement (local uniquement)
        // À ne pas exécuter en production
        $this->call(UserSeeder::class);
        $this->call(ConversationSeeder::class);
        $this->call(MessageSeeder::class);
    }
}
